
import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js";
import { Sessao } from "./padrao/Sessao.js";

const listaReservas = document.querySelector(".lista-reservas");
// const detalhesReserva = document.querySelector(".detalhes-reserva");
// const itensReserva = document.getElementById("itens-reserva");
// const fecharDetalhesBtn = document.getElementById("fechar-detalhes");

let minhasReservas = [];

function formatarPreco(valor) {
    return Number(valor).toLocaleString("pt-MZ", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + " MT";
}

function formatarData(data) {
    if (!data) {
        return "";
    }
    const dataFormatada = new Date(data.replace(" ", "T"));
    if (Number.isNaN(dataFormatada.getTime())) {
        return data;
    }
    return dataFormatada.toLocaleString("pt-MZ");
}


async function carregarMinhasReservas() {
    listaReservas.textContent = "A carregar reservas...";
    try {
        const resposta = await Api.enviarRequisicao(
            "/reservas/minhas",
            null,
            "GET"
        );
        if (!Array.isArray(resposta.dados)) {
            throw new Error("Lista de reservas inválida.");
        }
        minhasReservas = resposta.dados;
        mostrarReservas();
        const parametros = new URLSearchParams(window.location.search);
        const id = parametros.get("id");

        if (id && /^[1-9][0-9]*$/.test(id)) {
            await mostrarDetalhesReserva(Number(id));
        }

    } catch (erro) {
        console.error(erro);
        listaReservas.textContent ="Não foi possível carregar as suas reservas.";
       Mensagens.menssagemErroSucesso("s",erro.message);
    }
}

function mostrarReservas() {
    listaReservas.replaceChildren();
    if (minhasReservas.length === 0) {
        listaReservas.textContent ="Ainda não efectuou nenhuma reserva.";
        return;
    }
    minhasReservas.forEach(reserva => {
        const elemento = document.createElement("div");
        elemento.classList.add("reserva");
        elemento.dataset.idReserva = reserva.id;

        const numero = document.createElement("h3");
        numero.textContent = `Reserva nº ${reserva.id}`;

        const data = document.createElement("p");
        data.textContent = `Data: ${formatarData(reserva.dataDaReserva)}`;

        const estado = document.createElement("p");
        estado.classList.add("estado-reserva");
        estado.textContent = `Estado: ${reserva.estado}`;

        const total = document.createElement("p");
        total.textContent = `Total: ${formatarPreco(reserva.valorTotal)}`;

        const consultarBtn = document.createElement("button");
        consultarBtn.type = "button";
        consultarBtn.classList.add("consultar-reserva");
        consultarBtn.textContent = "Ver detalhes";

        elemento.appendChild(numero);
        elemento.appendChild(data);
        elemento.appendChild(estado);
        elemento.appendChild(total);
        elemento.appendChild(consultarBtn);

        if (reserva.estado === "PENDENTE") {
            const cancelarBtn = document.createElement("button");
            cancelarBtn.type = "button";
            cancelarBtn.classList.add("cancelar-reserva");
            cancelarBtn.textContent = "Cancelar reserva";
            elemento.appendChild(cancelarBtn);
        }
        listaReservas.appendChild(elemento);
    });
}



async function mostrarDetalhesReserva(idDaReserva) {
    const elementoReserva = listaReservas.querySelector(
        `.reserva[data-id-reserva="${idDaReserva}"]`
    );

    if (!elementoReserva) {
        return;
    }

    const detalhesExistentes = elementoReserva.querySelector(".detalhes-reserva");
    const botaoDetalhes = elementoReserva.querySelector(".consultar-reserva");

    if (detalhesExistentes) {
        detalhesExistentes.remove();
        botaoDetalhes.textContent = "Ver detalhes";
        return;
    }

    try {
        botaoDetalhes.disabled = true;

        const resposta = await Api.enviarRequisicao(
            `/reservas/${idDaReserva}`,
            null,
            "GET"
        );

        const reserva = resposta.dados;

        if (!reserva) {
            throw new Error("Reserva não encontrada.");
        }

        const detalhes = document.createElement("div");
        detalhes.classList.add("detalhes-reserva");

        const titulo = document.createElement("h2");
        titulo.textContent = "Detalhes da reserva";

        const itensReserva = document.createElement("div");
        itensReserva.classList.add("itens-reserva");

        detalhes.appendChild(titulo);

        reserva.itens.forEach(item => {

            const elemento = document.createElement("div");
            elemento.classList.add("item-reserva");

            const nome = document.createElement("h4");
            nome.textContent = item.nome;

            const quantidade = document.createElement("p");
            quantidade.textContent = `Quantidade: ${item.quantidade}`;

            const preco = document.createElement("p");
            preco.textContent =
                `Preço unitário: ${formatarPreco(item.precoUnitario)}`;

            const subtotal = document.createElement("p");
            subtotal.textContent =
                `Subtotal: ${formatarPreco(item.subtotal)}`;

            elemento.appendChild(nome);
            elemento.appendChild(quantidade);
            elemento.appendChild(preco);
            elemento.appendChild(subtotal);

            itensReserva.appendChild(elemento);
        });

        const total = document.createElement("h3");
        total.textContent =
            `Total da reserva: ${formatarPreco(reserva.valorTotal)}`;

        detalhes.appendChild(itensReserva);
        detalhes.appendChild(total);

        elementoReserva.insertBefore(detalhes, botaoDetalhes);

        botaoDetalhes.textContent = "Ocultar detalhes";

    } catch (erro) {
        console.error(erro);
        Mensagens.menssagemErroSucesso("e", erro.message);

    } finally {
        botaoDetalhes.disabled = false;
    }
}

async function cancelarReserva(idDaReserva) {
    const confirmar = confirm( "Tem a certeza de que pretende cancelar esta reserva?");
    if (!confirmar) {
        return;
    }
    try {
        const resposta = await Api.enviarRequisicao( `/reservas/${idDaReserva}/cancelar`, null, "DELETE");
     Mensagens.menssagemErroSucesso("s",resposta.menssagem);
        await carregarMinhasReservas();
    } catch (erro) {
        console.error(erro);
        Mensagens.menssagemErroSucesso("e",erro.menssagem);
    }
}

listaReservas.addEventListener("click", async evento => {
    const reserva = evento.target.closest(".reserva");
    if (!reserva) {
        return;
    }
    const id = Number(reserva.dataset.idReserva);
    if (evento.target.closest(".consultar-reserva")) {
        await mostrarDetalhesReserva(id);
    }
    if (evento.target.closest(".cancelar-reserva")) {
        await cancelarReserva(id);
    }
});

async function iniciarPagina() {
    const usuario = await Sessao.exigirLogin();
    if (!usuario) {
        return;
    }
    await carregarMinhasReservas();
}
iniciarPagina();
