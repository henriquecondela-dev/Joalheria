<?php
declare(strict_types=1);

use APP\Middleware\AutenticacaoMiddleware;
use APP\padrao\Padroes;
use APP\padrao\RotaInvalidaException;

use APP\Controller\AutenticacaoControllers;
use APP\Repositories\UsuarioRepositorio;
use APP\Services\AutenticacaoService;

use APP\Controller\ProdutoController;
use APP\Repositories\ProdutoRepositorio;
use APP\Services\ProdutoService;

use APP\Controller\ReservaController;
use APP\Repositories\ReservaRepositorio;
use APP\Services\ReservaService;

use APP\Repositories\VendedorRepositorio;
use APP\Services\VendedorService;
use APP\Controller\VendedorController;

use APP\Repositories\AdminRepositorio;
use APP\Services\AdminService;
use APP\Controller\AdminController;

use APP\Repositories\SolicitacaoVendedorRepositorio;
use APP\Services\SolicitacaoVendedorService;
use APP\Controller\SolicitacaoVendedorController;

require_once __DIR__ . "/../carregamentoAutomatico.php";
require_once dirname(__DIR__, 1) . "/config/db.php";

$rotaBase = "/joalheria/api";

//Rotas de Autenticacao publicas
$rotaAuthLogin = "{$rotaBase}/auth/login";
$rotaAuthLogout = "{$rotaBase}/auth/logout";
$rotaAuthRegistrar = "{$rotaBase}/auth/signup";
$rotaLoginVendedor = "{$rotaBase}/auth/login/vendedor";
$rotaLoginADMIN = "{$rotaBase}/auth/login/admin";
$rotaRecuperarSenha = "{$rotaBase}/auth/recuperarSenha";
$rotaAuthMe = "{$rotaBase}/auth/eu";

//Rotas de Administrador privadas
$rotaADMIN = "{$rotaBase}/actualizar/admin";
$rotaVendedor = "{$rotaBase}/actualizar/vendedor";

//Rotas publicas de produtos
$rotaProdutos = "{$rotaBase}/produtos";
$rotaCategorias = "{$rotaBase}/categorias";
$rotaDepartamentos = "{$rotaBase}/departamentos";
$rotaReservas = "{$rotaBase}/reservas";
$rotaMinhasReservas = "{$rotaBase}/reservas/minhas";

//Rotas vendedor, administrador e solicitacoes
$rotaAreaVendedor = "{$rotaBase}/vendedor";
$rotaAreaAdmin = "{$rotaBase}/admin";
$rotaSolicitacoes = "{$rotaBase}/solicitacoes/vendedor";
$rotaSolicitacoesAdmin = "{$rotaBase}/admin/solicitacoes/vendedor";

ini_set("session.use_strict_mode", "1");
session_name("JOALHERIA_API");

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => false,
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

$metodo = $_SERVER["REQUEST_METHOD"];
$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

