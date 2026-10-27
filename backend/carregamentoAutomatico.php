<?php
declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    $prefixo = "APP\\";

    if (!str_starts_with($classe, $prefixo)) {
        return;
    }

    $caminhoRelativo = substr($classe, strlen($prefixo));
    $caminhoRelativo = str_replace("\\", "/", $caminhoRelativo);
    $ficheiro = __DIR__ . "/src/" . $caminhoRelativo . ".php";

    if (is_file($ficheiro)) {
        require_once $ficheiro;
    }
});
