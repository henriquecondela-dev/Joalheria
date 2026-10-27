import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js";

const corpoPedidos = document.getElementById("corpo-pedidos-vendedores");

function mostrarEstado(mensagem) {
    corpoPedidos.replaceChildren();
    const linha = corpoPedidos.insertRow();
    const celula = linha.insertCell();
    celula.colSpan = 8;
    celula.textContent = mensagem;
}

async function carregarPedidos() {
    const resposta = await Api.enviarRequisicao("/admin/solicitacoes/vendedor?estado=PENDENTE");
    const pedidos = resposta.dados;
    corpoPedidos.replaceChildren();
    if (!pedidos.length) {
        mostrarEstado("Não existem candidaturas pendentes.");
        return;
    }
    pedidos.forEach(pedido => {
        const linha = corpoPedidos.insertRow();
        [pedido.id, pedido.idDoUsuario, pedido.nomeCompleto, pedido.email,
         pedido.numeroDoCartao, pedido.departamento, pedido.dataDoPedido].forEach(valor => {
            linha.insertCell().textContent = valor ?? "";
        });
        const accoes = linha.insertCell();
        const aprovar = document.createElement("button");
        aprovar.type = "button";
        aprovar.textContent = "Aprovar";
        aprovar.dataset.id = pedido.id;
        aprovar.dataset.decisao = "APROVADA";
        const rejeitar = document.createElement("button");
        rejeitar.type = "button";
        rejeitar.textContent = "Rejeitar";
        rejeitar.dataset.id = pedido.id;
        rejeitar.dataset.decisao = "REJEITADA";
        accoes.append(aprovar, rejeitar);
    });
}

corpoPedidos.addEventListener("click", async evento => {
    const botao = evento.target.closest("button[data-decisao]");
    if (!botao || botao.disabled) return;
    const decisao = botao.dataset.decisao;
    const id = Number(botao.dataset.id);
    if (!Number.isInteger(id) || id <= 0) return;
    if (!confirm(`${decisao === "APROVADA" ? "Aprovar" : "Rejeitar"} a candidatura nº ${id}?`)) return;
    let motivo = "";
    if (decisao === "REJEITADA") {
        const texto = prompt("Motivo da rejeição (opcional):", "");
        if (texto === null) return;
        motivo = texto.trim();
    }
    botao.disabled = true;
    try {
        await Api.enviarRequisicao(`/admin/solicitacoes/vendedor/${id}/decidir`, { decisao, motivo }, "POST");
        Mensagens.menssagemErroSucesso("s", "Decisão registada com sucesso.");
        await carregarPedidos();
    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        botao.disabled = false;
    }
});

carregarPedidos().catch(erro => {
    console.error(erro);
    mostrarEstado("Não foi possível carregar as candidaturas. Verifica a sessão do administrador.");
});
