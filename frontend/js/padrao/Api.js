export class Api {
    static rotaBase = "http://127.0.0.1:8000/joalheria/api";
    static async enviarRequisicao(rota, dados, metodo) {
        const resposta = await fetch(`${this.rotaBase}${rota}`, {
            method: metodo,
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "include",
            body: JSON.stringify(dados)
        });
        let resultado;
        try {
            resultado = await resposta.json();
        } catch {
            throw new Error("A API não devolveu JSON válido.");
        }
        if(resposta.status===500){
            throw new Error("Falha interna");
        }
        if (!resposta.ok || resultado?.sucesso === false) {
            throw new Error(
                resultado?.ERRO ??
                resultado?.menssagem ??
                resultado?.mensagem ??
                "Erro ao processar a requisição."
            );
        }
        
        return resultado;
    }
}