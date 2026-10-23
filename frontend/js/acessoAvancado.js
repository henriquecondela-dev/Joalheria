import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js"
//abas
const ladoAdm = document.querySelector(".ladoAdm");
const ladoVendedor = document.querySelector(".ladoVendedor");
const ladoCadastrar = document.querySelector(".ladoCadastrar");

//formularois
const formularioADM = document.querySelector(".admin-form");
const formularioVendedor = document.querySelector(".vendedor-form");
const formularioCadastroVendedor = document.querySelector(".cadastrarvendedor-form");


//botoess
const entrarAdmbtn = document.getElementById("entrarAdm");
const entrarVendedorbtn = document.getElementById("entrarVendedor");
const cadastrarVendedorbtn = document.getElementById("cadastrarVendedor");


entrarAdmbtn.addEventListener("click", () => {
    if (ladoVendedor.classList.contains("activo")) {
        ladoVendedor.classList.remove("activo");
    }
    ladoAdm.classList.toggle("activo");
});
entrarVendedorbtn.addEventListener("click", () => {
    if (ladoAdm.classList.contains("activo")) {
        ladoAdm.classList.remove("activo");
    }
    ladoVendedor.classList.toggle("activo");
});
cadastrarVendedorbtn.addEventListener("click", () => {
    console.log("clicou")
    if (ladoAdm.classList.contains("activo") || ladoVendedor.classList.contains("activo")) {
        ladoAdm.classList.remove("activo");
        ladoVendedor.classList.remove("activo");
    }
    ladoCadastrar.classList.toggle("activo");

})
document.addEventListener("click", (evento) => {
    const clicouNoFormulario = ladoAdm.contains(evento.target) || ladoVendedor.contains(evento.target) || ladoCadastrar.contains(evento.target);

    const clicouNoBotao =
        entrarAdmbtn.contains(evento.target) ||
        entrarVendedorbtn.contains(evento.target) ||
        cadastrarVendedorbtn.contains(evento.target);
    if (!clicouNoFormulario && !clicouNoBotao) {
        ladoAdm.classList.remove("activo");
        ladoVendedor.classList.remove("activo");
        ladoCadastrar.classList.remove("activo");
    }
});


formularioADM.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("admin-email").value.trim();
    const senha = document.getElementById("admin-senha").value.trim();
    if (email === "" || senha === "") {
        Mensagens.mostrarMensagem(formularioADM, "Campos obrigatorios");
        return;
    }
    const dados = { email: email, senha: senha };
    const botao = formularioADM.querySelector("#admin-login");
    botao.disable = true;
    try {
        const resposta = await Api.enviarRequisicao("/auth/login/admin", dados, "POST");
        Mensagens.mostrarMensagem(formularioADM, resposta.menssagem, true);
        setTimeout(() => {
            window.location.assign(`/frontend/pages/adm.html?usuario=${resposta.dados.id}`);
        }, 1000);
    } catch (erro) {
        Mensagens.mostrarMensagem(formularioADM, erro.message);
        console.error(erro.message);
    } finally {
        botao.disable = false;
    }
})
formularioVendedor.addEventListener("submit", async (e) => {
    e.preventDefault()
    const email = document.getElementById("vendedor-email").value.trim();
    const senha = document.getElementById("vendedor-senha").value.trim();
    if (email === "" || senha === "") {
        Mensagens.mostrarMensagem(formularioADM, "Campos obrigatorios");
        return;
    }
    const dados = { email: email, senha: senha };
    const botao = formularioADM.querySelector("#vendedor-login");
    botao.disable = true;
    try {
        const resposta = await Api.enviarRequisicao("/auth/login/vendedor", dados, "POST");
        Mensagens.mostrarMensagem(formularioADM, resposta.menssagem, true);
        setTimeout(() => {
            window.location.assign(`/frontend/pages/vendedor.html?usuario=${resposta.dados.id}`);
        }, 1000);
    } catch (erro) {
        Mensagens.mostrarMensagem(formularioADM, erro.message);
        console.error(erro.message);
    } finally {
        botao.disable = false;
    }
})