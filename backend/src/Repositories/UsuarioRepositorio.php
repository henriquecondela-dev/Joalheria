<?php
declare(strict_types=1);

namespace APP\Repositories;
use InvalidArgumentException;
use PDO;


class UsuarioRepositorio
{

    function __construct(private PDO $PDO)
    {
    }

    public function procurarUsuarioPorEmail(string $email): ?array
    {
        $stmt = $this->PDO->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(["email" => $email]);

        return $stmt->fetch() ?: null;
    }
    public function procurarUsuarioPorBI(string $numeroDoBI): ?array{
        $stmt = $this->PDO->prepare("SELECT * FROM clientes WHERE numeroDoBI = :numeroDoBI");
        $stmt->execute(["numeroDoBI" => $numeroDoBI]);
        return $stmt->fetch() ?: null;
    }
    public function procurarUsuarioPorId(int $id): ?array
    {
        $sql = "
        SELECT
            id,
            nome,
            sobrenome,
            email,
            telefone,
            enderecoResidencial,
            genero,
            papel,
            activo,
            dataDeCriacao
        FROM usuarios
        WHERE id = :id
    ";

        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["id" => $id]);
        return $stmt->fetch() ?: null;
    }
    function actualizarPapelVendedor(int $idDoUsuario, string $numeroDoCartao, int $idDoDepartamento): ?array
    {
        $u = $this->procurarUsuarioPorId($idDoUsuario);
        if ($u === null) {
            throw new InvalidArgumentException(
                "O utilizador não existe.",
                422
            );
        }
        if ($u["papel"] === "VENDEDOR") {
            return null;
        }
        $this->PDO->beginTransaction();
        try {
            $sql = "INSERT INTO vendedores (idDoUsuario, numeroDoCartao,idDoDepartamento) values (:idDoUsuario, :numeroDoCartao,:idDoDepartamento)";
            $update = $this->PDO->prepare($sql);
            $update->execute([
                "idDoUsuario" => $idDoUsuario,
                "numeroDoCartao" => $numeroDoCartao,
                "idDoDepartamento" => $idDoDepartamento
            ]);
            // $gerente = $update->fetch() ?? null;
            // if ($gerente === null) {
            //     return null;
            // }
            $update2 = $this->PDO->prepare("UPDATE usuarios set papel=:papel where id=:id");
            $update2->execute([
                "id" => $idDoUsuario,
                "papel" => 'VENDEDOR'
            ]);
            $usuarioActualizado = $this->procurarUsuarioPorId($idDoUsuario);
            $this->PDO->commit();
            return $usuarioActualizado;
        } catch (\Throwable $e) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }
            throw $e;
        }
    }
    function actualizarPapelADMIN(int $idDoUsuario, string $numeroDoCartao, string $nivelEducacional): ?array
    {
        $u = $this->procurarUsuarioPorId($idDoUsuario);
        if ($u === null) {
            throw new InvalidArgumentException(
                "O utilizador não existe.",
                422
            );
        }
        if ($u["papel"] === "ADMIN") {
            return null;
        }
        $this->PDO->beginTransaction();
        try {
            $sql = "INSERT INTO gerentes (idDoUsuario, numeroDoCartao,nivelEducacional) values (:idDoUsuario, :numeroDoCartao,:nivelEducacional)";
            $update = $this->PDO->prepare($sql);
            $update->execute([
                "idDoUsuario" => $idDoUsuario,
                "numeroDoCartao" => $numeroDoCartao,
                "nivelEducacional" => $nivelEducacional
            ]);
            // $gerente = $update->fetch() ?? null;
            // if ($gerente === null) {
            //     return null;
            // }
            $update2 = $this->PDO->prepare("UPDATE usuarios set papel=:papel where id=:id;");
            $update2->execute([
                "id" => $idDoUsuario,
                "papel" => 'ADMIN'
            ]);
            $usuarioActualizado = $this->procurarUsuarioPorId($idDoUsuario);
            $this->PDO->commit();
            return $usuarioActualizado;
        } catch (\Throwable $e) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }
            throw $e;
        }
    }
    function criarUsuario(
        string $nome,
        string $sobrenome,
        string $email,
        string $telefone,
        string $senhaHash,
        string $enderecoResidencial,
        string $genero,
        string $numeroDoBI,
        string $dataDeNascimento,
        ?string $localDeTrabalhoOuEstudo
    ): ?array {
        // $existe=$this->procurarUsuarioPorEmail(email:$email);
        // if($existe===null){
        //     return null;
        // }
        $this->PDO->beginTransaction();
        try {
            $stmt = $this->PDO->prepare("INSERT INTO usuarios 
        (nome, sobrenome, email,telefone,senhaHash, enderecoResidencial,genero) VALUES 
        (:nome, :sobrenome, :email,:telefone,:senhaHash,:enderecoResidencial,:genero)");
            $stmt->execute([
                "nome" => $nome,
                "sobrenome" => $sobrenome,
                "email" => $email,
                "telefone" => $telefone,
                "senhaHash" => $senhaHash,
                "enderecoResidencial" => $enderecoResidencial,
                "genero" => $genero
            ]);
            $usuario = (int) $this->PDO->lastInsertId();
            if (!is_int($usuario)) {
                return null;
            }
            $stmtCliente = $this->PDO->prepare("INSERT INTO clientes 
        (idDoUsuario,numeroDoBI,dataDeNascimento,localDeTrabalhoOuEstudo) VALUES 
        (:idDoUsuario,:numeroDoBI,:dataDeNascimento,:localDeTrabalhoOuEstudo)");
            $stmtCliente->execute([
                "idDoUsuario" => $usuario,
                "numeroDoBI" => $numeroDoBI,
                "dataDeNascimento" => $dataDeNascimento,
                "localDeTrabalhoOuEstudo" => $localDeTrabalhoOuEstudo
            ]);
            $usuarioCriado = $this->procurarUsuarioPorId($usuario);

            $this->PDO->commit();

            return $usuarioCriado;
        } catch (\Throwable $e) {
            if ($this->PDO->inTransaction()) {
                $this->PDO->rollBack();
            }
            throw $e;
        }
    }

    public function verificarVendedor(int $idDoUsuario): bool
    {
        $sql = "SELECT 1 FROM vendedoresWHERE idDoUsuario = :idDoUsuario LIMIT 1";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["idDoUsuario" => $idDoUsuario]);
        return $stmt->fetchColumn() !== false;
    }
                         
    public function verificarAdministrador(int $idDoUsuario): bool
    {
        $sql = "SELECT 1 FROM gerentes WHERE idDoUsuario = :idDoUsuario LIMIT 1";
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute(["idDoUsuario" => $idDoUsuario]);
        return $stmt->fetchColumn() !== false;
    }
    public function actualizarSenha(int $idDoUsuario, string $senhaHash):?array{
        $sql="UPDATE usuarios set senhaHash=:senhaHash where id=:idDoUsuario";
        $stmt=$this->PDO->prepare($sql);
        $stmt->execute([
            "senhaHash"=>$senhaHash,
            "idDoUsuario"=>$idDoUsuario
        ]);
        $usuarioActualizado = $this->procurarUsuarioPorId($idDoUsuario);
        return $usuarioActualizado;
    }
}