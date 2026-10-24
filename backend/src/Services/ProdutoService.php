<?php
declare(strict_types=1);

namespace APP\Services;

use APP\Repositories\ProdutoRepositorio;
use InvalidArgumentException;

class ProdutoService
{
    public function __construct(private ProdutoRepositorio $produtos) {}

    public function listarProdutos(?string $categoria): array
    {
        if ($categoria !== null) {
            $categoria = strtoupper(trim($categoria));
            if ($categoria === '' || strlen($categoria) > 100) {
                throw new InvalidArgumentException('Categoria inválida.', 422);
            }
        }
        return $this->produtos->listarProdutos($categoria);
    }

    public function procurarProdutoPorId(int $id): ?array
    {
        return $this->produtos->procurarProdutoPorId($id);
    }

    public function listarCategorias(): array
    {
        return $this->produtos->listarCategorias();
    }

    public function listarDepartamentos(): array
    {
        return $this->produtos->listarDepartamentos();
    }
}
