
import { Api } from "./padrao/Api.js";
import { Sessao } from "./padrao/Sessao.js";
import { Mensagens } from "./padrao/Mensagens.js";

const corpoVendedores = document.getElementById("corpo-vendedores");
const corpoProdutividade = document.getElementById("corpo-produtividade");
const corpoPedidos = document.getElementById("corpo-pedidos-vendedores");

const periodoSelect = document.getElementById("periodo");

function mostrarMensagem(estado, mensagem) {
    Mensagens.menssagemErroSucesso(estado, mensagem);
}

function criarCelula(linha, valor) {
    const celula = linha.insertCell();
    celula.textContent = valor ?? "Não informado";
    return celula;
}

function mostrarLinhaVazia(corpo, colunas, mensagem) {
    corpo.replaceChildren();

    const linha = corpo.insertRow();
    const celula = linha.insertCell();

    celula.colSpan = colunas;
    celula.className = "sem-dados";
    celula.textContent = mensagem;
}

function formatarPreco(valor) {
    return Number(valor || 0).toLocaleString("pt-MZ", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + " MT";
}

function formatarData(valor) {
    if (!valor) return "Não informado";

    const data = new Date(String(valor).replace(" ", "T"));

    if (Number.isNaN(data.getTime())) {
        return String(valor);
    }

    return data.toLocaleDateString("pt-MZ");
}

function dataSQL(data) {
    const ano = data.getFullYear();
    const mes = String(data.getMonth() + 1).padStart(2, "0");
    const dia = String(data.getDate()).padStart(2, "0");

    return `${ano}-${mes}-${dia}`;
}

function obterPeriodo() {
    const hoje = new Date();
    const inicio = new Date(
        hoje.getFullYear(),
        hoje.getMonth(),
        hoje.getDate()
    );

    const fim = new Date(inicio);

    switch (periodoSelect.value) {
        case "semanal":
            const diaSemana = (inicio.getDay() + 6) % 7;
            inicio.setDate(inicio.getDate() - diaSemana);
            break;

        case "mensal":
            inicio.setDate(1);
            break;
    }

    return {
        inicio: dataSQL(inicio),
        fim: dataSQL(fim)
    };
}
   
async function carregarVendedores() {
    try {
        const resposta = await Api.enviarRequisicao(
            "/admin/vendedores",
            null,
            "GET"
        );

        const vendedores = resposta.dados;

        if (!Array.isArray(vendedores)) {
            throw new Error("A API não devolveu uma lista de vendedores.");
        }

        corpoVendedores.replaceChildren();

        if (vendedores.length === 0) {
            mostrarLinhaVazia(
                corpoVendedores,
                5,
                "Ainda não existem vendedores registados."
            );
            return;
        }

        vendedores.forEach(vendedor => {
            const linha = corpoVendedores.insertRow();

            criarCelula(linha, vendedor.numeroDoCartao);

            criarCelula(
                linha,
                [vendedor.nome, vendedor.sobrenome]
                    .filter(Boolean)
                    .join(" ")
            );

            criarCelula(linha, vendedor.enderecoResidencial);
            criarCelula(linha, vendedor.telefone);
            criarCelula(linha, vendedor.departamento);
        });

    } catch (erro) {
        console.error("Erro ao carregar vendedores:", erro);

        mostrarLinhaVazia(
            corpoVendedores,
            5,
            "Não foi possível carregar os vendedores."
        );

        mostrarMensagem("e", erro.message);
    }
}

async function carregarProdutividade() {
    try {
        const periodo = obterPeriodo();

        const parametros = new URLSearchParams({
            inicio: periodo.inicio,
            fim: periodo.fim
        });

        const resposta = await Api.enviarRequisicao(
            `/admin/produtividade?${parametros.toString()}`,
            null,
            "GET"
        );

        const produtividade = resposta.dados;

        if (!Array.isArray(produtividade)) {
            throw new Error("A API não devolveu os dados de produtividade.");
        }

        corpoProdutividade.replaceChildren();

        if (produtividade.length === 0) {
            mostrarLinhaVazia(
                corpoProdutividade,
                4,
                "Não existem vendas no período seleccionado."
            );
            return;
        }

        produtividade.forEach(vendedor => {
            const linha = corpoProdutividade.insertRow();

            criarCelula(linha, vendedor.vendedor);
            criarCelula(linha, vendedor.departamento);

            criarCelula(
                linha,
                Number(vendedor.quantidadeDeVendas || 0)
            );

            criarCelula(
                linha,
                formatarPreco(vendedor.valorTotal)
            );
        });

    } catch (erro) {
        console.error("Erro ao carregar produtividade:", erro);

        mostrarLinhaVazia(
            corpoProdutividade,
            4,
            "Não foi possível carregar a produtividade."
        );

        mostrarMensagem("e", erro.message);
    }
}

async function carregarPedidos() {
    try {
        const resposta = await Api.enviarRequisicao(
            "/admin/solicitacoes",
            null,
            "GET"
        );

        const pedidos = resposta.dados;

        if (!Array.isArray(pedidos)) {
            throw new Error("A API não devolveu uma lista de pedidos.");
        }

        corpoPedidos.replaceChildren();

        if (pedidos.length === 0) {
            mostrarLinhaVazia(
                corpoPedidos,
                8,
                "Ainda não existem candidaturas."
            );
            return;
        }

        pedidos.forEach(pedido => {
            const linha = corpoPedidos.insertRow();

            criarCelula(linha, pedido.id);
            criarCelula(linha, pedido.idDoUsuario);

            criarCelula(
                linha,
                [pedido.nome, pedido.sobrenome]
                    .filter(Boolean)
                    .join(" ")
            );

            criarCelula(linha, pedido.email);
            criarCelula(linha, pedido.numeroDoCartao);
            criarCelula(linha, pedido.departamento);
            criarCelula(linha, formatarData(pedido.criadoEm));

            const celulaAccoes = linha.insertCell();

            if (pedido.estado !== "PENDENTE") {
                celulaAccoes.textContent = pedido.estado;
                return;
            }

            const aprovarBtn = document.createElement("button");
            aprovarBtn.type = "button";
            aprovarBtn.textContent = "Aprovar";
            aprovarBtn.dataset.id = pedido.id;
            aprovarBtn.dataset.decisao = "APROVADA";

            const rejeitarBtn = document.createElement("button");
            rejeitarBtn.type = "button";
            rejeitarBtn.textContent = "Rejeitar";
            rejeitarBtn.dataset.id = pedido.id;
            rejeitarBtn.dataset.decisao = "REJEITADA";

            celulaAccoes.append(aprovarBtn, rejeitarBtn);
        });

    } catch (erro) {
        console.error("Erro ao carregar pedidos:", erro);

        mostrarLinhaVazia(
            corpoPedidos,
            8,
            "Não foi possível carregar as candidaturas."
        );

        mostrarMensagem("e", erro.message);
    }
}

async function decidirPedido(idDoPedido, decisao, botao) {
    const texto = decisao === "APROVADA"
        ? "aprovação"
        : "rejeição";

    if (!confirm(`Confirma a ${texto} do pedido nº ${idDoPedido}?`)) {
        return;
    }

    const linha = botao.closest("tr");
    const botoes = linha.querySelectorAll("button[data-decisao]");

    botoes.forEach(item => item.disabled = true);

    try {
        const resposta = await Api.enviarRequisicao(
            `/admin/solicitacoes/${idDoPedido}/decidir`,
            { decisao },
            "POST"
        );

        mostrarMensagem(
            "s",
            resposta.menssagem || "Pedido decidido com sucesso!"
        );

        await Promise.all([
            carregarPedidos(),
            carregarVendedores(),
            carregarProdutividade()
        ]);

    } catch (erro) {
        console.error("Erro ao decidir pedido:", erro);
        mostrarMensagem("e", erro.message);

    } finally {
        botoes.forEach(item => item.disabled = false);
    }
}

corpoPedidos.addEventListener("click", async evento => {
    const botao = evento.target.closest("button[data-decisao]");

    if (!botao || botao.disabled) return;

    const idDoPedido = Number(botao.dataset.id);
    const decisao = botao.dataset.decisao;

    if (!Number.isInteger(idDoPedido) || idDoPedido <= 0) {
        return;
    }

    if (!["APROVADA", "REJEITADA"].includes(decisao)) {
        return;
    }

    await decidirPedido(idDoPedido, decisao, botao);
});

periodoSelect.addEventListener("change", carregarProdutividade);

async function iniciarPagina() {
    try {
        const usuario = await Sessao.obterUsuario();

        if (!usuario) {
            window.location.replace("/frontend/pages/acessoAvancado.html");
            return;
        }

        if (String(usuario.papel).toUpperCase() !== "ADMIN") {
            window.location.replace("/frontend/pages/inicial.html");
            return;
        }

        await Promise.all([
            carregarVendedores(),
            carregarProdutividade(),
            carregarPedidos()
        ]);

    } catch (erro) {
        console.error("Erro ao iniciar a página:", erro);
        mostrarMensagem("e", erro.message);
    }
}

iniciarPagina();
