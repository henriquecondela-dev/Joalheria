<?php declare(strict_types=1);

namespace APP\padrao;
use InvalidArgumentException;

use RotaInvalidaException;

class Padroes{
    function __construct(){}
    public function RespostaDados(
        int $status,
        ?string $menssagem,
        ?array $dados,
        bool $sucesso
    ): void {
        if($dados===null){
            http_response_code($status);
             echo json_encode(
            [
                "sucesso"=>$sucesso,
                "menssagem"=>$menssagem,
            ],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        }
        elseif($menssagem===null){
            http_response_code($status);
            echo json_encode(
                [
                    "sucesso"=>$sucesso,
                    "dados"=>$dados
                ],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }else{
            http_response_code($status);
             echo json_encode(
            [
                "sucesso"=>$sucesso,
                "menssagem"=>$menssagem,
                "dados"=>$dados
            ],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        }
        
    }
    public function RespostaFalha(
        int $status,
        string $menssagem,
        bool $sucesso=false
    ): void {
        http_response_code($status);
        echo json_encode(
            [
                "sucesso"=>$sucesso,
                "menssagem"=>$menssagem
            ],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

     public static function RespostaERRO(
        int $status,
        string $erro,
        bool $sucesso=false
    ): void {
        http_response_code($status);
        echo json_encode(
            [
                "sucesso"=>$sucesso,
                "ERRO"=>$erro
            ],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    public function lerDadosJson(): array{
        $tipo = $_SERVER["CONTENT_TYPE"] ?? "";
        if (!preg_match( '~^application/json(?:\s*;|$)~i', $tipo )) {
            throw new InvalidArgumentException( "É necessário enviar JSON.", 415 );
        }
        $corpo = file_get_contents("php://input");
        if ($corpo === false) {
            throw new InvalidArgumentException( "Erro ao ler o pedido.", 400 );
        }
        try {
            $dados = json_decode( $corpo, true, 512, JSON_THROW_ON_ERROR );
        } catch (\JsonException $e) {
            throw new InvalidArgumentException( "JSON inválido.", 400, $e );
        }
        if (!is_array($dados) || array_is_list($dados)) {
            throw new InvalidArgumentException( "É necessário enviar um objecto JSON.", 422 );
        }
        return $dados;
    }
    public function verificarRota(string $url, string $rota) :bool{
        if($url !==$rota){
            return false;
        }
        return true;
    }
}