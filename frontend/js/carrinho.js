
import { Api } from "./padrao/Api.js";
import { Carrinho } from "./padrao/Carrinho.js";
import { Mensagens } from "./padrao/Mensagens.js";

const listaDeProdutos = document.querySelector(".lista-produto");

const subtotal = document.getElementById("subtotal");
const quantidadeTotal = document.getElementById("quantidade-total");
const valorTotal = document.getElementById("valorTotal");
const finalizarBtn = document.querySelector(".finalizar");
const resumo = document.querySelector(".resumo");

let produtosDoCarrinho = [];
let existemProdutosIndisponiveis = false;

function formatarPreco(valor) {
    return Number(valor).toLocaleString("pt-MZ", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

async function carregarCarrinho() {
    existemProdutosIndisponiveis = false;

    listaDeProdutos.textContent = "A carregar carrinho...";
    finalizarBtn.disabled = true;
    try {
        const itensGuardados = Carrinho.listarProdutos();
        if (itensGuardados.length === 0) {
            produtosDoCarrinho = [];
            mostrarProdutos();
            return;
        }
        const resposta = await Api.enviarRequisicao(
            "/produtos",
            null,
            "GET"
        );
        if (!Array.isArray(resposta.dados)) {
            throw new Error("A API não devolveu uma lista válida.");
        }
        const todosProdutos = resposta.dados;
        produtosDoCarrinho = itensGuardados.map(item => {
            const produto = todosProdutos.find(
                produto => Number(produto.id) === Number(item.idDoProduto)
            );
            if (!produto) {
                return null;
            }
            return { ...produto, quantidade: Number(item.quantidade) };

        }).filter(produto => produto !== null);

        existemProdutosIndisponiveis = produtosDoCarrinho.length !== itensGuardados.length;

        mostrarProdutos();

        if (existemProdutosIndisponiveis) {
            Mensagens.mostrarMensagem(
                resumo,
                "Alguns produtos do carrinho já não estão disponíveis no catálogo.",
                false,
                5000
            );
        }

    } catch (erro) {
        console.error(erro);

        listaDeProdutos.textContent = "Não foi possível carregar o carrinho.";

        finalizarBtn.disabled = true;
    }
}

function criarProduto(produto) {
    const elemento = document.createElement("div");
    elemento.classList.add("produto");
    elemento.dataset.idProduto = produto.id;

    const remover = document.createElement("button");
    remover.classList.add("remover");
    remover.type = "button";
    remover.title = "Remover do carrinho";
    remover.dataset.idProduto = produto.id;

    const icone = document.createElement("img");
    icone.src = "/icons/lixeira-de-reciclagem.png";
    icone.alt = "Remover";
    icone.classList.add("icon");

    remover.appendChild(icone);

    const imagem = document.createElement("img");
    imagem.classList.add("produto-img");
    imagem.src = produto.imagemPrincipal || "";
    imagem.alt = produto.nome;

    const detalhes = document.createElement("div");
    detalhes.classList.add("detalhes");

    const nome = document.createElement("h3");
    nome.textContent = produto.nome;

    const preco = document.createElement("p");
    preco.classList.add("preco");
    preco.textContent = formatarPreco(produto.precoDeVenda);

    const quantidade = document.createElement("input");
    quantidade.type = "number";
    quantidade.classList.add("quantidade-item");
    quantidade.min = 1;
    quantidade.step = 1;
    quantidade.value = produto.quantidade;
    quantidade.dataset.idProduto = produto.id;
    const disponivel = Math.max(
        0,
        Number(produto.stockDisponivel)
    );
    quantidade.max = disponivel;

    const totalItem = document.createElement("p");
    totalItem.classList.add("total-item");

    totalItem.textContent = "Total: " + formatarPreco(
        Number(produto.precoDeVenda) * produto.quantidade
    );

    detalhes.appendChild(nome);
    detalhes.appendChild(preco);
    detalhes.appendChild(quantidade);
    detalhes.appendChild(totalItem);

    if (disponivel === 0) {
        const aviso = document.createElement("p");
        aviso.textContent = "Produto indisponível";
        aviso.classList.add("aviso-stock");
        detalhes.appendChild(aviso);

    } else if (produto.quantidade > disponivel) {
        const aviso = document.createElement("p");
        aviso.textContent = `Disponíveis: ${disponivel}`;
        aviso.classList.add("aviso-stock");
        detalhes.appendChild(aviso);
    }
    elemento.appendChild(remover);
    elemento.appendChild(imagem);
    elemento.appendChild(detalhes);

    return elemento;
}

function mostrarProdutos() {
    listaDeProdutos.classList.remove("menssagem");
    listaDeProdutos.replaceChildren();

    if (produtosDoCarrinho.length === 0) {
        listaDeProdutos.classList.add("menssagem");
        listaDeProdutos.textContent = "O seu carrinho está vazio!.";
        actualizarResumo();
        return;
    }
    const fragmento = document.createDocumentFragment();
    produtosDoCarrinho.forEach(produto => {
        fragmento.appendChild(criarProduto(produto));
    });
    listaDeProdutos.appendChild(fragmento);
    actualizarResumo();
}

function actualizarResumo() {
    let quantidade = 0;
    let total = 0;
    let quantidadesValidas = true;
    produtosDoCarrinho.forEach(produto => {
        quantidade += produto.quantidade;
        total += Number(produto.precoDeVenda) * produto.quantidade;
        const disponivel = Number(produto.stockDisponivel);
        if (
            !Number.isInteger(produto.quantidade) ||
            produto.quantidade < 1 ||
            produto.quantidade > disponivel
        ) {
            quantidadesValidas = false;
        }
    });
    quantidadeTotal.textContent = quantidade;
    subtotal.textContent = formatarPreco(total);
    valorTotal.textContent = formatarPreco(total);
    finalizarBtn.disabled = produtosDoCarrinho.length === 0 ||  !quantidadesValidas || existemProdutosIndisponiveis;
}
function removerProduto(idDoProduto) {
    Carrinho.removerProduto(idDoProduto);
    produtosDoCarrinho = produtosDoCarrinho.filter(
        produto => Number(produto.id) !== Number(idDoProduto)
    );
    mostrarProdutos();
}

function alterarQuantidade(idDoProduto, novaQuantidade) {
    const produto = produtosDoCarrinho.find(
        produto => Number(produto.id) === Number(idDoProduto)
    );
    if (!produto) {
        return;
    }
    const quantidade = Number(novaQuantidade);
    const disponivel = Number(produto.stockDisponivel);
    if (
        !Number.isInteger(quantidade) ||
        quantidade < 1 ||
        quantidade > disponivel
    ) {
        Mensagens.mostrarMensagem(
            resumo,
            `A quantidade deve estar entre 1 e ${disponivel}.`,
            false,
            4000
        );
        mostrarProdutos();
        return;
    }
    Carrinho.alterarQuantidade(idDoProduto, quantidade);
    produto.quantidade = quantidade;
    mostrarProdutos();
}


function removerProdutosIndisponiveis() {
    const produtosValidos = produtosDoCarrinho.map(produto => ({
        idDoProduto: Number(produto.id),
        quantidade: produto.quantidade
    }));
    Carrinho.guardarProdutos(produtosValidos);
    existemProdutosIndisponiveis = false;
    mostrarProdutos();
}


listaDeProdutos.addEventListener("click", evento => {
    const botaoRemover = evento.target.closest(".remover");
    if (botaoRemover) {
        const id = Number(botaoRemover.dataset.idProduto);
        removerProduto(id);
    }
});


listaDeProdutos.addEventListener("change", evento => {
    if (evento.target.classList.contains("quantidade-item")) {
        const id = Number(evento.target.dataset.idProduto);
        alterarQuantidade(id, evento.target.value);
    }
});



finalizarBtn.addEventListener("click", async () => {
    if (finalizarBtn.disabled || produtosDoCarrinho.length === 0) {
        return;
    }
    const itens = produtosDoCarrinho.map(produto => ({
        idDoProduto: Number(produto.id),
        quantidade: Number(produto.quantidade)
    }));
    finalizarBtn.disabled = true;
    try {
        const resposta = await Api.enviarRequisicao("/reservas",{itens}, "POST");
        setTimeout(()=>{
        finalizarBtn.textContent = "A efectuar reserva...";
        Mensagens.menssagemErroSucesso("s",resposta.menssagem);
        },1000);
        Carrinho.limparCarrinho();
        carregarCarrinho();
        finalizarBtn.textContent = "Reservar";
        window.location.href = `/frontend/pages/reservas.html?id=${resposta.dados.id}`;
    } catch (erro) {
        console.error(erro);
        Mensagens.menssagemErroSucesso("e",erro.message);
        actualizarResumo();
    } 
});



carregarCarrinho();
