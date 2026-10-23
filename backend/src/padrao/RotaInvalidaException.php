<?php
declare(strict_types=1);
namespace APP\padrao;

class RotaInvalidaException extends \Exception{
    function __construct(string $menssagem,int $codigo){
        parent::__construct($menssagem,$codigo);
    }
}