
const abrirFiltros= document.getElementById("abrir-filtros");
const fecharFiltros=document.getElementById("fechar-filtros");

const blocoFiltros=document.querySelector(".bloco-filtros");


abrirFiltros.addEventListener("click",abirOuFecharFiltros);
fecharFiltros.addEventListener("click",abirOuFecharFiltros);

function abirOuFecharFiltros(){
    blocoFiltros.classList.toggle("activo");
}