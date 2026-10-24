
import { Api } from "./padrao/Api.js";

const imagemPrincipal = document.getElementById("imagem-principal");
const nomeProduto = document.getElementById("nome-produto");
const precoProduto = document.getElementById("preco-produto");
const descricaoProduto = document.getElementById("descricao-produto");
const menssagemProduto=document.getElementById("menssagem-produto")

const informacoesProduto = document.getElementById("informacoes-produto");
const opcoesProduto = document.getElementById("opcoes-produto");
const produtosRelacionados = document.getElementById("lista-relacionados");


const quantidadeProduto = document.querySelector(".quantidade");
const aumentarBtn = document.getElementById("aumentar");
const diminuirBtn = document.getElementById("diminuir");
const adicionarBtn = document.querySelector(".adicionar");

let produtoActual = null;

function obterIdProduto() {
    const parametros = new URLSearchParams(window.location.search);
    const id = parametros.get("id");
    if (!id || !/^[1-9][0-9]*$/.test(id)) {
        return null;
    }
    const numero = Number(id);
    // if (!Number.isSafeInteger(numero)) {
    //     return null;
    // }
    return numero;
}

async function carregarProduto() {
    const id = obterIdProduto();
    if (id === null) {
        menssagemProduto.textContent = "Produto inválido.";
        return;
    }
    try {
        const resposta = await Api.enviarRequisicao( `/produtos/${id}`, null, "GET" );
        if (!resposta.dados) {
            throw new Error("Produto não encontrado.");
        }
        produtoActual = resposta.dados;
        console.log(produtoActual);
        
        mostrarProduto(produtoActual);
        await carregarProdutosRelacionados(produtoActual);
    } catch (erro) {
        console.error(erro);
        menssagemProduto.textContent = "Não foi possível carregar o produto.";
        produtosRelacionados.textContent = "";
    }
}

function mostrarProduto(produto) {
    nomeProduto.textContent = produto.nome;
    precoProduto.textContent ="PRECO: "+ Number(produto.precoDeVenda)
        .toLocaleString("pt-MZ", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    descricaoProduto.textContent = produto.descricao || "";

    const imagens = produto.imagens ?? [];

    imagemPrincipal.src = imagens[0]?.caminhoDaImagem || "";
    imagemPrincipal.alt = produto.nome;
    imagemPrincipal.classList.remove("apareceu");
    mostrarCaracteristicas(produto);
    const disponivel = Math.max(
        0,
        Number(produto.stockDisponivel)
    );

    quantidadeProduto.value = 1;
    quantidadeProduto.max = disponivel;
    aumentarBtn.disabled = disponivel === 0;
    diminuirBtn.disabled = disponivel === 0;
    quantidadeProduto.disabled = disponivel === 0;
    adicionarBtn.disabled = disponivel === 0;

    if (disponivel === 0) {
        adicionarBtn.textContent = "Produto indisponível";
    } else {
        adicionarBtn.textContent = "Adicionar ao carrinho";
    }
}

function mostrarCaracteristicas(produto) {

    informacoesProduto.replaceChildren();
    opcoesProduto.replaceChildren();

    adicionarInformacao("Marca", produto.marca);
    adicionarInformacao("Categoria", produto.categoria);
    adicionarInformacao("Quanatidade Disponvel", produto.stockDisponivel);
    const caracteristicas = produto.caracteristicas ?? {};

    const nomesCampos = {
        material: "Material",
        comprimentoEmCm: "Comprimento (cm)",
        diametroEmMm: "Diâmetro (mm)",
        possuiPedra: "Possui pedra",
        tipoDePedra: "Tipo de pedra",
        quilates: "Quilates",
        materialDaAlca: "Material da alça",
        tipoDeFecho: "Tipo de fecho",
        sexo: "Sexo",
        fabricante: "Fabricante"
    };

    for (const [campo, valor] of Object.entries(caracteristicas)) {
        if (valor === null || valor === "") {
            continue;
        }
        let valorApresentado = valor;
        if (campo === "possuiPedra") {
            valorApresentado = Number(valor) === 1 ? "Sim" : "Não";
        }
        const nomeCampo = nomesCampos[campo] ?? campo;
        adicionarInformacao(nomeCampo, valorApresentado);
    }
    opcoesProduto.hidden = true;
}



function adicionarInformacao(nome, valor) {

    if (valor === null || valor === undefined || valor === "") {
        return;
    }
    const paragrafo = document.createElement("p");
    const titulo = document.createElement("span");
    titulo.classList.add("esq");
    titulo.textContent = `${nome}: `;

    const conteudo = document.createElement("span");
    conteudo.textContent = String(valor);

    paragrafo.appendChild(titulo);
    paragrafo.appendChild(conteudo);

    informacoesProduto.appendChild(paragrafo);
}

async function carregarProdutosRelacionados(produto) {
    try {
        const resposta = await Api.enviarRequisicao(
            "/produtos",
            null,
            "GET"
        );
        const todosProdutos = resposta.dados;
        if (!Array.isArray(todosProdutos)) {
            throw new Error("Lista de produtos inválida.");
        }
        const relacionados = todosProdutos
            .filter(outroProduto =>
                outroProduto.categoria === produto.categoria &&
                Number(outroProduto.id) !== Number(produto.id)
            )
            .slice(0, 10);
        mostrarProdutosRelacionados(relacionados);
    } catch (erro) {
        console.error("Erro ao carregar produtos relacionados:", erro);
        produtosRelacionados.textContent ="Não foi possível carregar produtos relacionados.";
    }
}

function mostrarProdutosRelacionados(produtos) {
    produtosRelacionados.replaceChildren();
    if (produtos.length === 0) {
        produtosRelacionados.textContent =
            "Não existem produtos relacionados.";
        return;
    }
    produtos.forEach(produto => {
        const imagem = document.createElement("img");
        imagem.classList.add("produto-img");
        imagem.src = produto.imagemPrincipal || "";
        imagem.alt = produto.nome;
        imagem.title = produto.nome;
        imagem.loading = "lazy";

        imagem.addEventListener("click", () => {
            window.location.href =
                `/frontend/pages/produto.html?id=${produto.id}`;
        });
        produtosRelacionados.appendChild(imagem);
    });
}

function aumentarQuantidade() {
    if (!produtoActual) {
        return;
    }
    const disponivel = Number(produtoActual.stockDisponivel);

    let quantidade = Number(quantidadeProduto.value);

    if (quantidade < disponivel) {
        quantidade += 1;
    }
    quantidadeProduto.value = quantidade;
}

function diminuirQuantidade() {
    let quantidade = Number(quantidadeProduto.value);
    quantidade -= 1;
    if (quantidade < 1) {
        quantidade = 1;
    }
    quantidadeProduto.value = quantidade;
}

function validarQuantidade() {
    if (!produtoActual) {
        return;
    }
    const disponivel = Number(produtoActual.stockDisponivel);
    let quantidade = Number(quantidadeProduto.value);

    if (!Number.isInteger(quantidade) || quantidade < 1) {
        quantidade = 1;
    }
    if (quantidade > disponivel) {
        quantidade = disponivel;
    }
    quantidadeProduto.value = quantidade;
}

aumentarBtn.addEventListener("click", aumentarQuantidade);
diminuirBtn.addEventListener("click", diminuirQuantidade);

quantidadeProduto.addEventListener("change", validarQuantidade);

carregarProduto();
