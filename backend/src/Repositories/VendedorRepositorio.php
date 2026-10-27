<?php
declare(strict_types=1);

namespace APP\Repositories;

use PDO;
use Throwable;
use DomainException;

class VendedorRepositorio
{
    public function __construct(private PDO $PDO)
    {
    }

    public function obterPerfil(int $idDoUsuario): ?array
    {
        $sql = "SELECT
                    u.id, u.nome, u.sobrenome,
                    u.email, u.telefone,
                    u.enderecoResidencial,
                    v.numeroDoCartao,
                    v.idDoDepartamento,
                    d.nome AS departamento
                FROM vendedores v
                INNER JOIN usuarios u ON u.id = v.idDoUsuario
                INNER JOIN departamentos d ON d.id = v.idDoDepartamento
                WHERE v.idDoUsuario = :id
                  AND u.papel = 'VENDEDOR'
                  AND u.activo = 1";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["id" => $idDoUsuario]);
        return $stmt->fetch() ?: null;
    }
    public function listarInventario(int $idDoDepartamento): array
    {
        $sql = "SELECT
                    p.id, p.nome, p.marca,
                    p.precoDeVenda,
                    p.stockFisico,
                    p.stockReservado,
                    (p.stockFisico - p.stockReservado)
                        AS stockDisponivel,
                    c.nome AS categoria
                FROM produtos p
                INNER JOIN categorias c ON c.id = p.idDaCategoria
                WHERE p.idDoDepartamento = :departamento
                  AND p.activo = 1
                ORDER BY p.nome";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["departamento" => $idDoDepartamento]);
        return $stmt->fetchAll();
    }
    public function listarVendas(int $idDoDepartamento): array
    {
        $sql = "SELECT
                    v.id, v.dataDaVenda,
                    v.valorTotal, v.idDaReserva,
                    CONCAT(u.nome, ' ', u.sobrenome)
                        AS cliente,
                    CONCAT(uv.nome, ' ', uv.sobrenome)
                        AS vendedor
                FROM vendas v
                LEFT JOIN usuarios u ON u.id = v.idDoCliente
                INNER JOIN usuarios uv ON uv.id = v.idDoVendedor
                WHERE v.idDoDepartamento = :departamento
                ORDER BY v.dataDaVenda DESC, v.id DESC";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["departamento" => $idDoDepartamento]);
        return $stmt->fetchAll();
    }
    public function listarReservasDoDepartamento(
        int $idDoDepartamento
    ): array {

        $sql = "SELECT
            r.id,
            r.dataDaReserva,
            r.estado,
            CONCAT(u.nome, ' ', u.sobrenome) AS cliente,
            SUM(
                (i.quantidade - COALESCE((
                    SELECT SUM(iv.quantidade)
                    FROM vendas v
                    INNER JOIN itensDeVenda iv
                        ON iv.idDaVenda = v.id
                    WHERE v.idDaReserva = r.id
                      AND iv.idDoProduto = i.idDoProduto
                ), 0)) * i.precoUnitario
            ) AS valorDoDepartamento
        FROM reservas r
        INNER JOIN usuarios u ON u.id = r.idDoCliente
        INNER JOIN itensEmReserva i ON i.idDaReserva = r.id
        INNER JOIN produtos p ON p.id = i.idDoProduto
        WHERE p.idDoDepartamento = :departamento
          AND r.estado IN ('PENDENTE', 'CONFIRMADA')
        GROUP BY
            r.id, r.dataDaReserva, r.estado,
            u.nome, u.sobrenome
        HAVING valorDoDepartamento > 0
        ORDER BY r.dataDaReserva DESC";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["departamento" => $idDoDepartamento]);
        return $stmt->fetchAll();
    }
    public function obterPainel(int $idDoDepartamento): array
    {
        $sql = "SELECT
                    COUNT(*) AS quantidadeDeVendas,
                    COALESCE(SUM(valorTotal), 0) AS totalVendido
                FROM vendas
                WHERE idDoDepartamento = :departamento
                  AND DATE(dataDaVenda) = CURDATE()";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["departamento" => $idDoDepartamento]);
        $vendasHoje = $stmt->fetch();
        $sql = "SELECT
                    COUNT(*) AS produtosDisponiveis
                FROM produtos
                WHERE idDoDepartamento = :departamento
                  AND activo = 1
                  AND stockFisico > stockReservado";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["departamento" => $idDoDepartamento]);
        $disponiveis = $stmt->fetch();
        $sql = "SELECT
                    v.id, v.dataDaVenda,
                    p.nome AS produto,
                    i.quantidade,
                    (i.quantidade * i.precoUnitario) AS valor
                FROM vendas v
                INNER JOIN itensDeVenda i ON i.idDaVenda = v.id
                INNER JOIN produtos p ON p.id = i.idDoProduto
                WHERE v.idDoDepartamento = :departamento
                ORDER BY v.dataDaVenda DESC, v.id DESC
                LIMIT 5";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["departamento" => $idDoDepartamento]);
        $recentes = $stmt->fetchAll();
        $sql = "SELECT
            p.id, p.nome, p.precoDeVenda,
            (
                SELECT img.caminhoDaImagem
                FROM imagensDeProdutos img
                WHERE img.idDoProduto = p.id
                ORDER BY img.imagemPrincipal DESC,
                         img.ordemDeExibicao ASC
                LIMIT 1
            ) AS imagemPrincipal,
            (
                SELECT COALESCE(SUM(iv.quantidade), 0)
                FROM itensDeVenda iv
                INNER JOIN vendas v ON v.id = iv.idDaVenda
                WHERE iv.idDoProduto = p.id
                  AND v.idDoDepartamento = :departamentoVendas
            ) AS quantidadeVendida
        FROM produtos p
        WHERE p.idDoDepartamento = :departamento
          AND p.activo = 1
        ORDER BY quantidadeVendida DESC, p.id DESC
        LIMIT 3";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute([
            "departamento" => $idDoDepartamento,
            "departamentoVendas" => $idDoDepartamento
        ]);
        return [
            "vendasHoje" => $vendasHoje,
            "produtosDisponiveis" =>
                (int) $disponiveis["produtosDisponiveis"],
            "vendasRecentes" => $recentes,
            "produtosDestaque" => $stmt->fetchAll()
        ];
    }
    public function registarVendaDirecta(
        int $idDoVendedor,
        int $idDoDepartamento,
        array $itens,
        ?int $idDoCliente
    ): array {
        $this->PDO->beginTransaction();

        try {
            if ($idDoCliente !== null) {
                $stmt = $this->PDO->prepare(
                    "SELECT 1 FROM clientes WHERE idDoUsuario = :id"
                );
                $stmt->execute(["id" => $idDoCliente]);

                if (!$stmt->fetchColumn()) {
                    throw new DomainException(
                        "O cliente indicado não existe.",
                        409
                    );
                }
            }

            $produtos = [];
            $total = 0;

            foreach ($itens as $item) {
                $sql = "SELECT id, nome, precoDeVenda,
                               stockFisico, stockReservado
                        FROM produtos
                        WHERE id = :id
                          AND idDoDepartamento = :departamento
                          AND activo = 1
                        FOR UPDATE";

                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "id" => $item["idDoProduto"],
                    "departamento" => $idDoDepartamento
                ]);

                $produto = $stmt->fetch();

                if (!$produto) {
                    throw new DomainException(
                        "Produto inexistente ou de outro departamento.",
                        409
                    );
                }

                $disponivel = (int) $produto["stockFisico"] - (int) $produto["stockReservado"];

                if ($disponivel < $item["quantidade"]) {
                    throw new DomainException(
                        "Stock insuficiente para " . $produto["nome"],
                        409
                    );
                }

                $produto["quantidade"] = $item["quantidade"];
                $produtos[] = $produto;

                $total += (float) $produto["precoDeVenda"]
                    * $item["quantidade"];
            }

            $sql = "INSERT INTO vendas
                        (idDoVendedor, idDoDepartamento,
                         idDoCliente, valorTotal)
                    VALUES
                        (:vendedor, :departamento, :cliente, :total)";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute([
                "vendedor" => $idDoVendedor,
                "departamento" => $idDoDepartamento,
                "cliente" => $idDoCliente,
                "total" => round($total, 2)
            ]);

            $idDaVenda = (int) $this->PDO->lastInsertId();

            foreach ($produtos as $produto) {
                $stmt = $this->PDO->prepare(
                    "INSERT INTO itensDeVenda
                        (idDaVenda, idDoProduto, quantidade, precoUnitario)
                     VALUES (:venda, :produto, :quantidade, :preco)"
                );

                $stmt->execute([
                    "venda" => $idDaVenda,
                    "produto" => $produto["id"],
                    "quantidade" => $produto["quantidade"],
                    "preco" => $produto["precoDeVenda"]
                ]);

                $stmt = $this->PDO->prepare(
                    "UPDATE produtos
                     SET stockFisico = stockFisico - :quantidade
                     WHERE id = :id"
                );

                $stmt->execute([
                    "quantidade" => $produto["quantidade"],
                    "id" => $produto["id"]
                ]);
                $this->registarMovimento(
                    (int) $produto["id"],
                    $idDoVendedor,
                    (int) $produto["quantidade"],
                    "Venda directa nº " . $idDaVenda
                );
            }
            $this->PDO->commit();
            return [
                "id" => $idDaVenda,
                "valorTotal" => round($total, 2)
            ];
        } catch (Throwable $erro) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }
            throw $erro;
        }
    }
    public function finalizarReserva(
        int $idDaReserva,
        int $idDoVendedor,
        int $idDoDepartamento
    ): array {
        $this->PDO->beginTransaction();
        try {
            $stmt = $this->PDO->prepare(
                "SELECT id, idDoCliente, estado
                 FROM reservas
                 WHERE id = :id
                 FOR UPDATE"
            );
            $stmt->execute(["id" => $idDaReserva]);
            $reserva = $stmt->fetch();

            if (
                !$reserva ||
                !in_array(
                    $reserva["estado"],
                    ["PENDENTE", "CONFIRMADA"],
                    true
                )
            ) {
                throw new DomainException(
                    "Esta reserva não pode ser finalizada.",
                    409
                );
            }
            $sql = "SELECT
                        i.idDoProduto, i.quantidade,
                        i.precoUnitario, p.nome
                    FROM itensEmReserva i
                    INNER JOIN produtos p ON p.id = i.idDoProduto
                    WHERE i.idDaReserva = :reserva
                      AND p.idDoDepartamento = :departamento
                    ORDER BY i.idDoProduto";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute([
                "reserva" => $idDaReserva,
                "departamento" => $idDoDepartamento
            ]);
            $itens = $stmt->fetchAll();

            if (!$itens) {
                throw new DomainException(
                    "A reserva não possui itens deste departamento.",
                    409
                );
            }
            $produtosPendentes = [];
            $total = 0;
            foreach ($itens as $item) {
                $sql = "SELECT
                            COALESCE(SUM(iv.quantidade), 0)
                                AS quantidadeVendida
                        FROM vendas v
                        INNER JOIN itensDeVenda iv
                            ON iv.idDaVenda = v.id
                        WHERE v.idDaReserva = :reserva
                          AND iv.idDoProduto = :produto";

                $stmt = $this->PDO->prepare($sql);
                $stmt->execute([
                    "reserva" => $idDaReserva,
                    "produto" => $item["idDoProduto"]
                ]);

                $vendida = (int) $stmt->fetchColumn();
                $pendente = (int) $item["quantidade"] - $vendida;

                if ($pendente < 0) {
                    throw new DomainException(
                        "Inconsistência nas quantidades da reserva.",
                        409
                    );
                }

                if ($pendente === 0) {
                    continue;
                }

                $item["quantidadePendente"] = $pendente;
                $produtosPendentes[] = $item;
                $total += $pendente * (float) $item["precoUnitario"];
            }

            if (!$produtosPendentes) {
                throw new DomainException(
                    "Os itens deste departamento já foram vendidos.",
                    409
                );
            }

            foreach ($produtosPendentes as $item) {
                $stmt = $this->PDO->prepare(
                    "SELECT stockFisico, stockReservado
                     FROM produtos
                     WHERE id = :id FOR UPDATE"
                );
                $stmt->execute(["id" => $item["idDoProduto"]]);
                $stock = $stmt->fetch();

                if (
                    !$stock ||
                    (int) $stock["stockFisico"]
                    < $item["quantidadePendente"] ||
                    (int) $stock["stockReservado"]
                    < $item["quantidadePendente"]
                ) {
                    throw new DomainException(
                        "Stock reservado inconsistente.",
                        409
                    );
                }
            }

            $stmt = $this->PDO->prepare(
                "INSERT INTO vendas
                    (idDoVendedor, idDoDepartamento,
                     idDoCliente, idDaReserva, valorTotal)
                 VALUES
                    (:vendedor, :departamento,
                     :cliente, :reserva, :total)"
            );

            $stmt->execute([
                "vendedor" => $idDoVendedor,
                "departamento" => $idDoDepartamento,
                "cliente" => $reserva["idDoCliente"],
                "reserva" => $idDaReserva,
                "total" => round($total, 2)
            ]);

            $idDaVenda = (int) $this->PDO->lastInsertId();

            foreach ($produtosPendentes as $item) {
                $stmt = $this->PDO->prepare(
                    "INSERT INTO itensDeVenda
                        (idDaVenda, idDoProduto, quantidade, precoUnitario)
                     VALUES (:venda, :produto, :quantidade, :preco)"
                );

                $stmt->execute([
                    "venda" => $idDaVenda,
                    "produto" => $item["idDoProduto"],
                    "quantidade" => $item["quantidadePendente"],
                    "preco" => $item["precoUnitario"]
                ]);

                $stmt = $this->PDO->prepare(
                    "UPDATE produtos
                     SET stockFisico = stockFisico - :fisico,
                         stockReservado = stockReservado - :reservado
                     WHERE id = :id"
                );

                $stmt->execute([
                    "fisico" => $item["quantidadePendente"],
                    "reservado" => $item["quantidadePendente"],
                    "id" => $item["idDoProduto"]
                ]);

                $this->registarMovimento(
                    (int) $item["idDoProduto"],
                    $idDoVendedor,
                    (int) $item["quantidadePendente"],
                    "Levantamento da reserva nº " . $idDaReserva
                );
            }

            $sql = "SELECT
                        COALESCE(SUM(i.quantidade), 0)
                            AS totalReservado
                    FROM itensEmReserva i
                    WHERE i.idDaReserva = :reserva";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute(["reserva" => $idDaReserva]);
            $totalReservado = (int) $stmt->fetchColumn();

            $sql = "SELECT
                        COALESCE(SUM(i.quantidade), 0)
                            AS totalVendido
                    FROM vendas v
                    INNER JOIN itensDeVenda i ON i.idDaVenda = v.id
                    WHERE v.idDaReserva = :reserva";

            $stmt = $this->PDO->prepare($sql);
            $stmt->execute(["reserva" => $idDaReserva]);
            $totalVendido = (int) $stmt->fetchColumn();

            if ($totalVendido > $totalReservado) {
                throw new DomainException(
                    "Quantidade vendida superior à reservada.",
                    409
                );
            }

            if ($totalVendido === $totalReservado) {
                $stmt = $this->PDO->prepare(
                    "UPDATE reservas
                     SET estado = 'CONCLUIDA'
                     WHERE id = :id"
                );
                $stmt->execute(["id" => $idDaReserva]);
            }

            $this->PDO->commit();

            return [
                "idDaVenda" => $idDaVenda,
                "idDaReserva" => $idDaReserva,
                "valorTotal" => round($total, 2),
                "estadoDaReserva" =>
                    $totalVendido === $totalReservado
                    ? "CONCLUIDA"
                    : $reserva["estado"]
            ];

        } catch (Throwable $erro) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }
            throw $erro;
        }
    }
    private function registarMovimento(
        int $idDoProduto,
        int $idDoVendedor,
        int $quantidade,
        string $observacao
    ): void {
        $sql = "INSERT INTO movimentosDeStock
                    (idDoProduto, idDoUsuarioResponsavel,
                     tipoDeMovimento, quantidade, observacao)
                VALUES
                    (:produto, :vendedor,
                     'SAIDA_POR_VENDA', :quantidade, :observacao)";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute([
            "produto" => $idDoProduto,
            "vendedor" => $idDoVendedor,
            "quantidade" => $quantidade,
            "observacao" => $observacao
        ]);
    }
}
