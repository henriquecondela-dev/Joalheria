const botaoMenu=document.getElementById("menu-button");
const menu=document.querySelector(".menu");
const iconMenu=document.querySelector(".icon-menu");


botaoMenu.addEventListener("click", () => {
    
    menu.classList.toggle("activo");
    botaoMenu.classList.toggle("activo");
});
