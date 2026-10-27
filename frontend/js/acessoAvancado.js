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
    const senha = document.getElementById("admin-senha").value;

    if (email === "" || senha === "") {
        Mensagens.menssagemErroSucesso("e", "Campos obrigatórios");
        return;
    }

    const dados = { email, senha };

    const botao = formularioADM.querySelector("#admin-login");
    botao.disabled = true;

    try {
        const resposta = await Api.enviarRequisicao(
            "/auth/login/admin",
            dados,
            "POST"
        );

        Mensagens.menssagemErroSucesso("s",
            resposta.menssagem ?? "Login realizado com sucesso!",
        );

        setTimeout(() => {
            window.location.assign("/frontend/pages/adm.html");
        }, 1000);

    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        console.error(erro);

    } finally {
        botao.disabled = false;
    }
});

formularioVendedor.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("vendedor-email").value.trim();
    const senha = document.getElementById("vendedor-senha").value;

    if (email === "" || senha === "") {
        Mensagens.menssagemErroSucesso("e", "Campos obrigatórios");
        return;
    }

    const dados = { email, senha };

    const botao = formularioVendedor.querySelector("#vendedor-login");
    botao.disabled = true;

    try {
        const resposta = await Api.enviarRequisicao(
            "/auth/login/vendedor",
            dados,
            "POST"
        );

        Mensagens.menssagemErroSucesso("s",
            resposta.menssagem ?? "Login realizado com sucesso!",
        );

        setTimeout(() => {
            window.location.assign("/frontend/pages/vendedor.html");
        }, 1000);

    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        console.error(erro);

    } finally {
        botao.disabled = false;
    }
});


const emailCandidatura = document.getElementById("cadastrarvendedor-email");
const departamentoCandidatura = document.getElementById("departamento");

async function carregarFormularioCandidatura() {
    document.getElementById("cadastrovendedor-login").disabled=false;
    const usuario = await Api.enviarRequisicao("/auth/eu", null, "GET");
    const perfil = usuario.dados;
    if (perfil.papel !== "CLIENTE") {
        document.getElementById("cadastrovendedor-login").disabled=true;
        throw new Error("A candidatura está disponível apenas para clientes.");

    }
    // const departamentos = await Api.enviarRequisicao("/departamentos", null, "GET");
    // departamentoCandidatura.replaceChildren(new Option("Seleccionar departamento", ""));
    // departamentos.dados.filter(departamento => Number(departamento.activo) === 1).forEach(departamento => {
    //     departamentoCandidatura.add(new Option(departamento.nome, String(departamento.id)));
    // });
    return perfil;
}

cadastrarVendedorbtn.addEventListener("click", async () => {
    try {
        const perfil = await carregarFormularioCandidatura();
        emailCandidatura.value = perfil.email;
    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        console.error(erro.message)
    }
});

formularioCadastroVendedor.addEventListener("submit", async evento => {
    evento.preventDefault();
    const botao = formularioCadastroVendedor.querySelector('button[type="submit"]');
    botao.disabled = true;
    try {
        await carregarFormularioCandidatura();
        const resposta = await Api.enviarRequisicao("/solicitacoes/vendedor", {
            numeroDoCartao: document.getElementById("numeroDoCartao").value.trim(),
            idDoDepartamento: Number(departamentoCandidatura.value)
        }, "POST");
        Mensagens.menssagemErroSucesso("s", resposta.menssagem);
        formularioCadastroVendedor.reset();
    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        console.error(erro.message)
    } finally {
        botao.disabled = false;
    }
});
