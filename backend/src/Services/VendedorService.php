<?php
declare(strict_types=1);

namespace APP\Services;

use APP\Repositories\VendedorRepositorio;
use DomainException;
use InvalidArgumentException;

class VendedorService
{
    public function __construct(
        private VendedorRepositorio $vendedores
    ) {}

    public function obterPerfil(int $idDoUsuario): array
    {
        $perfil = $this->vendedores->obterPerfil($idDoUsuario);

        if ($perfil === null) {
            throw new DomainException(
                "Vendedor não encontrado ou inactivo.",
                403
            );
        }

        return $perfil;
    }

    public function listarInventario(int $idDoUsuario): array
    {
        $perfil = $this->obterPerfil($idDoUsuario);

        return $this->vendedores->listarInventario(
            (int) $perfil["idDoDepartamento"]
        );
    }

    public function listarVendas(int $idDoUsuario): array
    {
        $perfil = $this->obterPerfil($idDoUsuario);

        return $this->vendedores->listarVendas(
            (int) $perfil["idDoDepartamento"]
        );
    }

    public function listarReservas(int $idDoUsuario): array
    {
        $perfil = $this->obterPerfil($idDoUsuario);

        return $this->vendedores->listarReservasDoDepartamento(
            (int) $perfil["idDoDepartamento"]
        );
    }

    public function obterPainel(int $idDoUsuario): array
    {
        $perfil = $this->obterPerfil($idDoUsuario);

        $painel = $this->vendedores->obterPainel(
            (int) $perfil["idDoDepartamento"]
        );

        $painel["departamento"] = $perfil["departamento"];

        return $painel;
    }

    public function registarVenda(
        int $idDoUsuario,
        array $dados
    ): array {
        $perfil = $this->obterPerfil($idDoUsuario);

        $itens = $dados["itens"] ?? null;

        if (
            !is_array($itens) ||
            !array_is_list($itens) ||
            count($itens) === 0 ||
            count($itens) > 100
        ) {
            throw new InvalidArgumentException(
                "A venda deve possuir entre 1 e 100 itens.",
                422
            );
        }

        $itensValidados = [];
        $ids = [];

        foreach ($itens as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException(
                    "Item inválido.",
                    422
                );
            }

            $id = $item["idDoProduto"] ?? null;
            $quantidade = $item["quantidade"] ?? null;

            if (
                !is_int($id) ||
                $id < 1 ||
                !is_int($quantidade) ||
                $quantidade < 1
            ) {
                throw new InvalidArgumentException(
                    "Produto ou quantidade inválida.",
                    422
                );
            }
            if (isset($ids[$id])) {
                throw new InvalidArgumentException(
                    "Não envie o mesmo produto duas vezes.",
                    422
                );
            }

            $ids[$id] = true;

            $itensValidados[] = [
                "idDoProduto" => $id,
                "quantidade" => $quantidade
            ];
        }
        usort(
            $itensValidados,fn($a, $b) => $a["idDoProduto"] <=> $b["idDoProduto"]
        );

        $idDoCliente = $dados["idDoCliente"] ?? null;

        if (
            $idDoCliente !== null &&
            (!is_int($idDoCliente) || $idDoCliente < 1)
        ) {
            throw new InvalidArgumentException(
                "Cliente inválido.",
                422
            );
        }

        return $this->vendedores->registarVendaDirecta(
            $idDoUsuario,
            (int) $perfil["idDoDepartamento"],
            $itensValidados,
            $idDoCliente
        );
    }

    public function finalizarReserva(
        int $idDoUsuario,
        int $idDaReserva
    ): array {
        if ($idDaReserva < 1) {
            throw new InvalidArgumentException(
                "Reserva inválida.",
                422
            );
        }

        $perfil = $this->obterPerfil($idDoUsuario);

        return $this->vendedores->finalizarReserva(
            $idDaReserva,
            $idDoUsuario,
            (int) $perfil["idDoDepartamento"]
        );
    }
}
