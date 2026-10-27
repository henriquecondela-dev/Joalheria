export class Mensagens {
    static mostrarMensagem(local, mensagem, sucesso = false,espera=3000) {
        let elemento = local.querySelector(".mensagem-Erro");
        if (!elemento) {
            elemento = document.createElement("p");
            elemento.className = "mensagem-Erro";
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

    static menssagemErroSucesso(estado="s",mensagem ){
        let elemento=document.querySelector(".mensagem-Erro");
        if (elemento.timeoutId) {
            clearTimeout(elemento.timeoutId);
        }
        if(estado==="s".toLocaleLowerCase()){
            elemento.classList.add("sucesso");
            elemento.textContent = mensagem;
            elemento.timeoutId = setTimeout(() => {
                elemento.classList.remove("sucesso");
                // elemento.style.display='none';
        }, 3000);
        }else if(estado==="e"){
            elemento.classList.add("erro");
            elemento.textContent = mensagem;
            elemento.timeoutId = setTimeout(() => {
            elemento.classList.remove("erro");
        },3000);
    }
    }
}