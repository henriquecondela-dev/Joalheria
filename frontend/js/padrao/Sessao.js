
import { Api } from "./Api.js";
export class Sessao {
    static async obterUsuario() {
        try {
            const resposta = await Api.enviarRequisicao("/auth/eu", null, "GET");
            return resposta.dados;
        } catch (erro) {
            console.error("Erro ao consultar a sessão:", erro);
            return null;
        }
    }
    static async exigirLogin() {
        const usuario = await this.obterUsuario();
        if (usuario) {
            return usuario;
        }
        const paginaActual =  window.location.pathname + window.location.search;
        sessionStorage.setItem("paginaAposLogin", paginaActual);
        window.location.href = "/frontend/pages/credenciar.html";
        return null;
    }
}
