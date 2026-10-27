
import { Sessao } from "./padrao/Sessao.js";
const nomeUsuario = document.getElementById("nome-usuario");
async function carregarNomeUsuario() {
    if (!nomeUsuario) return;
    const usuario = await Sessao.obterUsuario();
    if (!usuario) {
        nomeUsuario.textContent = "";
        return;
    }
    nomeUsuario.textContent = `${usuario.nome} ${usuario.sobrenome}`;
    nomeUsuario.title = `${usuario.nome} ${usuario.sobrenome}`;
}
carregarNomeUsuario();
