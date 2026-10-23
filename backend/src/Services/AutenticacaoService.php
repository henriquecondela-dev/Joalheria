<?php

declare(strict_types=1);

namespace APP\Services;

use APP\Repositories\UsuarioRepositorio;
use InvalidArgumentException;
use RuntimeException;

class AutenticacaoService
{
    public function __construct(private UsuarioRepositorio $usuarios)
    {
    }
    public function actualizarSenha(array $dados,int $idDoUsuario): ?array
    {
        $senha = $dados["senha"] ?? null;
        $numeroDoBI = $dados["numeroDoBI"] ?? null;
        if (
            !is_int($idDoUsuario) || !is_string($senha) ||
            strlen($senha) < 6 ||
            strlen($senha) > 1024 || !is_string($numeroDoBI)
        ) {
            throw new InvalidArgumentException("Dados invalidos ou senha fora dos padroes");
        }

        if ($this->usuarios->procurarUsuarioPorBI($numeroDoBI) === null) {
            throw new InvalidArgumentException("Bi invalido");
        }
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $usuario = $this->usuarios->actualizarSenha(idDoUsuario:$idDoUsuario,senhaHash:$senhaHash);

        if ($usuario === null) {
            throw new RuntimeException("Nao foi possivel actualizar a senha");
        }
        return $usuario;
    }
    public function registarUsuario(array $dados): array
    {

        $nome = $dados["nome"] ?? null;
        $sobrenome = $dados["sobrenome"] ?? null;
        $email = $dados["email"] ?? null;
        $telefone = $dados["telefone"] ?? null;
        $senha = $dados["senha"] ?? null;
        $enderecoResidencial = $dados["enderecoResidencial"] ?? null;
        $genero = $dados["genero"] ?? null;

        $numeroDoBI = $dados["numeroDoBI"] ?? null;
        $dataDeNascimento = $dados["dataDeNascimento"] ?? null;
        $localDeTrabalhoOuEstudo = $dados["localDeTrabalhoOuEstudo"] ?? null;

        if (
            !is_string($nome) ||
            trim($nome) === "" ||

            !is_string($sobrenome) ||
            trim($sobrenome) === "" ||

            !is_string($email) ||
            strlen($email) > 255 ||
            filter_var($email, FILTER_VALIDATE_EMAIL) === false ||

            !is_string($telefone) ||
            trim($telefone) === "" ||

            !is_string($senha) ||
            strlen($senha) < 6 ||
            strlen($senha) > 1024 ||

            !is_string($enderecoResidencial) ||
            trim($enderecoResidencial) === "" ||

            !is_string($numeroDoBI) ||
            trim($numeroDoBI) === "" ||
            strlen($numeroDoBI) > 50 ||

            !is_string($dataDeNascimento)
        ) {
            throw new InvalidArgumentException(
                "Dados pessoais ou credenciais inválidos."
            );
        }
        $generosPermitidos = ["MASCULINO", "FEMININO", "OUTRO"];
        if (!is_string($genero) || !in_array($genero, $generosPermitidos, true)) {
            throw new InvalidArgumentException(
                "Género inválido."
            );
        }
        $data = \DateTimeImmutable::createFromFormat("!Y-m-d", $dataDeNascimento);
        if ($data === false || $data->format("Y-m-d") !== $dataDeNascimento) {
            throw new InvalidArgumentException(
                "Data de nascimento inválida. formato aceito yyy-mm-dd"
            );
        }

        if ($localDeTrabalhoOuEstudo !== null && (!is_string($localDeTrabalhoOuEstudo))) {
            throw new InvalidArgumentException(
                "Local de trabalho ou estudo inválido."
            );
        }
        $nome = trim($nome);
        $sobrenome = trim($sobrenome);
        $email = strtolower(trim($email));
        $telefone = trim($telefone);
        $enderecoResidencial = trim($enderecoResidencial);
        $numeroDoBI = trim($numeroDoBI);

        if ($localDeTrabalhoOuEstudo !== null) {
            $localDeTrabalhoOuEstudo = trim($localDeTrabalhoOuEstudo);
            if ($localDeTrabalhoOuEstudo === "") {
                $localDeTrabalhoOuEstudo = null;
            }
        }

        if ($this->usuarios->procurarUsuarioPorEmail($email) !== null) {
            throw new RuntimeException(
                "O email em uso.",
                409
            );
        }
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $usuario = $this->usuarios->criarUsuario(
            $nome,
            $sobrenome,
            $email,
            $telefone,
            $senhaHash,
            $enderecoResidencial,
            $genero,
            $numeroDoBI,
            $dataDeNascimento,
            $localDeTrabalhoOuEstudo
        );
        if ($usuario === null) {
            throw new RuntimeException(
                "Não foi possível registar o utilizador."
            );
        }
        return $usuario;
    }
    public function verificarCredenciais(string $email, string $senha): ?array
    {
        $email = strtolower(trim($email));

        $usuario = $this->usuarios->procurarUsuarioPorEmail($email);

        if ($usuario === null || !$usuario["activo"] || !password_verify($senha, $usuario["senhaHash"])) {
            return null;
        }
        return [
            "id" => (int) $usuario["id"],
            "nome" => $usuario["nome"],
            "sobrenome" => $usuario["sobrenome"],
            "email" => $usuario["email"],
            "papel" => $usuario["papel"]
        ];
    }
    public function verificarCredenciaisPorPapel(
        string $email,
        string $senha,
        string $papel
    ): ?array {
        $usuario = $this->verificarCredenciais($email, $senha);
        if ($usuario === null) {
            return null;
        }
        if ($usuario["papel"] !== $papel) {
            return null;
        }
        if ($papel === "VENDEDOR") {
            if ($this->usuarios->verificarVendedor($usuario["id"])) {
                return $usuario;
            }
        }
        if ($papel === "ADMIN") {
            if ($this->usuarios->verificarAdministrador($usuario["id"])) {
                return $usuario;
            }
        }
        return null;
    }
}