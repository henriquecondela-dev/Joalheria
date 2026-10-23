export class Mensagens {
    static mostrarMensagem(local, mensagem, sucesso = false,espera=3000) {
        let elemento = local.querySelector(".mensagem-feedback");
        if (!elemento) {
            elemento = document.createElement("p");
            elemento.className = "mensagem-feedback";
            elemento.setAttribute("role", "status");
            local.appendChild(elemento);
        }
        if (elemento.timeoutId) {
            clearTimeout(elemento.timeoutId);
        }
        elemento.textContent = mensagem;
        elemento.style.textAlign = 'center';
        elemento.style.color = sucesso ? "green" : "red";
        //  elemento.style.display='block';
        elemento.timeoutId = setTimeout(() => {
            elemento.remove(); 
        }, espera);
    }
}