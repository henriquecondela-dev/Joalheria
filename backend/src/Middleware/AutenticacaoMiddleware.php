<?php declare(strict_types=1);

namespace APP\Middleware;

use APP\Repositories\UsuarioRepositorio;
use APP\padrao\Padroes;
class AutenticacaoMiddleware{
    public function __construct(private UsuarioRepositorio $usuarios,
    private Padroes $padrao
    ){}
    
    public function obterUsuarioAutenticado() :array{
        $id=$_SESSION["usuario_id"] ?? null;
        if(!is_int($id)){
            $this->padrao->RespostaFalha(401,"Usuario nao autenticado");
            exit;
        }
        $usuario=$this->usuarios->procurarUsuarioPorId($id);
        if($usuario===null){
            unset($_SESSION["usuario_id"]);
            $this->padrao->RespostaFalha(401,"sessao invalida");
            exit;
        }
        return $usuario;
    }
    public  function exigirPapel( string $papelNecessario ): void {
        $usuario = $this->obterUsuarioAutenticado();
        if ($usuario["papel"] !== $papelNecessario) {
            $this->padrao->RespostaFalha(401,"Nao tem permissao para essa operacao");
            exit;
        }
    }
}
