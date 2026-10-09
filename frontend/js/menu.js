const botaoMenu=document.getElementById("menu-button");
const menu=document.querySelector(".menu");
const iconMenu=document.querySelector(".icon-menu");

botaoMenu.addEventListener("click", () => {
    // Liga/Desliga a classe ativo no menu e no botão
    menu.classList.toggle("activo");
    botaoMenu.classList.toggle("activo");
});