<?php declare(strict_types=1);
namespace APP\Repositories;

use PDO;
use DomainException;
use InvalidArgumentException;

class SolicitacaoVendedorRepositorio {
    public function __construct(private PDO $PDO) {}

    public function criar(int $idDoUsuario, string $numeroDoCartao, int $idDoDepartamento): int {
        $this->PDO->beginTransaction();
        try {
            $stmt = $this->PDO->prepare('SELECT papel, activo FROM usuarios WHERE id = ? FOR UPDATE');
            $stmt->execute([$idDoUsuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$usuario || !$usuario['activo']) throw new DomainException('Utilizador inactivo.', 403);
            if ($usuario['papel'] !== 'CLIENTE') throw new DomainException('Apenas clientes podem enviar esta candidatura.', 403);

            $stmt = $this->PDO->prepare('SELECT id FROM departamentos WHERE id = ? AND activo = 1');
            $stmt->execute([$idDoDepartamento]);
            if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Departamento inválido.', 422);

            $stmt = $this->PDO->prepare("SELECT id FROM solicitacoesDeVendedor WHERE idDoUsuario = ? AND estado = 'PENDENTE'");
            $stmt->execute([$idDoUsuario]);
            if ($stmt->fetchColumn()) throw new DomainException('Já tens uma candidatura pendente.', 409);

            $stmt = $this->PDO->prepare('INSERT INTO solicitacoesDeVendedor (idDoUsuario, numeroDoCartao, idDoDepartamento) VALUES (?, ?, ?)');
            $stmt->execute([$idDoUsuario, $numeroDoCartao, $idDoDepartamento]);
            $id = (int) $this->PDO->lastInsertId();
            $this->PDO->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->PDO->inTransaction()) $this->PDO->rollBack();
            throw $e;
        }
    }

    public function listar(string $estado): array {
        $sql = 'SELECT s.id, s.idDoUsuario, CONCAT(u.nome, " ", u.sobrenome) AS nomeCompleto, u.email, u.telefone, u.enderecoResidencial, s.numeroDoCartao, s.idDoDepartamento, d.nome AS departamento, s.estado, s.dataDoPedido, s.dataDaDecisao, s.motivoDaRejeicao FROM solicitacoesDeVendedor s JOIN usuarios u ON u.id = s.idDoUsuario JOIN departamentos d ON d.id = s.idDoDepartamento';
        $parametros = [];
        if ($estado !== '') {
            $sql .= ' WHERE s.estado = ?';
            $parametros[] = $estado;
        }
        $sql .= ' ORDER BY s.dataDoPedido DESC, s.id DESC';
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMinhas(int $idDoUsuario): array {
        $stmt = $this->PDO->prepare('SELECT s.id, s.numeroDoCartao, d.nome AS departamento, s.estado, s.dataDoPedido, s.dataDaDecisao, s.motivoDaRejeicao FROM solicitacoesDeVendedor s JOIN departamentos d ON d.id = s.idDoDepartamento WHERE s.idDoUsuario = ? ORDER BY s.id DESC');
        $stmt->execute([$idDoUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function decidir(int $id, int $idDoAdmin, string $decisao, ?string $motivo): void {
        $this->PDO->beginTransaction();
        try {
            $stmt = $this->PDO->prepare('SELECT * FROM solicitacoesDeVendedor WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$pedido) throw new InvalidArgumentException('Candidatura não encontrada.', 422);
            if ($pedido['estado'] !== 'PENDENTE') throw new DomainException('Esta candidatura já foi analisada.', 409);

            $stmt = $this->PDO->prepare('SELECT papel, activo FROM usuarios WHERE id = ? FOR UPDATE');
            $stmt->execute([(int) $pedido['idDoUsuario']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$usuario || !$usuario['activo'] || $usuario['papel'] !== 'CLIENTE') {
                throw new DomainException('O candidato já não é um cliente activo.', 409);
            }

            if ($decisao === 'APROVADA') {
                $stmt = $this->PDO->prepare('SELECT id FROM departamentos WHERE id = ? AND activo = 1');
                $stmt->execute([(int)$pedido['idDoDepartamento']]);
                if (!$stmt->fetchColumn()) throw new DomainException('O departamento já não está activo.', 409);
                $stmt = $this->PDO->prepare('INSERT INTO vendedores (idDoUsuario, numeroDoCartao, idDoDepartamento) VALUES (?, ?, ?)');
                $stmt->execute([(int)$pedido['idDoUsuario'], $pedido['numeroDoCartao'], (int)$pedido['idDoDepartamento']]);
                $stmt = $this->PDO->prepare("UPDATE usuarios SET papel = 'VENDEDOR' WHERE id = ?");
                $stmt->execute([(int)$pedido['idDoUsuario']]);
            }

            $stmt = $this->PDO->prepare('UPDATE solicitacoesDeVendedor SET estado = ?, idDoAdministrador = ?, dataDaDecisao = NOW(), motivoDaRejeicao = ? WHERE id = ?');
            $stmt->execute([$decisao, $idDoAdmin, $decisao === 'REJEITADA' ? $motivo : null, $id]);
            $this->PDO->commit();
        } catch (\Throwable $e) {
            if ($this->PDO->inTransaction()) $this->PDO->rollBack();
            throw $e;
        }
    }
}
