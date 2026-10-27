<?php
declare(strict_types=1);
namespace APP\Repositories;

use PDO;
use DomainException;
use InvalidArgumentException;
use Throwable;

class AdminRepositorio {
    public function __construct(private PDO $PDO) {}

    public function perfil(int $id): ?array {
        $s=$this->PDO->prepare("SELECT u.id,u.nome,u.sobrenome,u.email,u.telefone,u.enderecoResidencial,g.numeroDoCartao,g.nivelEducacional FROM gerentes g JOIN usuarios u ON u.id=g.idDoUsuario WHERE u.id=:id AND u.papel='ADMIN' AND u.activo=1");
        $s->execute(['id'=>$id]); return $s->fetch() ?: null;
    }
    public function departamentos(): array {
        return $this->PDO->query("SELECT d.id,d.nome,c.nome AS categoria, COALESCE(pl.metaDeVendas,0) AS metaDeVendas, COALESCE(pl.planoDeCompras,0) AS planoDeCompras FROM departamentos d JOIN categorias c ON c.id=d.idDaCategoria LEFT JOIN planosDeDepartamento pl ON pl.idDoDepartamento=d.id AND pl.ano=YEAR(CURDATE()) AND pl.mes=MONTH(CURDATE()) WHERE d.activo=1 ORDER BY d.nome")->fetchAll();
    }
    public function inventario(): array {
        return $this->PDO->query("SELECT p.id,p.nome,p.custoDeAquisicao,p.precoDeVenda,p.stockFisico,p.stockReservado,(p.stockFisico-p.stockReservado) AS stockDisponivel,c.nome AS categoria,d.nome AS departamento,p.idDoDepartamento,p.idDaCategoria,(SELECT i.caminhoDaImagem FROM imagensDeProdutos i WHERE i.idDoProduto=p.id ORDER BY i.imagemPrincipal DESC,i.ordemDeExibicao ASC LIMIT 1) AS imagemPrincipal FROM produtos p JOIN categorias c ON c.id=p.idDaCategoria JOIN departamentos d ON d.id=p.idDoDepartamento WHERE p.activo=1 ORDER BY p.nome")->fetchAll();
    }
    public function vendas(): array {
        return $this->PDO->query("SELECT v.id,v.dataDaVenda,v.valorTotal,v.idDaReserva,d.nome AS departamento,CONCAT(uv.nome,' ',uv.sobrenome) AS vendedor,CONCAT(COALESCE(uc.nome,''),' ',COALESCE(uc.sobrenome,'')) AS cliente FROM vendas v JOIN departamentos d ON d.id=v.idDoDepartamento JOIN usuarios uv ON uv.id=v.idDoVendedor LEFT JOIN usuarios uc ON uc.id=v.idDoCliente ORDER BY v.dataDaVenda DESC,v.id DESC")->fetchAll();
    }
    public function vendedores(): array {
        return $this->PDO->query("SELECT u.id,u.nome,u.sobrenome,u.enderecoResidencial,u.telefone,v.numeroDoCartao,d.nome AS departamento FROM vendedores v JOIN usuarios u ON u.id=v.idDoUsuario JOIN departamentos d ON d.id=v.idDoDepartamento WHERE u.papel='VENDEDOR' AND u.activo=1 ORDER BY u.nome")->fetchAll();
    }
    public function relatorio(?string $inicio,?string $fim,?int $departamento,?int $categoria): array {
        $sql="SELECT v.id,v.dataDaVenda,p.nome AS produto,c.nome AS categoria,d.nome AS departamento,i.quantidade,(i.quantidade*i.precoUnitario) AS valorTotal, v.idDoVendedor FROM vendas v JOIN itensDeVenda i ON i.idDaVenda=v.id JOIN produtos p ON p.id=i.idDoProduto JOIN categorias c ON c.id=p.idDaCategoria JOIN departamentos d ON d.id=v.idDoDepartamento WHERE 1=1";
        $args=[];
        if ($inicio!==null) {$sql.=" AND v.dataDaVenda >= :inicio";$args['inicio']=$inicio.' 00:00:00';}
        if ($fim!==null) {$sql.=" AND v.dataDaVenda < DATE_ADD(:fim,INTERVAL 1 DAY)";$args['fim']=$fim;}
        if ($departamento!==null) {$sql.=" AND v.idDoDepartamento=:departamento";$args['departamento']=$departamento;}
        if ($categoria!==null) {$sql.=" AND p.idDaCategoria=:categoria";$args['categoria']=$categoria;}
        $sql.=" ORDER BY v.dataDaVenda DESC,v.id DESC";
        $s=$this->PDO->prepare($sql);$s->execute($args); return $s->fetchAll();
    }
    public function produtividade(?string $inicio,?string $fim): array {
        $sql="SELECT u.id,CONCAT(u.nome,' ',u.sobrenome) AS vendedor,d.nome AS departamento,COUNT(v.id) AS quantidadeDeVendas,COALESCE(SUM(v.valorTotal),0) AS valorTotal FROM vendedores vd JOIN usuarios u ON u.id=vd.idDoUsuario JOIN departamentos d ON d.id=vd.idDoDepartamento LEFT JOIN vendas v ON v.idDoVendedor=vd.idDoUsuario";
        $args=[];
        if($inicio!==null){$sql.=" AND v.dataDaVenda>=:inicio";$args['inicio']=$inicio.' 00:00:00';}
        if($fim!==null){$sql.=" AND v.dataDaVenda < DATE_ADD(:fim, INTERVAL 1 DAY)";$args['fim']=$fim;}
        $sql.=" WHERE u.papel='VENDEDOR' AND u.activo=1 GROUP BY u.id,u.nome,u.sobrenome,d.nome ORDER BY valorTotal DESC";
        $s=$this->PDO->prepare($sql);$s->execute($args);return $s->fetchAll();
    }
    public function produtosMaisVendidos():array {
        return $this->PDO->query("SELECT p.id,p.nome,p.precoDeVenda,SUM(i.quantidade) AS vendidos FROM itensDeVenda i JOIN produtos p ON p.id=i.idDoProduto GROUP BY p.id,p.nome,p.precoDeVenda ORDER BY vendidos DESC LIMIT 3")->fetchAll();
    }
    public function solicitar(int $id,string $cartao,int $departamento):array {
        $this->PDO->beginTransaction();
        try {
            $s=$this->PDO->prepare("SELECT papel,activo FROM usuarios WHERE id=:id FOR UPDATE");$s->execute(['id'=>$id]);$u=$s->fetch();
            if(!$u || !$u['activo'] || $u['papel']!=='CLIENTE')throw new DomainException('Apenas clientes activos podem solicitar.',403);
            $s=$this->PDO->prepare("SELECT 1 FROM departamentos WHERE id=:id AND activo=1");$s->execute(['id'=>$departamento]);if(!$s->fetchColumn())throw new InvalidArgumentException('Departamento inválido.',422);
            $s=$this->PDO->prepare("SELECT id FROM solicitacoesDeVendedor WHERE idDoUsuario=:id AND estado='PENDENTE' LIMIT 1");$s->execute(['id'=>$id]);if($s->fetch())throw new DomainException('Já existe um pedido pendente.',409);
            $s=$this->PDO->prepare("INSERT INTO solicitacoesDeVendedor(idDoUsuario,numeroDoCartao,idDoDepartamento) VALUES(:id,:cartao,:departamento)");$s->execute(['id'=>$id,'cartao'=>$cartao,'departamento'=>$departamento]);
            $idPedido=(int)$this->PDO->lastInsertId();$this->PDO->commit();return ['id'=>$idPedido,'estado'=>'PENDENTE'];
        }catch(Throwable $e){if($this->PDO->inTransaction())$this->PDO->rollBack();throw $e;}
    }
    public function pedidos():array {
        return $this->PDO->query("SELECT s.id,s.idDoUsuario,s.numeroDoCartao,s.idDoDepartamento,s.estado,s.dataDaSolicitacao,CONCAT(u.nome,' ',u.sobrenome) AS nome,u.email,u.telefone,d.nome AS departamento FROM solicitacoesDeVendedor s JOIN usuarios u ON u.id=s.idDoUsuario JOIN departamentos d ON d.id=s.idDoDepartamento ORDER BY (s.estado='PENDENTE') DESC,s.dataDaSolicitacao DESC")->fetchAll();
    }
    public function decidir(int $idPedido,int $idAdmin,string $decisao):array {
        $this->PDO->beginTransaction();
        try {
            $s=$this->PDO->prepare("SELECT * FROM solicitacoesDeVendedor WHERE id=:id FOR UPDATE");$s->execute(['id'=>$idPedido]);$p=$s->fetch();
            if(!$p || $p['estado']!=='PENDENTE')throw new DomainException('Pedido inexistente ou já decidido.',409);
            // Idempotência e controlo de papel sob bloqueio da linha do utilizador.
            $s=$this->PDO->prepare("SELECT papel,activo FROM usuarios WHERE id=:id FOR UPDATE");$s->execute(['id'=>$p['idDoUsuario']]);$u=$s->fetch();
            if(!$u || !$u['activo'] || $u['papel']!=='CLIENTE')throw new DomainException('O utilizador já não pode ser promovido.',409);
            if($decisao==='APROVADA') {
                $s=$this->PDO->prepare("INSERT INTO vendedores(idDoUsuario,numeroDoCartao,idDoDepartamento) VALUES(:usuario,:cartao,:departamento)");$s->execute(['usuario'=>$p['idDoUsuario'],'cartao'=>$p['numeroDoCartao'],'departamento'=>$p['idDoDepartamento']]);
                $s=$this->PDO->prepare("UPDATE usuarios SET papel='VENDEDOR' WHERE id=:id");$s->execute(['id'=>$p['idDoUsuario']]);
                $s=$this->PDO->prepare("UPDATE solicitacoesDeVendedor SET estado='REJEITADA',idDoAdministrador=:admin,dataDaDecisao=NOW() WHERE idDoUsuario=:usuario AND estado='PENDENTE' AND id<>:pedido");$s->execute(['admin'=>$idAdmin,'usuario'=>$p['idDoUsuario'],'pedido'=>$idPedido]);
            }
            $s=$this->PDO->prepare("UPDATE solicitacoesDeVendedor SET estado=:estado,idDoAdministrador=:admin,dataDaDecisao=NOW() WHERE id=:id");$s->execute(['estado'=>$decisao,'admin'=>$idAdmin,'id'=>$idPedido]);
            $this->PDO->commit();return ['id'=>$idPedido,'estado'=>$decisao];
        }catch(Throwable $e){if($this->PDO->inTransaction())$this->PDO->rollBack();throw $e;}
    }

