<?php
declare(strict_types=1);

namespace APP\Controller;

use APP\Repositories\UsuarioRepositorio;
use APP\Services\AutenticacaoService;
use APP\padrao\Padroes;
use InvalidArgumentException;

class AutenticacaoControllers
{

    public function __construct(
        private AutenticacaoService $autenticacao,
        private UsuarioRepositorio $usuarios,
        private Padroes $padroes
    ) {
    }
    public function registarUsuario(): void
    {
        $usuario = $this->autenticacao->registarUsuario($this->padroes->lerDadosJson());
        $this->padroes->RespostaDados(status: 201, menssagem: "usuario criado com sucesso!", dados: $usuario, sucesso: true);
    }
    public function iniciarSessao(): void
    {
        $dados = $this->padroes->lerDadosJson();
        if (!is_string($dados["email"] ?? null) || !is_string($dados["senha"] ?? null)) {
            throw new InvalidArgumentException("Email e palavra-passe são obrigatórios.");
        }

        $usuario = $this->autenticacao->verificarCredenciais($dados["email"], $dados["senha"]);

        if ($usuario === null) {
            $this->padroes->RespostaFalha(401, "Email ou senha Incorectos");
            return;
        }
        session_regenerate_id(true);
        $_SESSION["usuario_id"] = (int) $usuario["id"];
        $this->padroes->RespostaDados(
            status: 200,
            menssagem: "Login Realizado com sucesso!",
            dados: $usuario,
            sucesso: true
        );
    }
    public function actualizarSenha(): void
    {
        $dados = $this->padroes->lerDadosJson();
        if (!is_string($dados["senha"] ?? null) || !is_string($dados["numeroDoBI"] ?? null)) {
            throw new InvalidArgumentException("Senha e Numero de BI sao obrigatorios");
        }
        $usuario = $this->usuarios->procurarUsuarioPorBI($dados["numeroDoBI"]);
        $usuarioActualizado = $this->autenticacao->actualizarSenha($dados, $usuario["idDoUsuario"]);
        session_regenerate_id(true);
        $_SESSION["usuario_id"] = (int) $usuario["idDoUsuario"];
        $this->padroes->RespostaDados(
            status: 200,
            menssagem: "Senha Actualizada",
            dados: $usuarioActualizado,
            sucesso: true
        );
    }
    public function obterUsuarioAutenticado(): ?array
    {
        $id = $_SESSION["usuario_id"] ?? null;

        if (!is_int($id)) {
            $this->padroes->RespostaFalha(401, "Nao autenticado");
            return null;
        }
        $usuario = $this->usuarios->procurarUsuarioPorId($id);
        if ($usuario === null) {
            unset($_SESSION["usuario_id"]);
            $this->padroes->RespostaFalha(401, "Sessao invalida");
            return null;
        }
        return $usuario;
    }
    public function terminarSessao(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", [
                "expires" => time() - 3600,
                "path" => $params["path"],
                "domain" => $params["domain"],
                "secure" => $params["secure"],
                "httponly" => $params["httponly"],
                "samesite" => $params["samesite"]
            ]);
        }
        session_destroy();
        $this->padroes->RespostaDados(200, "Sessao Terminada", null, true);
    }
    public function actualizarPapelVendedor(): void
    {
        $usuario = $this->obterUsuarioAutenticado();
        if ($usuario === null) {
            return;
        }
        $dados = $this->padroes->lerDadosJson();
        if (
            !is_int($dados["idDoUsuario"] ?? null) ||
            !is_string($dados["numeroDoCartao"] ?? null) ||
            !is_int($dados["idDoDepartamento"] ?? null)
        ) {
            throw new InvalidArgumentException(
                "Dados inválidos ou em falta",
                422
            );
        }
        $usuarioActualizado = $this->usuarios->actualizarPapelVendedor(idDoUsuario: $dados["idDoUsuario"], numeroDoCartao: $dados["numeroDoCartao"], idDoDepartamento: $dados["idDoDepartamento"]);
        if ($usuarioActualizado === null) {
            $this->padroes->RespostaFalha(409, "Conta ja e Vendedor");
        } else {
            $this->padroes->RespostaDados(200, "Conta actualizada com sucesso", $usuarioActualizado, true);
        }
    }
    public function actualizarPapelADMIN(): void
    {
        $usuario = $this->obterUsuarioAutenticado();
        if ($usuario === null) {
            return;
        }
        $dados = $this->padroes->lerDadosJson();
        // if(!is_string($dados["numeroDoCartao"])){
        //     echo $dados["numeroDoCartao"];
        //     exit;
        // }
        if (
            !is_int($dados["idDoUsuario"] ?? null) || !is_string($dados["numeroDoCartao"] ?? null) || !is_string($dados["nivelEducacional"] ?? null)
        ) {
            throw new InvalidArgumentException("Dodos invalidos ou em falta", 422);
        }
        $usuarioActualizado = $this->usuarios->actualizarPapelADMIN(idDoUsuario: $dados["idDoUsuario"], numeroDoCartao: $dados["numeroDoCartao"], nivelEducacional: $dados["nivelEducacional"]);
        if ($usuarioActualizado === null) {
            $this->padroes->RespostaFalha(409, "Conta ja e Administrador");
        } else {
            $this->padroes->RespostaDados(200, "Conta actualizada com sucesso", $usuarioActualizado, true);
        }
    }
    public function iniciarSessaoPorPapel(string $papel): void
    {
        $dados = $this->padroes->lerDadosJson();
        if (
            !is_string($dados["email"] ?? null) ||
            !is_string($dados["senha"] ?? null) ||
            trim($dados["email"]) === "" ||
            $dados["senha"] === ""
        ) {
            throw new InvalidArgumentException(
                "Email e palavra-passe são obrigatórios.",
                422
            );
        }
        $usuario = $this->autenticacao->verificarCredenciaisPorPapel(
            $dados["email"],
            $dados["senha"],
            $papel
        );
        if ($usuario === null) {
            $this->padroes->RespostaFalha(
                401,
                "Credenciais inválidas ou acesso não autorizado."
            );
            return;
        }
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION["usuario_id"] = (int) $usuario["id"];
        $this->padroes->RespostaDados(
            200,
            "Login realizado com sucesso!",
            $usuario,
            true
        );
    }
}
