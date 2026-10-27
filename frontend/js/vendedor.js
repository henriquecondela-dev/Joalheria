
import { Api } from "./padrao/Api.js";
import { AreaVendedor } from "./padrao/AreaVendedor.js";

const painel = document.querySelector("main.vendedor");

function definirTexto(selector, valor) {
    const elemento = painel.querySelector(selector);

    if (elemento) {
        elemento.textContent = valor;
    }
}

function preencherVendasRecentes(vendas) {
    const tabela = painel.querySelector(".tabela table");
    const corpo = tabela.tBodies[0] || tabela.createTBody();

    corpo.replaceChildren();

    if (!vendas.length) {
        const linha = corpo.insertRow();
        const celula = linha.insertCell();

        celula.colSpan = 4;
        celula.textContent = "Ainda não existem vendas registadas.";
        return;
    }

    vendas.forEach(venda => {
        const linha = corpo.insertRow();

        const valores = [
            AreaVendedor.formatarData(venda.dataDaVenda),
            venda.produto,
            venda.quantidade,
            AreaVendedor.formatarPreco(venda.valor)
        ];

        valores.forEach(valor => {
            const celula = linha.insertCell();
            celula.textContent = valor;
        });
    });
}

function preencherDestaques(produtos) {
    const destaques = painel.querySelector(".destaques");
    destaques.replaceChildren();

    if (!produtos.length) {
        destaques.textContent = "Ainda não existem produtos em destaque.";
        return;
    }

    produtos.forEach(produto => {
        const card = document.createElement("div");
        card.className = "card";

        const imagem = document.createElement("img");
        imagem.className = "produto-img";
        imagem.src = produto.imagemPrincipal || "/icons/product.png";
        imagem.alt = produto.nome;

        const detalhes = document.createElement("div");
        detalhes.className = "detalhes-produto";

        const nome = document.createElement("p");
        nome.className = "produto-nome";
        nome.textContent = produto.nome;

        const preco = document.createElement("p");
        preco.className = "produto-preco";
        preco.textContent = AreaVendedor.formatarPreco(
            produto.precoDeVenda
        );

        detalhes.append(nome, preco);
        card.append(imagem, detalhes);
        destaques.appendChild(card);
    });
}

async function carregarPainel() {
    const usuario = await AreaVendedor.verificarAcesso();

    if (!usuario) return;

    try {
        const resposta = await Api.enviarRequisicao(
            "/vendedor/painel",
            null,
            "GET"
        );

        const dados = resposta.dados;
        const vendasHoje = dados.vendasHoje || {};

        definirTexto(
            ".vendas.cards h1",
            Number(vendasHoje.quantidadeDeVendas || 0)
        );

        definirTexto(
            ".vendas.cards p:last-child",
            AreaVendedor.formatarPreco(vendasHoje.totalVendido)
        );

        definirTexto(
            ".produtos-disponiveis h1",
            dados.produtosDisponiveis || 0
        );

        definirTexto(
            ".total-vendido h1",
            AreaVendedor.formatarPreco(vendasHoje.totalVendido)
        );

        definirTexto(
            ".departamento.cards h1",
            dados.departamento || "Não atribuído"
        );

        preencherVendasRecentes(dados.vendasRecentes || []);
        preencherDestaques(dados.produtosDestaque || []);

    } catch (erro) {
        console.error(erro);

        painel.querySelector(".registo-vendas").textContent =
            "Não foi possível carregar os dados do painel.";
    }
}

if (painel) {
    carregarPainel();
}
