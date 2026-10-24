<?php
declare(strict_types=1);

namespace APP\Repositories;

use PDO;

class ProdutoRepositorio
{
    public function __construct(private PDO $PDO) {}

    public function listarProdutos(?string $categoria = null): array
    {
        $sql = "SELECT p.id, p.nome, p.descricao, p.marca, p.precoDeVenda,
                       p.stockFisico - p.stockReservado AS stockDisponivel,
                       c.nome AS categoria, d.nome AS departamento,
                       (SELECT i.caminhoDaImagem
                        FROM imagensDeProdutos i
                        WHERE i.idDoProduto = p.id
                        ORDER BY i.imagemPrincipal DESC, i.ordemDeExibicao, i.id
                        LIMIT 1) AS imagemPrincipal
                FROM produtos p
                INNER JOIN categorias c ON c.id = p.idDaCategoria
                INNER JOIN departamentos d ON d.id = p.idDoDepartamento
                WHERE p.activo = 1";

        $parametros = [];
        if ($categoria !== null) {
            $sql .= " AND c.nome = :categoria";
            $parametros['categoria'] = $categoria;
        }
        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    public function procurarProdutoPorId(int $id): ?array
    {
        $sql = "SELECT p.id, p.nome, p.descricao, p.marca, p.precoDeVenda,
                       p.stockFisico - p.stockReservado AS stockDisponivel,
                       c.nome AS categoria, d.nome AS departamento
                FROM produtos p
                INNER JOIN categorias c ON c.id = p.idDaCategoria
                INNER JOIN departamentos d ON d.id = p.idDoDepartamento
                WHERE p.id = :id AND p.activo = 1";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(['id' => $id]);
        $produto = $stmt->fetch();
        if (!$produto) {
            return null;
        }

        $produto['imagens'] = $this->procurarImagensDoProduto($id);
        $produto['caracteristicas'] = $this->procurarCaracteristicasDoProduto($id, $produto['categoria']);
        return $produto;
    }

    public function listarCategorias(): array
    {
        return $this->PDO->query(
            "SELECT id, nome, descricao FROM categorias ORDER BY id"
        )->fetchAll();
    }

    public function listarDepartamentos(): array
    {
        return $this->PDO->query(
            "SELECT d.id, d.nome, d.activo, c.nome AS categoria
             FROM departamentos d
             INNER JOIN categorias c ON c.id = d.idDaCategoria
             ORDER BY d.id"
        )->fetchAll();
    }

    private function procurarImagensDoProduto(int $id): array
    {
        $stmt = $this->PDO->prepare(
            "SELECT caminhoDaImagem, imagemPrincipal, ordemDeExibicao
             FROM imagensDeProdutos WHERE idDoProduto = :id
             ORDER BY imagemPrincipal DESC, ordemDeExibicao, id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    private function procurarCaracteristicasDoProduto(int $id, string $categoria): array
    {
        // Estas consultas são fixas, escolhidas pelo servidor, e não pelo cliente.
        switch ($categoria) {
            case 'COLARES':
                $sql = "SELECT x.comprimentoEmCm, m.nome AS material
                        FROM colares x INNER JOIN materiais m ON m.id = x.idDoMaterial
                        WHERE x.idDoProduto = :id";
                break;
            case 'PULSEIRAS':
                $sql = "SELECT x.comprimentoEmCm, m.nome AS material
                        FROM pulseiras x INNER JOIN materiais m ON m.id = x.idDoMaterial
                        WHERE x.idDoProduto = :id";
                break;
            case 'ANEIS':
                $sql = "SELECT x.diametroEmMm, m.nome AS material, x.possuiPedra,
                               x.tipoDePedra, ad.quilates
                        FROM aneis x
                        INNER JOIN materiais m ON m.id = x.idDoMaterial
                        LEFT JOIN aneisDeDiamante ad ON ad.idDoProduto = x.idDoProduto
                        WHERE x.idDoProduto = :id";
                break;
            case 'RELOGIOS':
                $sql = "SELECT m.nome AS materialDaAlca, x.tipoDeFecho,
                               x.sexo, x.fabricante
                        FROM relogios x
                        INNER JOIN materiaisDeAlca m ON m.id = x.idDoMaterialDaAlca
                        WHERE x.idDoProduto = :id";
                break;
            case 'BRINCOS':
                $sql = "SELECT m.nome AS material, x.possuiPedra, x.tipoDePedra
                        FROM brincos x INNER JOIN materiais m ON m.id = x.idDoMaterial
                        WHERE x.idDoProduto = :id";
                break;
            case 'PEGAS':
                $sql = "SELECT m.nome AS material, x.tipoDeFecho
                        FROM pegas x INNER JOIN materiais m ON m.id = x.idDoMaterial
                        WHERE x.idDoProduto = :id";
                break;
            default:
                return [];
        }
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: [];
    }
}
