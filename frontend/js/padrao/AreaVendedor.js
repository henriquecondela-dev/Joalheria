
import { Sessao } from "./Sessao.js";

export class AreaVendedor {

    static async verificarAcesso() {
        const usuario = await Sessao.obterUsuario();

        if (!usuario) {
            window.location.replace("/frontend/pages/credenciar.html");
            return null;
        }

        if (String(usuario.papel).toUpperCase() !== "VENDEDOR") {
            window.location.replace("/frontend/pages/inicial.html");
            return null;
        }

        return usuario;
    }

    static formatarPreco(valor) {
        return Number(valor || 0).toLocaleString("pt-MZ", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + " MT";
    }

    static formatarData(valor) {
        if (!valor) return "";

        const data = new Date(String(valor).replace(" ", "T"));

        if (Number.isNaN(data.getTime())) {
            return String(valor);
        }

        return data.toLocaleString("pt-MZ");
    }
}
