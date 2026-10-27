<?php declare(strict_types=1);
namespace APP\Controller;

use APP\Services\SolicitacaoVendedorService;
use APP\padrao\Padroes;

class SolicitacaoVendedorController {
    public function __construct(private SolicitacaoVendedorService $service, private Padroes $padrao) {}

    public function criar(int $idDoUsuario): void {
        $id = $this->service->criar($idDoUsuario, $this->padrao->lerDadosJson());
        $this->padrao->RespostaDados(201, 'Candidatura enviada. Aguarda a decisão da administração.', ['id' => $id, 'estado' => 'PENDENTE'], true);
    }

    public function listar(): void {
        $pedidos = $this->service->listar(strtoupper(trim((string)($_GET['estado'] ?? 'PENDENTE'))));
        $this->padrao->RespostaDados(200, 'Candidaturas encontradas.', $pedidos, true);
    }

    public function minhas(int $idDoUsuario): void {
        $this->padrao->RespostaDados(200, 'As minhas candidaturas.', $this->service->minhas($idDoUsuario), true);
    }

    public function decidir(int $id, int $admin): void {
        $this->service->decidir($id, $admin, $this->padrao->lerDadosJson());
        $this->padrao->RespostaDados(200, 'Decisão registada.', null, true);
    }
}