    public function catalogosDeProdutos(): array {
        return [
            'categorias' => $this->PDO->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(),
            'departamentos' => $this->PDO->query("SELECT id, nome, idDaCategoria FROM departamentos WHERE activo=1 ORDER BY nome")->fetchAll(),
            'materiais' => $this->PDO->query("SELECT id, nome FROM materiais WHERE activo=1 ORDER BY nome")->fetchAll(),
            'materiaisDeAlca' => $this->PDO->query("SELECT id, nome FROM materiaisDeAlca WHERE activo=1 ORDER BY nome")->fetchAll()
        ];
    }

    public function criarProduto(array $dados): array {
        $this->PDO->beginTransaction();
        try {
            $categoria = $this->PDO->prepare("SELECT c.nome FROM categorias c JOIN departamentos d ON d.idDaCategoria=c.id WHERE c.id=:categoria AND d.id=:departamento AND d.activo=1");
            $categoria->execute(['categoria'=>$dados['idDaCategoria'], 'departamento'=>$dados['idDoDepartamento']]);
            $nomeCategoria = $categoria->fetchColumn();
            if (!$nomeCategoria) throw new InvalidArgumentException('Categoria e departamento incompatíveis ou inexistentes.',422);

            $c = $dados['caracteristicas'];
            $obrigatorios = match ($nomeCategoria) {
                'COLARES', 'PULSEIRAS' => ['comprimentoEmCm', 'idDoMaterial'],
                'ANEIS' => ['diametroEmMm', 'idDoMaterial'],
                'RELOGIOS' => ['idDoMaterialDaAlca', 'tipoDeFecho', 'sexo', 'fabricante'],
                'BRINCOS' => ['idDoMaterial'],
                'PEGAS' => ['idDoMaterial', 'tipoDeFecho'],
                default => throw new InvalidArgumentException('Categoria ainda não suportada.',422)
            };
            foreach ($obrigatorios as $campo) {
                if (!isset($c[$campo]) || $c[$campo] === '') {
                    throw new InvalidArgumentException("Característica obrigatória: {$campo}.",422);
                }
            }
            if ($nomeCategoria !== 'ANEIS' && isset($c['quilates'])) {
                throw new InvalidArgumentException('Quilates só é permitido em anéis.',422);
            }
            if (isset($c['quilates']) && empty($c['tipoDePedra'])) {
                throw new InvalidArgumentException('Indique o tipo de pedra do anel de diamante.',422);
            }

            $sql = "INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo) VALUES (:categoria,:departamento,:nome,:descricao,:marca,:custo,:preco,:stock,0,1)";
            $stmt = $this->PDO->prepare($sql);
            $stmt->execute([
                'categoria'=>$dados['idDaCategoria'], 'departamento'=>$dados['idDoDepartamento'],
                'nome'=>$dados['nome'], 'descricao'=>$dados['descricao'], 'marca'=>$dados['marca'],
                'custo'=>$dados['custoDeAquisicao'], 'preco'=>$dados['precoDeVenda'], 'stock'=>$dados['stockFisico']
            ]);
            $id = (int)$this->PDO->lastInsertId();
            $c = $dados['caracteristicas'];
            $material = $c['idDoMaterial'] ?? null;
            $diametro = $c['diametroEmMm'] ?? null;
            $comprimento = $c['comprimentoEmCm'] ?? null;
            $pedra = $c['possuiPedra'] ?? false;
            $tipoPedra = $pedra ? ($c['tipoDePedra'] ?? null) : null;
            switch ($nomeCategoria) {
                case 'COLARES':
                    $this->inserirCaracteristicas("INSERT INTO colares(idDoProduto,comprimentoEmCm,idDoMaterial) VALUES(?,?,?)",[$id,$comprimento,$material]);
                    break;
                case 'PULSEIRAS':
                    $this->inserirCaracteristicas("INSERT INTO pulseiras(idDoProduto,comprimentoEmCm,idDoMaterial) VALUES(?,?,?)",[$id,$comprimento,$material]);
                    break;
                case 'ANEIS':
                    $this->inserirCaracteristicas("INSERT INTO aneis(idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra) VALUES(?,?,?,?,?)",[$id,$diametro,$material,(int)$pedra,$tipoPedra]);
                    if (isset($c['quilates'])) {
                        $this->inserirCaracteristicas("INSERT INTO aneisDeDiamante(idDoProduto,quilates) VALUES(?,?)",[$id,$c['quilates']]);
                    }
                    break;
                case 'RELOGIOS':
                    $this->inserirCaracteristicas("INSERT INTO relogios(idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante) VALUES(?,?,?,?,?)",[$id,$c['idDoMaterialDaAlca'],$c['tipoDeFecho'],$c['sexo'],$c['fabricante']]);
                    break;
                case 'BRINCOS':
                    $this->inserirCaracteristicas("INSERT INTO brincos(idDoProduto,idDoMaterial,possuiPedra,tipoDePedra) VALUES(?,?,?,?)",[$id,$material,(int)$pedra,$tipoPedra]);
                    break;
                case 'PEGAS':
                    $this->inserirCaracteristicas("INSERT INTO pegas(idDoProduto,idDoMaterial,tipoDeFecho) VALUES(?,?,?)",[$id,$material,$c['tipoDeFecho']]);
                    break;
                default:
                    throw new InvalidArgumentException('Categoria sem registo específico implementado.',422);
            }
            $this->PDO->commit();
            return ['id'=>$id,'nome'=>$dados['nome'],'stockFisico'=>$dados['stockFisico']];
        } catch (Throwable $e) {
            if ($this->PDO->inTransaction()) $this->PDO->rollBack();
            throw $e;
        }
    }

