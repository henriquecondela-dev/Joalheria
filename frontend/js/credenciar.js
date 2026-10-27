import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js"

const habilitar_cadastro = document.getElementById("ir_cadastrar");
const habilitar_login = document.getElementById("ir_login");
const habilitar_recuperar = document.getElementById("ir_recuperar");
const habilitar_login2 = document.getElementById("ir_login2");

const formulario_login = document.querySelector(".section-Login");
const formulario_cadastro = document.querySelector(".section-cadastro");
const formulario_recuperar = document.querySelector(".recuperar-senha");

const recuperarForm = document.querySelector(".recuperar-form");
const loginForm = document.querySelector(".login-form");
const cadastroForm = document.querySelector(".cadastro-form");
habilitar_login2.addEventListener("click", (e) => {
    e.preventDefault();
    formulario_recuperar.style.display = "none";
    formulario_cadastro.style.display = "none";
    formulario_login.style.display = "block";


})
habilitar_recuperar.addEventListener("click", (e) => {
    e.preventDefault();
    formulario_cadastro.style.display = "none";
    formulario_login.style.display = "none";
    formulario_recuperar.style.display = "block";

})
habilitar_login.addEventListener("click", (e) => {
    e.preventDefault();
    formulario_cadastro.style.display = "none";
    formulario_login.style.display = "block";
});

habilitar_cadastro.addEventListener("click", (e) => {
    e.preventDefault();
    formulario_login.style.display = "none";
    formulario_cadastro.style.display = "block";
});

// formulario de entrar, fazer login
loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const senha = document.getElementById("senha").value.trim();
    if (!email || !senha) {
        // Mensagens.mostrarMensagem(loginForm, );
        Mensagens.menssagemErroSucesso("e","Email e senha sao obrigatorios");
        return;
    }
    const dados = {
        email: email,
        senha: senha
    };

    const botao = loginForm.querySelector('#login');
    botao.disabled = true;

    try {
        const resultado = await Api.enviarRequisicao("/auth/login", dados, "POST");
        
        Mensagens.menssagemErroSucesso("s",resultado.menssagem ?? "Login realizado com sucesso!");
        console.log("Utilizador autenticado:", resultado.dados);
        setTimeout(() => {

            const paginaAnterior = sessionStorage.getItem("paginaAposLogin");
            sessionStorage.removeItem("paginaAposLogin");
            const destino = paginaAnterior?.startsWith("/frontend/pages/") ? paginaAnterior : "/frontend/pages/inicial.html";
                
            window.location.assign(destino);

        }, 1000);
    } catch (erro) {
        Mensagens.menssagemErroSucesso("e",erro.message);
        console.error(erro.message)
    } finally {
        botao.disabled = false;
    }
});

recuperarForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const senha = document.getElementById("recuperar-senha").value.trim();
    const numeroDoBI = document.getElementById("numeroDeBi").value.trim();
    if (!senha || !numeroDoBI) {
        // Mensagens.mostrarMensagem(recuperarForm, );
         Mensagens.menssagemErroSucesso("e","Email e Numero De BI obrigatorios");
        return;
    }
    const dados = {
        senha: senha,
        numeroDoBI: numeroDoBI
    };

    const botao = recuperarForm.querySelector('#recuperar');
    botao.disabled = true;

    try {
        const resultado = await Api.enviarRequisicao("/auth/recuperarSenha", dados, "POST");
        
        Mensagens.menssagemErroSucesso("s",resultado.menssagem ?? "Senha Actualizada");
        console.log("Utilizador autenticado:", resultado.dados);
        setTimeout(() => {
            formulario_recuperar.style.display = "none";
            formulario_cadastro.style.display = "none";
            formulario_login.style.display = "block";
        }, 1000)

    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        console.error(erro.message)
    } finally {
        botao.disabled = false;
    }
});


// formulario de crair conta
cadastroForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const nome = document.getElementById("nome").value.trim();
    const sobrenome = document.getElementById("sobrenome").value.trim();
    const numeroDoBI = document.getElementById("n_bi").value.trim();
    const email = document.getElementById("cadastro-email").value.trim();
    const enderecoResidencial = document.getElementById("endereco").value.trim();
    const telefone = document.getElementById("telefone").value.trim();
    const localDeTrabalhoOuEstudo = document.getElementById("local_trabalho").value.trim();
    const genero = document.getElementById("genero").value;
    const dataDeNascimento = document.getElementById("dataDeNascimento").value;
    const senha = document.getElementById("cadastro-senha").value;

    if (!email || !senha || !nome || !sobrenome || !numeroDoBI || !telefone || !genero || !dataDeNascimento) {
        // Mensagens.mostrarMensagem(loginForm, );
         Mensagens.menssagemErroSucesso("e", "Prenchas todas campos obrigatorios, campos com *, sao obrigatorios");
        return;
    }
    const dados = {
        nome,
        sobrenome,
        numeroDoBI,
        email,
        enderecoResidencial,
        telefone,
        localDeTrabalhoOuEstudo,
        genero,
        dataDeNascimento,
        senha
    };
    const botao = cadastroForm.querySelector('#cadastrar');
    botao.disabled = true;
    try {
        const resultado = await Api.enviarRequisicao("/auth/signup", dados, "POST");
        // Mensagens.mostrarMensagem(
            // cadastroForm, , true, 3000);
            Mensagens.menssagemErroSucesso("e", resultado.menssagem ?? "Conta criada com sucesso!"); 
        cadastroForm.reset();
        document.getElementById("email").value = email;
        document.getElementById("senha").value = senha;
        setTimeout(() => {
            formulario_cadastro.style.display = "none";
            formulario_login.style.display = "block";
        }, 1000)
    } catch (erro) {
        Mensagens.menssagemErroSucesso("e", erro.message);
        
        console.error(erro.message)
    } finally {
        botao.disabled = false;
    }
});


