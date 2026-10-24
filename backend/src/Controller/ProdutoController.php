<?php
declare(strict_types=1);

namespace APP\Controller;

use APP\Services\ProdutoService;
use APP\padrao\Padroes;
use InvalidArgumentException;

class ProdutoController
{
    public function __construct(
        private ProdutoService $produtos,
        private Padroes $padrao
    ) {}

    public function listarProdutos(): void
    {
        $categoria = $_GET['categoria'] ?? null;
        if ($categoria !== null && !is_string($categoria)) {
            throw new InvalidArgumentException('Categoria inválida.', 422);
        }
        $dados = $this->produtos->listarProdutos($categoria);
        $this->padrao->RespostaDados(200, null, $dados, true);
    }

    public function procurarProdutoPorId(int $id): void
    {
        $produto = $this->produtos->procurarProdutoPorId($id);
        if ($produto === null) {
            $this->padrao->RespostaFalha(404, 'Produto não encontrado.');
            return;
        }
        $this->padrao->RespostaDados(200, null, $produto, true);
    }

    public function listarCategorias(): void
    {
        $this->padrao->RespostaDados(200, null, $this->produtos->listarCategorias(), true);
    }

    public function listarDepartamentos(): void
    {
        $this->padrao->RespostaDados(200, null, $this->produtos->listarDepartamentos(), true);
    }
}