try {
    $pdo = conectarMySQL();
    $padrao = new Padroes();

    $usuarioRepositorio = new UsuarioRepositorio($pdo);
    $usuarioService = new AutenticacaoService($usuarioRepositorio);
    $usuarioController = new AutenticacaoControllers($usuarioService, $usuarioRepositorio, $padrao);
    $autenticacao = new AutenticacaoMiddleware($usuarioRepositorio, $padrao);

    $produtoRepositorio = new ProdutoRepositorio($pdo);
    $produtoService = new ProdutoService($produtoRepositorio);
    $produtoController = new ProdutoController($produtoService, $padrao);

    $reservaRepositorio = new ReservaRepositorio($pdo);
    $reservaService = new ReservaService($reservaRepositorio);
    $reservaController = new ReservaController($reservaService, $padrao);

    $vendedorRepositorio = new VendedorRepositorio($pdo);
    $vendedorService = new VendedorService($vendedorRepositorio);
    $vendedorController = new VendedorController($vendedorService, $padrao);

    $adminRepo = new AdminRepositorio($pdo);
    $adminService = new AdminService($adminRepo);
    $adminController = new AdminController($adminService, $padrao);

    $solicitacaoRepositorio = new SolicitacaoVendedorRepositorio($pdo);
    $solicitacaoService = new SolicitacaoVendedorService($solicitacaoRepositorio);
    $solicitacaoController = new SolicitacaoVendedorController($solicitacaoService, $padrao);

    if ($padrao->verificarRota($url, $rotaAuthRegistrar) && $metodo === "POST") {
        $usuarioController->registarUsuario();

    } elseif ($padrao->verificarRota($url, $rotaAuthLogin) && $metodo === "POST") {
        $usuarioController->iniciarSessao();

    } elseif ($padrao->verificarRota($url, $rotaAuthLogout) && $metodo === "POST") {
        $usuarioController->terminarSessao();

    } elseif ($padrao->verificarRota($url, $rotaRecuperarSenha) && $metodo === "POST") {
        $usuarioController->actualizarSenha();

    } elseif ($padrao->verificarRota($url, $rotaAuthMe) && $metodo === "GET") {
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $padrao->RespostaDados(200, "Utilizador autenticado", [
            "id" => (int) $usuario["id"],
            "nome" => $usuario["nome"],
            "sobrenome" => $usuario["sobrenome"],
            "papel" => $usuario["papel"]
        ], true);

    } elseif ($padrao->verificarRota($url, $rotaLoginVendedor) && $metodo === "POST") {
        $usuarioController->iniciarSessaoPorPapel("VENDEDOR");

    } elseif ($padrao->verificarRota($url, $rotaLoginADMIN) && $metodo === "POST") {
        $usuarioController->iniciarSessaoPorPapel("ADMIN");

    } elseif ($padrao->verificarRota($url, $rotaADMIN) && $metodo === "PUT") {
        $autenticacao->exigirPapel("ADMIN");
        $usuarioController->actualizarPapelADMIN();

    } elseif ($padrao->verificarRota($url, $rotaVendedor) && $metodo === "PUT") {
        $autenticacao->exigirPapel("ADMIN");
        $usuarioController->actualizarPapelVendedor();

    } elseif ($padrao->verificarRota($url, $rotaProdutos) && $metodo === "GET") {
        $produtoController->listarProdutos();

    } elseif (preg_match('~^' . preg_quote($rotaProdutos, '~') . '/([1-9][0-9]*)$~', $url, $resultado) && $metodo === "GET") {
        $id = filter_var($resultado[1], FILTER_VALIDATE_INT);
        if ($id === false) {
            $padrao->RespostaFalha(404, "Produto não encontrado.");
        } else {
            $produtoController->procurarProdutoPorId($id);
        }

    } elseif ($padrao->verificarRota($url, $rotaCategorias) && $metodo === "GET") {
        $produtoController->listarCategorias();

    } elseif ($padrao->verificarRota($url, $rotaDepartamentos) && $metodo === "GET") {
        $produtoController->listarDepartamentos();

    } elseif ($padrao->verificarRota($url, $rotaReservas) && $metodo === "POST") {
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $reservaController->criarReserva((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, $rotaMinhasReservas) && $metodo === "GET") {
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $reservaController->listarMinhasReservas((int) $usuario["id"]);

    } elseif (preg_match('~^' . preg_quote($rotaReservas, '~') . '/([1-9][0-9]*)/cancelar$~', $url, $resultado) && $metodo === "DELETE") {
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $id = filter_var($resultado[1], FILTER_VALIDATE_INT);
        if ($id === false) {
            $padrao->RespostaFalha(404, "Reserva não encontrada.");
        } else {
            $reservaController->cancelarReserva($id, (int) $usuario["id"]);
        }

    } elseif (preg_match('~^' . preg_quote($rotaReservas, '~') . '/([1-9][0-9]*)$~', $url, $resultado) && $metodo === "GET") {
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $id = filter_var($resultado[1], FILTER_VALIDATE_INT);
        if ($id === false) {
            $padrao->RespostaFalha(404, "Reserva não encontrada.");
        } else {
            $reservaController->procurarReservaPorId($id, (int) $usuario["id"]);
        }

    } elseif ($padrao->verificarRota($url, "{$rotaAreaVendedor}/perfil") && $metodo === "GET") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $vendedorController->obterPerfil((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaVendedor}/painel") && $metodo === "GET") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $vendedorController->obterPainel((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaVendedor}/inventario") && $metodo === "GET") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $vendedorController->listarInventario((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaVendedor}/vendas") && $metodo === "GET") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $vendedorController->listarVendas((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaVendedor}/vendas") && $metodo === "POST") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $vendedorController->registarVenda((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaVendedor}/reservas") && $metodo === "GET") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $vendedorController->listarReservas((int) $usuario["id"]);

    } elseif (preg_match('~^' . preg_quote($rotaAreaVendedor, '~') . '/reservas/([1-9][0-9]*)/finalizar$~', $url, $resultado) && $metodo === "POST") {
        $autenticacao->exigirPapel("VENDEDOR");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $idDaReserva = filter_var($resultado[1], FILTER_VALIDATE_INT);
        if ($idDaReserva === false) {
            $padrao->RespostaFalha(404, "Reserva inválida.");
        } else {
            $vendedorController->finalizarReserva((int) $usuario["id"], $idDaReserva);
        }

    } elseif ($padrao->verificarRota($url, $rotaSolicitacoes) && $metodo === "POST") {
        $autenticacao->exigirPapel("CLIENTE");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $solicitacaoController->criar((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaSolicitacoes}/minhas") && $metodo === "GET") {
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $solicitacaoController->minhas((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, $rotaSolicitacoesAdmin) && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $solicitacaoController->listar();

    } elseif (preg_match('~^' . preg_quote($rotaSolicitacoesAdmin, '~') . '/([1-9][0-9]*)/decidir$~', $url, $resultado) && $metodo === "POST") {
        $autenticacao->exigirPapel("ADMIN");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $solicitacaoController->decidir((int) $resultado[1], (int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/perfil") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $adminController->perfil((int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/departamentos") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->departamentos();

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/inventario") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->inventario();

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/produtos/catalogos") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->catalogosDeProdutos();

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/produtos") && $metodo === "POST") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->criarProduto();

    } elseif (preg_match('~^' . preg_quote($rotaAreaAdmin, '~') . '/produtos/([1-9][0-9]*)/stock$~', $url, $resultado) && $metodo === "PATCH") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->abastecerProduto((int) $resultado[1]);

    } elseif (preg_match('~^' . preg_quote($rotaAreaAdmin, '~') . '/produtos/([1-9][0-9]*)/imagens$~', $url, $resultado) && $metodo === "POST") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->enviarImagemProduto((int) $resultado[1]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/vendas") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->vendas();

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/vendedores") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->vendedores();

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/destaques") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->destaques();

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/solicitacoes") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $solicitacaoController->listar();

    } elseif (preg_match('~^' . preg_quote($rotaAreaAdmin, '~') . '/solicitacoes/([1-9][0-9]*)/decidir$~', $url, $partes) && $metodo === "POST") {
        $autenticacao->exigirPapel("ADMIN");
        $usuario = $autenticacao->obterUsuarioAutenticado();
        $solicitacaoController->decidir((int) $partes[1], (int) $usuario["id"]);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/relatorio") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $dep = filter_input(INPUT_GET, "departamento", FILTER_VALIDATE_INT);
        $cat = filter_input(INPUT_GET, "categoria", FILTER_VALIDATE_INT);
        $adminController->relatorio($_GET["inicio"] ?? null, $_GET["fim"] ?? null, $dep ?: null, $cat ?: null);

    } elseif ($padrao->verificarRota($url, "{$rotaAreaAdmin}/produtividade") && $metodo === "GET") {
        $autenticacao->exigirPapel("ADMIN");
        $adminController->produtividade($_GET["inicio"] ?? null, $_GET["fim"] ?? null);

    } else {
        throw new RotaInvalidaException("Rota invalida", 404);
    }

} catch (PDOException $e) {
    Padroes::RespostaERRO(500, $e->getMessage());

} catch (RotaInvalidaException $e) {
    Padroes::RespostaERRO($e->getCode(), $e->getMessage());

} catch (InvalidArgumentException $e) {
    $codigo = $e->getCode();
    if (!in_array($codigo, [400, 415, 422], true)) {
        $codigo = 422;
    }
    Padroes::RespostaERRO($codigo, $e->getMessage());

} catch (DomainException $e) {
    $codigo = $e->getCode();
    if (!in_array($codigo, [403, 409], true)) {
        $codigo = 409;
    }
    Padroes::RespostaERRO($codigo, $e->getMessage());

} catch (Throwable $e) {
    error_log($e->getMessage());
    Padroes::RespostaERRO(500, $e->getMessage());
}
