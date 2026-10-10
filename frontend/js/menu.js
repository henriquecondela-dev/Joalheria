const botaoMenu=document.getElementById("menu-button");
const menu=document.querySelector(".menu");
const iconMenu=document.querySelector(".icon-menu");

const abrirFiltros= document.getElementById("abrir-filtros");
const fecharFiltros=document.getElementById("fechar-filtros");

const blocoFiltros=document.querySelector(".bloco-filtros");


botaoMenu.addEventListener("click", () => {
    
    menu.classList.toggle("activo");
    botaoMenu.classList.toggle("activo");
});

abrirFiltros.addEventListener("click",abirOuFecharFiltros)
fecharFiltros.addEventListener("click",abirOuFecharFiltros)

function abirOuFecharFiltros(){
    blocoFiltros.classList.toggle("activo");
}
