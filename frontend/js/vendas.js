
import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js";
import { AreaVendedor } from "./padrao/AreaVendedor.js";

const pagina = document.querySelector("main.vendas");

const formularioVenda = document.getElementById("formulario-venda");

const produtoSelect = document.getElementById("produto");
const quantidadeInput = document.getElementById("quantidade");
const dataInput = document.getElementById("data");

const registarBtn = document.querySelector(".registar-venda");

const corpoReservas = document.getElementById("corpo-reservas");
const corpoHistorico = document.getElementById("corpo-historico");

const valorVendasHoje = document.getElementById("valor-vendas-hoje");
const totalVendas = document.getElementById("total-vendas");
const reservasPendentes = document.getElementById("reservas-pendentes");

let produtos = [];
let reservas = [];
let vendas = [];


function mostrarMensagem(estado, mensagem) {
    Mensagens.menssagemErroSucesso(estado, mensagem);
}


function criarCelula(linha, valor) {
    const celula = document.createElement("td");

    celula.textContent = valor ?? "";

    linha.appendChild(celula);

    return celula;
}


function mostrarLinhaVazia(corpo, quantidadeColunas, mensagem) {

    corpo.replaceChildren();

    const linha = corpo.insertRow();
    const celula = linha.insertCell();

    celula.colSpan = quantidadeColunas;
    celula.className = "sem-dados";
    celula.textContent = mensagem;
}

function actualizarData() {
    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = String(hoje.getMonth() + 1).padStart(2, "0");
    const dia = String(hoje.getDate()).padStart(2, "0");

    dataInput.value = `${ano}-${mes}-${dia}`;
}
function actualizarLimiteQuantidade() {
    const idDoProduto = Number(produtoSelect.value);
    const produto = produtos.find(
        item => Number(item.id) === idDoProduto
    );
    if (!produto) {
        quantidadeInput.removeAttribute("max");
        return;
    }

    quantidadeInput.max = Number(produto.stockDisponivel);
}


async function carregarProdutos() {

    const resposta = await Api.enviarRequisicao(
        "/vendedor/inventario",
        null,
        "GET"
    );

    if (!Array.isArray(resposta.dados)) {
        throw new Error("A API não devolveu os produtos.");
    }

    produtos = resposta.dados;

    produtoSelect.replaceChildren();

    const inicial = document.createElement("option");

    inicial.value = "";
    inicial.textContent = "Seleccionar produto";

    produtoSelect.appendChild(inicial);

    produtos.forEach(produto => {

        const disponivel = Number(produto.stockDisponivel);

        const opcao = document.createElement("option");

        opcao.value = produto.id;

        opcao.textContent =
            `${produto.nome} (${disponivel} disponíveis)`;

        opcao.disabled = disponivel <= 0;

        produtoSelect.appendChild(opcao);
    });

    actualizarLimiteQuantidade();
}


async function carregarReservas() {

    const resposta = await Api.enviarRequisicao(
        "/vendedor/reservas",
        null,
        "GET"
    );

    if (!Array.isArray(resposta.dados)) {
        throw new Error("A API não devolveu as reservas.");
    }

    reservas = resposta.dados;

    corpoReservas.replaceChildren();

    if (reservas.length === 0) {

        mostrarLinhaVazia(
            corpoReservas,
            6,
            "Nenhuma reserva pendente neste departamento."
        );

        return;
    }

    reservas.forEach(reserva => {

        const linha = document.createElement("tr");

        criarCelula(linha, reserva.id);

        criarCelula(linha, reserva.cliente);

        criarCelula(
            linha,
            AreaVendedor.formatarData(reserva.dataDaReserva)
        );

        criarCelula(
            linha,
            AreaVendedor.formatarPreco(
                reserva.valorDoDepartamento
            )
        );

        criarCelula(linha, reserva.estado);

        const celulaAccao = document.createElement("td");

        const botao = document.createElement("button");

        botao.type = "button";
        botao.classList.add("finalizar-reserva");
        botao.dataset.idReserva = reserva.id;
        botao.textContent = "Finalizar";

        celulaAccao.appendChild(botao);
        linha.appendChild(celulaAccao);

        corpoReservas.appendChild(linha);
    });
}


async function carregarVendas() {

    const resposta = await Api.enviarRequisicao(
        "/vendedor/vendas",
        null,
        "GET"
    );

    if (!Array.isArray(resposta.dados)) {
        throw new Error("A API não devolveu as vendas.");
    }

    vendas = resposta.dados;

    corpoHistorico.replaceChildren();

    if (vendas.length === 0) {

        mostrarLinhaVazia(
            corpoHistorico,
            4,
            "Ainda não existem vendas registadas."
        );

        return;
    }

    vendas.forEach(venda => {

        const linha = document.createElement("tr");

        criarCelula(linha, venda.id);

        criarCelula(
            linha,
            AreaVendedor.formatarData(venda.dataDaVenda)
        );

        criarCelula(
            linha,
            venda.cliente || "Cliente presencial"
        );

        criarCelula(
            linha,
            AreaVendedor.formatarPreco(venda.valorTotal)
        );

        corpoHistorico.appendChild(linha);
    });
}


