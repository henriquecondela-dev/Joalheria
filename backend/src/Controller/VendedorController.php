<?php
declare(strict_types=1);

namespace APP\Controller;

use APP\Services\VendedorService;
use APP\padrao\Padroes;

class VendedorController
{
    public function __construct(
        private VendedorService $vendedores,
        private Padroes $padrao
    ) {}

    public function obterPerfil(int $idDoUsuario): void
    {
        $dados = $this->vendedores->obterPerfil($idDoUsuario);

        $this->padrao->RespostaDados(
            200, null, $dados, true
        );
    }
    public function obterPainel(int $idDoUsuario): void
    {
        $dados = $this->vendedores->obterPainel($idDoUsuario);

        $this->padrao->RespostaDados(
            200, null, $dados, true
        );
    }
    public function listarInventario(int $idDoUsuario): void
    {
        $dados = $this->vendedores->listarInventario($idDoUsuario);

        $this->padrao->RespostaDados(
            200, null, $dados, true
        );
    }
    public function listarVendas(int $idDoUsuario): void
    {
        $dados = $this->vendedores->listarVendas($idDoUsuario);

        $this->padrao->RespostaDados(
            200, null, $dados, true
        );
    }
    public function listarReservas(int $idDoUsuario): void
    {
        $dados = $this->vendedores->listarReservas($idDoUsuario);

        $this->padrao->RespostaDados(
            200, null, $dados, true
        );
    }
    public function registarVenda(int $idDoUsuario): void
    {
        $dados = $this->padrao->lerDadosJson();

        $venda = $this->vendedores->registarVenda(
            $idDoUsuario,
            $dados
        );

        $this->padrao->RespostaDados(
            201,
            "Venda registada com sucesso.",
            $venda,
            true
        );
    }
    public function finalizarReserva(
        int $idDoUsuario,
        int $idDaReserva
    ): void {
        $venda = $this->vendedores->finalizarReserva(
            $idDoUsuario,
            $idDaReserva
        );

        $this->padrao->RespostaDados(
            201,
            "Pagamento e levantamento registados.",
            $venda,
            true
        );
    }
}
