import { Api } from "./Api.js";
import { Sessao } from "./Sessao.js";
export class AreaAdmin {
    static async verificarAcesso() {
        const usuario = await Sessao.obterUsuario();
        if (!usuario) {
            location.replace("/frontend/pages/acessoAvancado.html");
            return null;
        }
        if (usuario.papel !== "ADMIN") {
            location.replace("/frontend/pages/inicial.html");
            return null;
        }
        return usuario;
    }
    static async obter(rota) {
        const r = await Api.enviarRequisicao(rota, null, "GET");
        return r.dados;
    }
    static preco(valor) {
        return (
            Number(valor || 0).toLocaleString("pt-MZ", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }) + " MT"
        );
    }
    static data(valor) {
        if (!valor) return "—";
        const d = new Date(String(valor).replace(" ", "T"));
        return isNaN(d.getTime()) ? String(valor) : d.toLocaleString("pt-MZ");
    }
    static tabela(table, rows, colunas, vazio = "Sem registos.") {
        if (!table) return;
        const cab = table.querySelector("thead") || table.createTHead();
        if (!cab.rows.length) {
            const tr = cab.insertRow();
            colunas.forEach((c) => {
                const th = document.createElement("th");
                th.textContent = c;
                tr.append(th);
            });
        }
        const corpo = table.tBodies[0] || table.createTBody();
        corpo.replaceChildren();
        if (!rows.length) {
            const tr = corpo.insertRow();
            const td = tr.insertCell();
            td.colSpan = colunas.length;
            td.textContent = vazio;
            return;
        }
        rows.forEach((valores) => {
            const tr = corpo.insertRow();
            valores.forEach((v) => {
                const td = tr.insertCell();
                td.textContent = v ?? "—";
            });
        });
    }
    static erro(erro) {
        console.error(erro);
        const el = document.querySelector(".mensagem-Erro");
        if (el) el.textContent = erro.message || "Erro ao consultar a API.";
    }
    static periodo(tipo) {
        const fim = new Date();
        const inicio = new Date(fim);
        if (tipo === "diario") {
        } else if (tipo === "semanal") inicio.setDate(fim.getDate() - 6);
        else inicio.setMonth(fim.getMonth() - 1);
        const formatar = (d) =>
            `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
        return { inicio: formatar(inicio), fim: formatar(fim) };
    }
}