async function actualizarResumo() {

    const resposta = await Api.enviarRequisicao(
        "/vendedor/painel",
        null,
        "GET"
    );

    const dados = resposta.dados;

    if (!dados) {
        throw new Error("Não foi possível obter o resumo das vendas.");
    }

    const vendasHoje = dados.vendasHoje || {};

    valorVendasHoje.textContent =
        AreaVendedor.formatarPreco(vendasHoje.totalVendido);

    totalVendas.textContent = vendas.length;

    reservasPendentes.textContent = reservas.length;
}


async function actualizarDados() {

    await Promise.all([
        carregarProdutos(),
        carregarReservas(),
        carregarVendas()
    ]);

    await actualizarResumo();
}


async function actualizarDepoisDaVenda() {

    try {

        await actualizarDados();

    } catch (erro) {

        console.error("Erro ao actualizar a página:", erro);

        mostrarMensagem(
            "e",
            "A venda foi registada, mas não foi possível actualizar todos os dados. Actualize a página."
        );
    }
}


async function registarVenda() {

    const idDoProduto = Number(produtoSelect.value);
    const quantidade = Number(quantidadeInput.value);

    if (
        !Number.isInteger(idDoProduto) ||
        idDoProduto <= 0 ||
        !Number.isInteger(quantidade) ||
        quantidade <= 0
    ) {

        mostrarMensagem(
            "e",
            "Seleccione um produto e uma quantidade válida."
        );

        return;
    }

    const produto = produtos.find(
        item => Number(item.id) === idDoProduto
    );

    if (!produto) {

        mostrarMensagem("e", "Produto não encontrado.");
        return;
    }

    const disponivel = Number(produto.stockDisponivel);

    if (quantidade > disponivel) {

        mostrarMensagem(
            "e",
            "A quantidade pretendida ultrapassa o stock disponível."
        );

        return;
    }

    const valor = Number(produto.precoDeVenda) * quantidade;

    const confirmar = confirm(
        `Confirma que recebeu o pagamento e entregou ${quantidade} unidade(s) de ${produto.nome}?\n\n` +
        `Total: ${AreaVendedor.formatarPreco(valor)}`
    );

    if (!confirmar) {
        return;
    }

    registarBtn.disabled = true;

    let vendaRegistada = false;

    try {

        const resposta = await Api.enviarRequisicao(
            "/vendedor/vendas",
            {
                itens: [
                    {
                        idDoProduto,
                        quantidade
                    }
                ]
            },
            "POST"
        );

        vendaRegistada = true;

        mostrarMensagem(
            "s",
            resposta.menssagem || "Venda registada com sucesso!"
        );

        formularioVenda.reset();
        actualizarData();

    } catch (erro) {

        console.error(erro);

        mostrarMensagem(
            "e",
            erro.message || "Não foi possível registar a venda."
        );

    } finally {

        registarBtn.disabled = false;
    }

    if (vendaRegistada) {
        await actualizarDepoisDaVenda();
    }
}


async function finalizarReserva(idDaReserva, botao) {

    const reserva = reservas.find(
        item => Number(item.id) === idDaReserva
    );

    if (!reserva) {
        return;
    }

    const confirmar = confirm(
        `Reserva nº ${reserva.id}\n` +
        `Cliente: ${reserva.cliente}\n` +
        `Total deste departamento: ` +
        `${AreaVendedor.formatarPreco(reserva.valorDoDepartamento)}\n\n` +
        "Confirma que recebeu o pagamento e entregou todos os produtos pendentes deste departamento?"
    );

    if (!confirmar) {
        return;
    }

    botao.disabled = true;

    let reservaFinalizada = false;

    try {

        const resposta = await Api.enviarRequisicao(
            `/vendedor/reservas/${idDaReserva}/finalizar`,
            null,
            "POST"
        );

        reservaFinalizada = true;

        mostrarMensagem(
            "s",
            resposta.menssagem || "Reserva finalizada com sucesso!"
        );

    } catch (erro) {

        console.error(erro);

        mostrarMensagem(
            "e",
            erro.message || "Não foi possível finalizar a reserva."
        );

    } finally {

        botao.disabled = false;
    }

    if (reservaFinalizada) {
        await actualizarDepoisDaVenda();
    }
}


formularioVenda.addEventListener("submit", async evento => {

    evento.preventDefault();

    if (registarBtn.disabled) {
        return;
    }

    await registarVenda();
});


corpoReservas.addEventListener("click", async evento => {

    const botao = evento.target.closest(".finalizar-reserva");

    if (!botao || botao.disabled) {
        return;
    }

    const idDaReserva = Number(botao.dataset.idReserva);

    if (!Number.isInteger(idDaReserva) || idDaReserva <= 0) {
        return;
    }

    await finalizarReserva(idDaReserva, botao);
});


produtoSelect.addEventListener("change", () => {

    actualizarLimiteQuantidade();

});


formularioVenda.addEventListener("reset", () => {

    setTimeout(() => {
        actualizarLimiteQuantidade();
    }, 0);

});


async function iniciarPagina() {

    const usuario = await AreaVendedor.verificarAcesso();

    if (!usuario) {
        return;
    }

    actualizarData();

    try {

        await actualizarDados();

    } catch (erro) {
        console.error(erro);
        mostrarMensagem(
            "e",
            "Não foi possível carregar os dados das vendas."
        );
    }
}


iniciarPagina();
