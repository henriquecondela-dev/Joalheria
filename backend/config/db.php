<?php
declare(strict_types=1);


function conectarMySQL(): PDO{
    $variaveis = parse_ini_file(dirname(__DIR__, 1) . '/.env');
    
    if ($variaveis === false) {
        throw new RuntimeException("Não foi possível carregar o ficheiro .env");
    }
    $host = $variaveis['BDHost'];
    $banco = $variaveis['BDNome'];
    $usuario = $variaveis['BDUsuario'];
    $senha = $variaveis['BDSenha'];


    if ($senha === false || $senha === "") {
        throw new RuntimeException("senha ou nao definida!");
    }
    $dsn = "mysql:host=$host;dbname=$banco;charset=utf8mb4";
    return new PDO(
        $dsn,
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
}

