<?php
declare(strict_types=1);

namespace APP\Services;

use APP\Repositories\ReservaRepositorio;
use InvalidArgumentException;
use DomainException;

class ReservaService
{
    public function __construct(
        private ReservaRepositorio $reservas
    ) {}

    public function criarReserva(
        int $idDoUsuario,
        array $dados
    ): array {

        if (!$this->reservas->verificarCliente($idDoUsuario)) {
            throw new DomainException( "Este utilizador não  e um cliente.",
                403
            );
        }
        $itens = $dados["itens"] ?? null;
        if (
            !is_array($itens) ||
            !array_is_list($itens) ||
            count($itens) === 0 ||
            count($itens) > 100
        ) {
            throw new InvalidArgumentException( "eserva muito alta",
                422
            );
        }
        $itensValidados = [];
        $idsEncontrados = [];
        foreach ($itens as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException( "Item da reserva inválido.",
                    422
                );
            }
            $id = $item["idDoProduto"] ?? null;
            $quantidade = $item["quantidade"] ?? null;

            if (!is_int($id) || $id < 1 || !is_int($quantidade) || $quantidade < 1) {
                throw new InvalidArgumentException( "ID ou quantidade do produto inválida.",
                    422
                );
            }

            if (isset($idsEncontrados[$id])) {
                throw new InvalidArgumentException( "Existem produtos repetidos na reserva.",
                    422
                );
            }
            $idsEncontrados[$id] = true;
            $itensValidados[] = [
                "idDoProduto" => $id,
                "quantidade" => $quantidade
            ];
        }
        usort( $itensValidados, fn($a, $b) => $a["idDoProduto"] <=> $b["idDoProduto"] );
        return $this->reservas->criarReserva(
            $idDoUsuario,
            $itensValidados
        );
    }

    public function listarMinhasReservas(int $idDoUsuario): array
    {
        return $this->reservas->listarReservasDoCliente(
            $idDoUsuario
        );
    }

    public function procurarReservaPorId(
        int $idDaReserva,
        int $idDoUsuario
    ): ?array {

        return $this->reservas->procurarReservaPorId(
            $idDaReserva,
            $idDoUsuario
        );
    }

    public function cancelarReserva(
        int $idDaReserva,
        int $idDoUsuario
    ): bool {

        return $this->reservas->cancelarReserva(
            $idDaReserva,
            $idDoUsuario
        );
    }
}
