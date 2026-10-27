import { Api } from "./padrao/Api.js";
import { AreaAdmin } from "./padrao/AreaAdmin.js";
async function iniciar() {
  const usuario = await AreaAdmin.verificarAcesso();
  if (!usuario) return;
  const nome = document.getElementById("nome-usuario");
  if (nome) nome.textContent = usuario.nome;
  document
    .querySelector(".sair-admin")
    ?.addEventListener("click", async (e) => {
      e.preventDefault();
      try {
        await Api.enviarRequisicao("/auth/logout", null, "POST");
        location.assign("/frontend/pages/acessoAvancado.html");
      } catch (err) {
        AreaAdmin.erro(err);
      }
    });
}
iniciar();
