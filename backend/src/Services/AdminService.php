<?php
declare(strict_types=1);
namespace APP\Services;
use APP\Repositories\AdminRepositorio;
use InvalidArgumentException;
use DomainException;
class AdminService {
    public function __construct(private AdminRepositorio $repo){}
    public function perfil(int $id):array {return $this->repo->perfil($id) ?? throw new DomainException('Administrador não encontrado.',403);}
    public function departamentos():array{return $this->repo->departamentos();}
    public function inventario():array{return $this->repo->inventario();}
    public function vendas():array{return $this->repo->vendas();}
    public function vendedores():array{return $this->repo->vendedores();}
    public function produtosMaisVendidos():array{return $this->repo->produtosMaisVendidos();}
    public function pedidos():array{return $this->repo->pedidos();}
    public function produtividade(?string $inicio,?string $fim):array {return $this->repo->produtividade($this->data($inicio),$this->data($fim));}
    public function relatorio(?string $inicio,?string $fim,?int $departamento,?int $categoria):array {
        $inicio=$this->data($inicio);$fim=$this->data($fim);
        if($inicio && $fim && $inicio>$fim)throw new InvalidArgumentException('Intervalo de datas inválido.',422);
        foreach([$departamento,$categoria] as $id)if($id!==null && $id<1)throw new InvalidArgumentException('Filtro inválido.',422);
        return $this->repo->relatorio($inicio,$fim,$departamento,$categoria);
    }
    private function data(?string $d):?string {if($d===null || $d==='')return null;$v=\DateTimeImmutable::createFromFormat('!Y-m-d',$d);if(!$v || $v->format('Y-m-d')!==$d)throw new InvalidArgumentException('Data inválida.',422);return $d;}
    public function solicitar(int $id,array $dados):array {
        $cartao=$dados['numeroDoCartao']??null;$dep=$dados['idDoDepartamento']??null;
        if(!is_string($cartao)||trim($cartao)===''||strlen(trim($cartao))>50||!is_int($dep)||$dep<1)throw new InvalidArgumentException('Número do cartão e departamento são obrigatórios.',422);
        return $this->repo->solicitar($id,trim($cartao),$dep);
    }
    public function decidir(int $id,int $admin,array $dados):array {
        $decisao=$dados['decisao']??null;
        if($id<1 || !in_array($decisao,['APROVADA','REJEITADA'],true))throw new InvalidArgumentException('Decisão inválida.',422);
        return $this->repo->decidir($id,$admin,$decisao);
    }

    public function catalogosDeProdutos(): array { return $this->repo->catalogosDeProdutos(); }

    public function criarProduto(array $dados): array {
        foreach (['idDaCategoria','idDoDepartamento','nome','descricao','marca','custoDeAquisicao','precoDeVenda','stockFisico','caracteristicas'] as $campo) {
            if (!array_key_exists($campo,$dados)) throw new InvalidArgumentException("Campo obrigatório: {$campo}.",422);
        }
        foreach (['idDaCategoria','idDoDepartamento','stockFisico'] as $campo) {
            if (!is_int($dados[$campo]) || $dados[$campo] < ($campo==='stockFisico'?0:1)) throw new InvalidArgumentException("{$campo} inválido.",422);
        }
        foreach (['nome'=>150,'descricao'=>5000,'marca'=>120] as $campo=>$limite) {
            if (!is_string($dados[$campo]) || trim($dados[$campo])==='' || mb_strlen($dados[$campo])>$limite) throw new InvalidArgumentException("{$campo} inválido.",422);
            $dados[$campo]=trim($dados[$campo]);
        }
        foreach (['custoDeAquisicao','precoDeVenda'] as $campo) {
            if (!is_int($dados[$campo]) && !is_float($dados[$campo]) && !(is_string($dados[$campo]) && is_numeric($dados[$campo]))) throw new InvalidArgumentException("{$campo} inválido.",422);
            $valor=(float)$dados[$campo];
            if (!is_finite($valor) || $valor<0 || $valor>9999999999.99) throw new InvalidArgumentException("{$campo} inválido.",422);
            $dados[$campo]=round($valor,2);
        }
        if (!is_array($dados['caracteristicas']) || array_is_list($dados['caracteristicas'])) throw new InvalidArgumentException('Características inválidas.',422);
        $c=$dados['caracteristicas'];
        foreach (['idDoMaterial','idDoMaterialDaAlca'] as $campo) if (isset($c[$campo]) && (!is_int($c[$campo]) || $c[$campo]<1)) throw new InvalidArgumentException("{$campo} inválido.",422);
        foreach (['comprimentoEmCm','diametroEmMm','quilates'] as $campo) if (isset($c[$campo]) && (!is_numeric($c[$campo]) || (float)$c[$campo]<=0)) throw new InvalidArgumentException("{$campo} inválido.",422);
        if (isset($c['possuiPedra']) && !is_bool($c['possuiPedra'])) throw new InvalidArgumentException('possuiPedra deve ser booleano.',422);
        if (($c['possuiPedra']??false) && (empty($c['tipoDePedra']) || !is_string($c['tipoDePedra']))) throw new InvalidArgumentException('Informe o tipo de pedra.',422);
        if (isset($c['quilates']) && (!($c['possuiPedra']??false))) throw new InvalidArgumentException('Anel de diamante deve possuir pedra.',422);
        if (isset($c['sexo']) && !in_array($c['sexo'],['MASCULINO','FEMININO','UNISSEX'],true)) throw new InvalidArgumentException('Sexo inválido.',422);
        foreach (['tipoDeFecho','fabricante','tipoDePedra'] as $campo) if (isset($c[$campo]) && (!is_string($c[$campo]) || mb_strlen($c[$campo])>120)) throw new InvalidArgumentException("{$campo} inválido.",422);
        return $this->repo->criarProduto($dados);
    }

    public function abastecerProduto(int $id, array $dados): array {
        $quantidade=$dados['quantidade']??null;
        if ($id<1 || !is_int($quantidade) || $quantidade<1 || $quantidade>1000000) throw new InvalidArgumentException('A quantidade deve ser um inteiro positivo até 1.000.000.',422);
        return $this->repo->abastecerProduto($id,$quantidade);
    }

    public function categoriaDoProduto(int $id): string { return $this->repo->categoriaDoProduto($id); }

    public function guardarImagemProduto(int $id,string $caminho): array {
        if ($id<1) throw new InvalidArgumentException('Produto inválido.',422);
        return $this->repo->guardarImagemProduto($id,$caminho);
    }

}
