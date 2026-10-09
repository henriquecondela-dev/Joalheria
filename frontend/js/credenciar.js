const habilitar_cadastro= document.getElementById("ir_cadastrar");
const habilitar_login= document.getElementById("ir_login");
const formulario_login= document.querySelector(".section-Login");
const formulario_cadastro= document.querySelector(".section-cadastro");

habilitar_login.addEventListener("click",(e)=>{
    e.preventDefault();
    formulario_cadastro.style.display='none';
    formulario_login.style.display='block';
})
habilitar_cadastro.addEventListener("click",(e)=>{
    e.preventDefault();
    formulario_login.style.display='none';
    formulario_cadastro.style.display='block';
})

