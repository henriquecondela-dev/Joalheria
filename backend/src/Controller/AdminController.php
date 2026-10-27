<?php
declare(strict_types=1);
namespace APP\Controller;
use APP\Services\AdminService;
use APP\padrao\Padroes;
class AdminController
{
    public function __construct(private AdminService $service, private Padroes $padrao)
    {
    }
    public function perfil(int $id): void
    {
        $this->enviar($this->service->perfil($id));
    }
    public function departamentos(): void
    {
        $this->enviar($this->service->departamentos());
    }
    public function inventario(): void
    {
        $this->enviar($this->service->inventario());
    }
    public function vendas(): void
    {
        $this->enviar($this->service->vendas());
    }
    public function vendedores(): void
    {
        $this->enviar($this->service->vendedores());
    }
    public function pedidos(): void
    {
        $this->enviar($this->service->pedidos());
    }
    public function destaques(): void
    {
        $this->enviar($this->service->produtosMaisVendidos());
    }
    public function produtividade(?string $inicio, ?string $fim): void
    {
        $this->enviar($this->service->produtividade($inicio, $fim));
    }
    public function relatorio(?string $inicio, ?string $fim, ?int $dep, ?int $cat): void
    {
        $this->enviar($this->service->relatorio($inicio, $fim, $dep, $cat));
    }
    public function solicitar(int $id): void
    {
        $this->enviar($this->service->solicitar($id, $this->padrao->lerDadosJson()), 201, 'Pedido enviado para análise.');
    }
    public function decidir(int $id, int $admin): void
    {
        $this->enviar($this->service->decidir($id, $admin, $this->padrao->lerDadosJson()), 200, 'Decisão registada.');
    }
    private function enviar(array $dados, int $status = 200, ?string $mensagem = null): void
    {
        $this->padrao->RespostaDados($status, $mensagem, $dados, true);
    }

    public function catalogosDeProdutos(): void
    {
        $this->enviar($this->service->catalogosDeProdutos());
    }

    public function criarProduto(): void
    {
        $this->enviar($this->service->criarProduto($this->padrao->lerDadosJson()), 201, 'Produto registado.');
    }

    public function abastecerProduto(int $id): void
    {
        $this->enviar($this->service->abastecerProduto($id, $this->padrao->lerDadosJson()), 200, 'Stock actualizado.');
    }


    public function enviarImagemProduto(int $id): void
    {
        if ($id < 1 || !isset($_FILES["imagem"]) || !is_array($_FILES["imagem"])) {
            throw new \InvalidArgumentException("Envie uma fotografia no campo imagem.", 422);
        }

        $ficheiro = $_FILES["imagem"];

        if (($ficheiro["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException("Falha no envio da fotografia. Código: " . $ficheiro["error"], 422);
        }

        if (!is_uploaded_file($ficheiro["tmp_name"])) {
            throw new \InvalidArgumentException("O ficheiro recebido não é um upload válido.", 422);
        }

        if ($ficheiro["size"] <= 0 || $ficheiro["size"] > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException("A fotografia deve ter no máximo 5 MB.", 422);
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($ficheiro["tmp_name"]);
        $extensoes = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];

        if (!isset($extensoes[$mime]) || getimagesize($ficheiro["tmp_name"]) === false) {
            throw new \InvalidArgumentException("Apenas fotografias JPEG, PNG ou WebP são permitidas.", 422);
        }

        $categoria = $this->service->categoriaDoProduto($id);

        $pastas = [
            "COLARES" => "colares",
            "PULSEIRAS" => "pulseiras",
            "ANEIS" => "aneis",
            "ANÉIS" => "aneis",
            "ANEIS DE DIAMANTE" => "aneis",
            "ANÉIS DE DIAMANTE" => "aneis",
            "ANEIS_DE_DIAMANTE" => "aneis",
            "RELOGIOS" => "relogios",
            "RELÓGIOS" => "relogios",
            "BRINCOS" => "brincos",
            "PEGAS" => "acessorios",
            "PEGAS/FECHOS" => "acessorios"
        ];

        if (!isset($pastas[$categoria])) {
            throw new \InvalidArgumentException("Categoria não suportada: " . $categoria, 422);
        }

        $pasta = $pastas[$categoria];
        $directorio = dirname(__DIR__, 3) . "/produtos/" . $pasta;

        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            throw new \RuntimeException("Não foi possível criar o directório das fotografias.");
        }

        if (!is_writable($directorio)) {
            throw new \RuntimeException("O servidor PHP não tem permissão para escrever em " . $directorio);
        }

        $nome = bin2hex(random_bytes(16)) . "." . $extensoes[$mime];
        $destino = $directorio . "/" . $nome;

        if (!move_uploaded_file($ficheiro["tmp_name"], $destino)) {
            throw new \RuntimeException("Não foi possível guardar a fotografia no servidor.");
        }

        $caminho = "/produtos/" . $pasta . "/" . $nome;

        try {
            $dados = $this->service->guardarImagemProduto($id, $caminho);
            $this->enviar($dados, 201, "Fotografia guardada com sucesso.");
        } catch (\Throwable $erro) {
            unlink($destino);
            throw $erro;
        }
    }

}
