<?php
declare(strict_types=1);

namespace APP\Repositories;
use PDO;
use Throwable;
use InvalidArgumentException;
use DomainException;

class ReservaRepositorio
{
    public function __construct(private PDO $PDO)
    {
    }

    public function verificarCliente(int $idDoUsuario): bool
    {
        $sql = "SELECT 1 FROM clientes WHERE idDoUsuario = :idDoUsuario";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["idDoUsuario" => $idDoUsuario]);
        return $stmt->fetchColumn() !== false;
    }

    public function criarReserva(int $idDoCliente, array $itens): array
    {
        $this->PDO->beginTransaction();
        try {
            $produtos = [];
            foreach ($itens as $item) {
                $sql = "SELECT id, nome, precoDeVenda, stockFisico, stockReservado FROM produtos WHERE id = :id AND activo = 1 FOR UPDATE";
                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "id" => $item["idDoProduto"]
                ]);
                $produto = $stmt->fetch();
                if (!$produto) {
                    throw new DomainException(
                        "Um dos produtos não está disponível",
                        409
                    );
                }
                $disponivel = (int) $produto["stockFisico"] - (int) $produto["stockReservado"];

                if ($item["quantidade"] > $disponivel) {
                    throw new DomainException(
                        "Stock insuficiente para: " . $produto["nome"],
                        409
                    );
                }
                $produto["quantidade"] = $item["quantidade"];
                $produtos[] = $produto;
            }
            $valorTotal = 0;
            foreach ($produtos as $produto) {
                $valorTotal += (float) $produto["precoDeVenda"] * $produto["quantidade"];
            }
            $valorTotal = round($valorTotal, 2);
            $sql = "INSERT INTO reservas
                        (idDoCliente, estado, valorTotal)
                    VALUES
                        (:idDoCliente, 'PENDENTE', :valorTotal)";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute([
                "idDoCliente" => $idDoCliente,
                "valorTotal" => $valorTotal
            ]);
            $idDaReserva = (int) $this->PDO->lastInsertId();

            foreach ($produtos as $produto) {
                $sql = "INSERT INTO itensEmReserva
                            (idDaReserva, idDoProduto,
                             quantidade, precoUnitario)
                        VALUES
                            (:idDaReserva, :idDoProduto,
                             :quantidade, :precoUnitario)";

                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "idDaReserva" => $idDaReserva,
                    "idDoProduto" => $produto["id"],
                    "quantidade" => $produto["quantidade"],
                    "precoUnitario" => $produto["precoDeVenda"]
                ]);

                $sql = "UPDATE produtos
                        SET stockReservado = stockReservado + :quantidade WHERE id = :idDoProduto";

                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "quantidade" => $produto["quantidade"],
                    "idDoProduto" => $produto["id"]
                ]);

                $sql = "INSERT INTO movimentosDeStock
                            (idDoProduto, idDoUsuarioResponsavel,
                             tipoDeMovimento, quantidade, observacao)
                        VALUES
                            (:idDoProduto, :idDoUsuario,
                             'RESERVA', :quantidade, :observacao)";

                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "idDoProduto" => $produto["id"],
                    "idDoUsuario" => $idDoCliente,
                    "quantidade" => $produto["quantidade"],
                    "observacao" => "Reserva nº " . $idDaReserva
                ]);
            }
            $this->PDO->commit();
            return $this->procurarReservaPorId(
                $idDaReserva,
                $idDoCliente
            );

        } catch (Throwable $erro) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }
            throw $erro;
        }
    }

    public function listarReservasDoCliente(int $idDoCliente): array
    {
        $sql = "SELECT id, dataDaReserva, estado, valorTotal
                FROM reservas
                WHERE idDoCliente = :idDoCliente
                ORDER BY id DESC";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["idDoCliente" => $idDoCliente]);

        return $stmt->fetchAll();
    }

    public function procurarReservaPorId(
        int $idDaReserva,
        int $idDoCliente
    ): ?array {

        $sql = "SELECT id, idDoCliente, dataDaReserva,
                       estado, valorTotal
                FROM reservas
                WHERE id = :idDaReserva
                  AND idDoCliente = :idDoCliente";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute([
            "idDaReserva" => $idDaReserva,
            "idDoCliente" => $idDoCliente
        ]);

        $reserva = $stmt->fetch();

        if (!$reserva) {
            return null;
        }

        $sql = "SELECT i.idDoProduto, p.nome,
                       i.quantidade, i.precoUnitario,
                       i.quantidade * i.precoUnitario AS subtotal
                FROM itensEmReserva i
                INNER JOIN produtos p ON p.id = i.idDoProduto
                WHERE i.idDaReserva = :idDaReserva";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["idDaReserva" => $idDaReserva]);
        $reserva["itens"] = $stmt->fetchAll();
        return $reserva;
    }

    public function cancelarReserva(
        int $idDaReserva,
        int $idDoCliente
    ): bool {
        $this->PDO->beginTransaction();
        try {
            // Bloquear a reserva para evitar dois cancelamentos.
            $sql = "SELECT id, estado
                    FROM reservas
                    WHERE id = :idDaReserva
                      AND idDoCliente = :idDoCliente
                    FOR UPDATE";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute([
                "idDaReserva" => $idDaReserva,
                "idDoCliente" => $idDoCliente
            ]);

            $reserva = $stmt->fetch();

            if (!$reserva) {
                $this->PDO->rollBack();
                return false;
            }

            if ($reserva["estado"] !== "PENDENTE") {
                throw new DomainException(
                    "Esta reserva já não pode ser cancelada.",
                    409
                );
            }

            $sql = "SELECT COUNT(*)
        FROM vendas
        WHERE idDaReserva = :idDaReserva";
            $stmt = $this->PDO->prepare($sql);
            $stmt->execute([
                "idDaReserva" => $idDaReserva
            ]);
            if ((int) $stmt->fetchColumn() > 0) {
                throw new DomainException(
                    "A reserva já possui vendas realizadas e não pode ser cancelada integralmente.",
                    409
                );
            }
            $sql = "SELECT idDoProduto, quantidade
                    FROM itensEmReserva
                    WHERE idDaReserva = :idDaReserva
                    ORDER BY idDoProduto";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute(["idDaReserva" => $idDaReserva]);
            $itens = $stmt->fetchAll();
            foreach ($itens as $item) {
                $sql = "UPDATE produtos SET stockReservado = stockReservado - :quantidade WHERE id = :idDoProduto AND stockReservado >= :quantidadeMinima";

                $stmt = $this->PDO->prepare($sql);

                $stmt->execute([
                    "quantidade" => $item["quantidade"],
                    "quantidadeMinima" => $item["quantidade"],
                    "idDoProduto" => $item["idDoProduto"]
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new DomainException(
                        "Não foi possível libertar o stock.",
                        409
                    );
                }

                $sql = "INSERT INTO movimentosDeStock
                            (idDoProduto, idDoUsuarioResponsavel,
                             tipoDeMovimento, quantidade, observacao)
                        VALUES
                            (:idDoProduto, :idDoUsuario,
                             'LIBERTACAO_DE_RESERVA',
                             :quantidade, :observacao)";

                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "idDoProduto" => $item["idDoProduto"],
                    "idDoUsuario" => $idDoCliente,
                    "quantidade" => $item["quantidade"],
                    "observacao" => "Cancelamento da reserva nº "
                        . $idDaReserva
                ]);
            }
            $sql = "UPDATE reservas
                    SET estado = 'CANCELADA'
                    WHERE id = :idDaReserva";
            $stmt = $this->PDO->prepare($sql);
            $stmt->execute(["idDaReserva" => $idDaReserva]);

            $this->PDO->commit();
            return true;
        } catch (Throwable $erro) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }

            throw $erro;
        }
    }
}
