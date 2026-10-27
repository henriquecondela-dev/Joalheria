
import { Api } from "./padrao/Api.js";
import { AreaVendedor } from "./padrao/AreaVendedor.js";

const tabela = document.querySelector(".inventario .produtos table");
const pesquisa = document.querySelector(".inventario .cabecalho input");

let produtos = [];

function criarCelula(linha, valor, classe = "") {
    const celula = document.createElement("td");
    celula.textContent = valor;

    if (classe) {
        celula.classList.add(classe);
    }

    linha.appendChild(celula);
}

function mostrarProdutos(lista) {
    const corpo = tabela.tBodies[0] || tabela.createTBody();
    corpo.replaceChildren();

    if (lista.length === 0) {
        const linha = corpo.insertRow();
        const celula = linha.insertCell();

        celula.colSpan = 5;
        celula.className = "sem-dados";
        celula.textContent = "Nenhum produto encontrado.";
        return;
    }

    lista.forEach(produto => {
        const quantidade = Number(produto.stockDisponivel);

        let estado = "Disponível";
        let classe = "disponivel";

        if (quantidade <= 0) {
            estado = "Esgotado";
            classe = "esgotado";
        } else if (quantidade <= 5) {
            estado = "Stock baixo";
            classe = "baixo";
        }

        const linha = document.createElement("tr");

        criarCelula(linha, produto.id);
        criarCelula(linha, produto.nome);
        criarCelula(
            linha,
            AreaVendedor.formatarPreco(produto.precoDeVenda)
        );
        criarCelula(linha, quantidade);
        criarCelula(linha, estado, classe);

        corpo.appendChild(linha);
    });
}

async function carregarInventario() {
    const usuario = await AreaVendedor.verificarAcesso();

    if (!usuario) return;

    try {
        const resposta = await Api.enviarRequisicao(
            "/vendedor/inventario",
            null,
            "GET"
        );

        if (!Array.isArray(resposta.dados)) {
            throw new Error("A API não devolveu uma lista de produtos.");
        }

        produtos = resposta.dados;
        mostrarProdutos(produtos);

    } catch (erro) {
        console.error(erro);
        mostrarProdutos([]);
    }
}

pesquisa.addEventListener("input", () => {
    const texto = pesquisa.value.trim().toLowerCase();

    const filtrados = produtos.filter(produto => {
        return String(produto.nome).toLowerCase().includes(texto) ||
            String(produto.marca || "").toLowerCase().includes(texto);
    });

    mostrarProdutos(filtrados);
});

carregarInventario();
