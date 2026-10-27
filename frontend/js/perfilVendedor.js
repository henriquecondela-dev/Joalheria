
import { Api } from "./padrao/Api.js";
import { AreaVendedor } from "./padrao/AreaVendedor.js";

const tabelas = document.querySelectorAll(".perfil .tabela-perfil");

function preencherTabela(tabela, dados) {
    const linhas = tabela.querySelectorAll("tr");

    linhas.forEach((linha, indice) => {
        const valor = dados[indice] ?? "Não informado";
        const celula = linha.querySelectorAll("td")[1];

        if (celula) {
            celula.textContent = valor;
        }
    });
}

async function carregarPerfil() {
    const usuario = await AreaVendedor.verificarAcesso();

    if (!usuario) return;

    try {
        const resposta = await Api.enviarRequisicao(
            "/vendedor/perfil",
            null,
            "GET"
        );

        const perfil = resposta.dados;

        preencherTabela(tabelas[0], [
            [perfil.nome, perfil.sobrenome].filter(Boolean).join(" "),
            perfil.numeroDoCartao,
            perfil.enderecoResidencial,
            perfil.telefone
        ]);

        preencherTabela(tabelas[1], [
            perfil.departamento
        ]);

    } catch (erro) {
        console.error(erro);
        tabelas.forEach(tabela => {
            tabela.replaceChildren();
            const linha = tabela.insertRow();
            const celula = linha.insertCell();
            celula.textContent = "Não foi possível carregar os dados.";
        });
    }
}

carregarPerfil();
