<?php
declare(strict_types=1);
use APP\Middleware\AutenticacaoMiddleware;
use APP\padrao\Padroes;
use APP\padrao\RotaInvalidaException;
//sobre autenticacao
require_once __DIR__ . "/../vendor/autoload.php";

require_once dirname(__DIR__, 1) . "/config/db.php";


use APP\Controller\AutenticacaoControllers;
use APP\Repositories\UsuarioRepositorio;
use APP\Services\AutenticacaoService;


$rotaBase = "/joalheria/api";

//Rotas de Autenticacao publicas
$rotaAuthLogin = "{$rotaBase}/auth/login";
$rotaAuthLogout = "{$rotaBase}/auth/logout";
$rotaAuthRegistrar = "{$rotaBase}/auth/signup";
$rotaLoginVendedor = "{$rotaBase}/auth/login/vendedor";
$rotaLoginADMIN = "{$rotaBase}/auth/login/admin";
$rotaRecuperarSenha="{$rotaBase}/auth/recuperarSenha";

//Rotas de Administrador privadas
$rotaADMIN = "{$rotaBase}/actualizar/admin";
$rotaVendedor = "{$rotaBase}/actualizar/vendedor";


ini_set("session.use_strict_mode", "1");
session_name("BACKEND_SESSID");
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
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
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

    if ($padrao->verificarRota($url, $rotaAuthRegistrar) && $metodo === "POST") {

        $usuarioController->registarUsuario();

    }
    elseif ($padrao->verificarRota($url, $rotaAuthLogin) && $metodo === "POST") {
        $usuarioController->iniciarSessao();

    } 
    elseif ($padrao->verificarRota($url, $rotaAuthLogout) && $metodo === "POST") {
        $usuarioController->terminarSessao();

    } 
    elseif ($padrao->verificarRota($url, $rotaRecuperarSenha) && $metodo === "POST") {
        $usuarioController->actualizarSenha();

    } 
    elseif ($padrao->verificarRota($url, $rotaLoginVendedor) && $metodo === "POST") {
        $usuarioController->iniciarSessaoPorPapel("VENDEDOR");

    } 
    elseif ($padrao->verificarRota($url, $rotaLoginADMIN) && $metodo === "POST") {
        $usuarioController->iniciarSessaoPorPapel("ADMIN");

    } 
    elseif ($padrao->verificarRota($url, $rotaADMIN) && $metodo === 'PUT') {
        $autenticacao->exigirPapel("ADMIN");
        $usuarioController->actualizarPapelADMIN();
    } 
    elseif ($padrao->verificarRota($url, $rotaVendedor) && $metodo === 'PUT') {
        $autenticacao->exigirPapel("ADMIN");
        $usuarioController->actualizarPapelVendedor();
    } 
    else {
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

} catch (Throwable $e) {
    error_log($e->getMessage());
    Padroes::RespostaERRO(500, $e->getMessage());
}

