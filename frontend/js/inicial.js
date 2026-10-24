import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js"
const abrirFiltros = document.getElementById("abrir-filtros");
const fecharFiltros = document.getElementById("fechar-filtros");
const blocoFiltros = document.querySelector(".bloco-filtros");


const caixaDeProdutos = document.querySelector(".caixaDeProdutos");
const pesquisa = document.getElementById("pesquisa");


const filtroCategoria = document.getElementById("filtro-categoria");
const abaixoDe = document.getElementById("abaixoDe");
const acimaDe = document.getElementById("acimaDe");
const aplicarFiltrosBtn = document.getElementById("aplicar-filtros");

abrirFiltros.addEventListener("click", abirOuFecharFiltros);
fecharFiltros.addEventListener("click", abirOuFecharFiltros);

function abirOuFecharFiltros() {
    blocoFiltros.classList.toggle("activo");
}

let todosProdutos = [];

async function carregarProdutos() {
    caixaDeProdutos.classList.remove("carregar")
    caixaDeProdutos.classList.add("carregar");
    caixaDeProdutos.textContent = "A carregar produtos...";
   
    setTimeout(async()=>{
        try {
        const resposta = await Api.enviarRequisicao(
            "/produtos",
            null,
            "GET"
        );
        if (!Array.isArray(resposta.dados)) {
            throw new Error("A API não devolveu uma lista de produtos.");
        }
        todosProdutos = resposta.dados;
        //console.log(todosProdutos[0]);
        caixaDeProdutos.classList.remove("carregar")
        mostrarProdutos(todosProdutos);
    } catch (erro) {
        console.error(erro);
        caixaDeProdutos.classList.add("carregar")
        caixaDeProdutos.textContent = "Não foi possível carregar os produtos.";
    }
    },1000  )
}

function criarCardProduto(produto) {
    const card = document.createElement("div");
    card.classList.add("card");
    card.dataset.idProduto = produto.id;
    card.classList.add("aparecer");
    const imagem = document.createElement("img");
    imagem.classList.add("produto-img");
    imagem.src = produto.imagemPrincipal || "";
    imagem.alt = produto.nome;

    const detalhes = document.createElement("div");
    detalhes.classList.add("detalhes-produto");

    const nome = document.createElement("p");
    nome.classList.add("produto-nome");
    nome.textContent = produto.nome;

    const precoParagrafo = document.createElement("p");
    precoParagrafo.classList.add("produto-preco");

    const strong = document.createElement("strong");
    const preco = document.createElement("span");
    preco.classList.add("preco");

    preco.textContent = Number(produto.precoDeVenda).toLocaleString("pt-mZ", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    strong.appendChild(preco);
    precoParagrafo.appendChild(strong);

    detalhes.appendChild(nome);
    detalhes.appendChild(precoParagrafo);

    const botao = document.createElement("button");
    botao.classList.add("adicionar-produto");
    botao.type = "button";
    botao.title = "Adicionar ao Carrinho";
    botao.dataset.idProduto = produto.id;

    const icone = document.createElement("img");
    icone.classList.add("icon");
    icone.src = "/icons/shopping-cart.png";
    icone.alt = "";

    botao.appendChild(icone);
    card.appendChild(imagem);
    card.appendChild(detalhes);
    card.appendChild(botao);
    return card;
}

function mostrarProdutos(produtos) {
    caixaDeProdutos.replaceChildren();
    if (produtos.length === 0) {
        caixaDeProdutos.classList.add("carregar");
        caixaDeProdutos.textContent = "Nenhum produto encontrado.";
        return;
    }
    caixaDeProdutos.classList.remove("carregar")
    const fragmento = document.createDocumentFragment();
    produtos.forEach(produto => {
        const card = criarCardProduto(produto);
        fragmento.appendChild(card);
    });
    caixaDeProdutos.appendChild(fragmento);
}

function filtrarProdutos() {
    const categoria = filtroCategoria.value;
    const textoPesquisa = pesquisa.value.trim().toLowerCase();

    const precoMinimo = acimaDe.value === "" ? null : Number(acimaDe.value);
    const precoMaximo = abaixoDe.value === "" ? null : Number(abaixoDe.value);
    if (
        precoMinimo !== null &&
        precoMaximo !== null &&
        precoMinimo > precoMaximo
    ) {
        Mensagens.mostrarMensagem(blocoFiltros,"O preço mínimo não pode ser maior que o preço máximo.",false,4000);

        return;
    }
    const produtosFiltrados = todosProdutos.filter(produto => {
        const preco = Number(produto.precoDeVenda);

        if (categoria !== "TODAS" && produto.categoria !== categoria) {
            return false;
        }

        const nome = String(produto.nome || "").toLowerCase();
        const marca = String(produto.marca || "").toLowerCase();

        if (
            !nome.includes(textoPesquisa) &&
            !marca.includes(textoPesquisa)
        ) {
            return false;
        }
        if (precoMinimo !== null && preco < precoMinimo) {
            return false;
        }
        if (precoMaximo !== null && preco > precoMaximo) {
            return false;
        }
        return true;
    });
    mostrarProdutos(produtosFiltrados);
    return true
}
pesquisa.addEventListener("input", filtrarProdutos);
aplicarFiltrosBtn.addEventListener("click", () => {
    const filtrado=filtrarProdutos();
    if(!filtrado){
      return;  
    }
    blocoFiltros.classList.remove("activo");
});

carregarProdutos();
caixaDeProdutos.addEventListener("click", (evento) => {
    if (evento.target.closest(".adicionar-produto")) {
        return;
    }
    const card = evento.target.closest(".card");
    if (!card) {
        return;
    }
    const id = card.dataset.idProduto;
    if (id) {
        window.location.href = `/frontend/pages/produto.html?id=${id}`;
    }
});