    private function inserirCaracteristicas(string $sql, array $valores): void {
        $this->PDO->prepare($sql)->execute($valores);
    }

    public function abastecerProduto(int $id, int $quantidade): array {
        $stmt = $this->PDO->prepare("UPDATE produtos SET stockFisico=stockFisico+:quantidade WHERE id=:id AND activo=1 AND stockFisico<=2147483647-:limite");
        $stmt->execute(['quantidade'=>$quantidade,'limite'=>$quantidade,'id'=>$id]);
        if ($stmt->rowCount() !== 1) throw new DomainException('Produto inexistente ou quantidade ultrapassa o limite do stock.',409);
        $consulta=$this->PDO->prepare("SELECT id,stockFisico,stockReservado FROM produtos WHERE id=:id");
        $consulta->execute(['id'=>$id]);
        return $consulta->fetch();
    }

    public function categoriaDoProduto(int $id): string {
        $stmt = $this->PDO->prepare("SELECT c.nome FROM produtos p JOIN categorias c ON c.id=p.idDaCategoria WHERE p.id=:id AND p.activo=1");
        $stmt->execute(['id'=>$id]);
        $nome = $stmt->fetchColumn();
        if (!$nome) throw new DomainException('Produto não encontrado.',409);
        return strtoupper((string)$nome);
    }

    public function guardarImagemProduto(int $id, string $caminho): array {
        $this->PDO->beginTransaction();
        try {
            $stmt=$this->PDO->prepare("SELECT id FROM produtos WHERE id=:id AND activo=1 FOR UPDATE");
            $stmt->execute(['id'=>$id]);
            if (!$stmt->fetch()) throw new DomainException('Produto não encontrado.',409);
            $this->PDO->prepare("UPDATE imagensDeProdutos SET imagemPrincipal=0 WHERE idDoProduto=:id")->execute(['id'=>$id]);
            $stmt=$this->PDO->prepare("INSERT INTO imagensDeProdutos(idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao) VALUES(:id,:caminho,1,0)");
            $stmt->execute(['id'=>$id,'caminho'=>$caminho]);
            $this->PDO->commit();
            return ['idDoProduto'=>$id,'caminhoDaImagem'=>$caminho];
        } catch (Throwable $e) {
            if ($this->PDO->inTransaction()) $this->PDO->rollBack();
            throw $e;
        }
    }

}
