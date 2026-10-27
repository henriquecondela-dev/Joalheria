<?php declare(strict_types=1);
namespace APP\Services;

use APP\Repositories\SolicitacaoVendedorRepositorio;
use InvalidArgumentException;

class SolicitacaoVendedorService {
    public function __construct(private SolicitacaoVendedorRepositorio $repositorio) {}

    public function criar(int $id, array $dados): int {
        $cartao = trim((string)($dados['numeroDoCartao'] ?? ''));
        $departamento = filter_var($dados['idDoDepartamento'] ?? null, FILTER_VALIDATE_INT);
        if ($cartao === '' || strlen($cartao) > 50) throw new InvalidArgumentException('Número do cartão inválido.', 422);
        if ($departamento === false || $departamento <= 0) throw new InvalidArgumentException('Selecciona um departamento válido.', 422);
        return $this->repositorio->criar($id, $cartao, $departamento);
    }

    public function listar(string $estado): array {
        if (!in_array($estado, ['', 'PENDENTE', 'APROVADA', 'REJEITADA'], true)) throw new InvalidArgumentException('Estado inválido.', 422);
        return $this->repositorio->listar($estado);
    }

    public function minhas(int $id): array { return $this->repositorio->listarMinhas($id); }

    public function decidir(int $id, int $idDoAdmin, array $dados): void {
        $decisao = strtoupper(trim((string)($dados['decisao'] ?? '')));
        if (!in_array($decisao, ['APROVADA', 'REJEITADA'], true)) throw new InvalidArgumentException('Decisão inválida.', 422);
        $motivo = trim((string)($dados['motivo'] ?? ''));
        if (strlen($motivo) > 500) throw new InvalidArgumentException('Motivo demasiado longo.', 422);
        $this->repositorio->decidir($id, $idDoAdmin, $decisao, $motivo ?: null);
    }
}
