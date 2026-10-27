
export class Carrinho {
    static chave = "carrinhoJoalheria";
    static listarProdutos() {
        const dados = localStorage.getItem(this.chave);
        return dados ? JSON.parse(dados) : [];
    }
    static guardarProdutos(produtos) {
        localStorage.setItem(this.chave, JSON.stringify(produtos));
    }
    static adicionarProduto(idDoProduto, quantidade = 1) {
        idDoProduto = Number(idDoProduto);
        quantidade = Number(quantidade);
        if (
            !Number.isSafeInteger(idDoProduto) || idDoProduto < 1 || !Number.isSafeInteger(quantidade) || quantidade < 1) {
            throw new Error("Produto ou quantidade inválida.");
        }
        const produtos = this.listarProdutos();
        const produtoExistente = produtos.find(
            produto => produto.idDoProduto === idDoProduto
        );
        if (produtoExistente) {
            produtoExistente.quantidade += quantidade;
        } else {
            produtos.push({
                idDoProduto,
                quantidade
            });
        }
        this.guardarProdutos(produtos);
    }
    static removerProduto(idDoProduto) {
        const produtos = this.listarProdutos().filter(
            produto => produto.idDoProduto !== Number(idDoProduto)
        );
        this.guardarProdutos(produtos);
    }
    static limparCarrinho() {
        localStorage.removeItem(this.chave);
    }

    static alterarQuantidade(idDoProduto, quantidade) {
        idDoProduto = Number(idDoProduto);
        quantidade = Number(quantidade);
        if (
            !Number.isSafeInteger(idDoProduto) ||
            !Number.isSafeInteger(quantidade) ||
            quantidade < 1
        ) {
            throw new Error("Quantidade inválida.");
        }
        const produtos = this.listarProdutos();
        const produto = produtos.find(
            produto => produto.idDoProduto === idDoProduto
        );
        if (!produto) {
            return;
        }
        produto.quantidade = quantidade;
        this.guardarProdutos(produtos);
    }
}
