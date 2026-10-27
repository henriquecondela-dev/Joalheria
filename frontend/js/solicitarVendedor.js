import { Api } from "./padrao/Api.js";
import { Sessao } from "./padrao/Sessao.js";
const formulario = document.querySelector(".cadastrarvendedor-form");
if (formulario) {
  const select = formulario.querySelector("#departamento");
  async function carregarDepartamentos() {
    try {
      const resposta = await Api.enviarRequisicao(
        "/departamentos",
        null,
        "GET",
      );
      select.replaceChildren(new Option("Seleccionar departamento", ""));
      resposta.dados.forEach((d) => select.add(new Option(d.nome, d.id)));
    } catch (e) {
      console.error(e);
    }
  }
  carregarDepartamentos();
  formulario.addEventListener("submit", async (e) => {
    e.preventDefault();
    const usuario = await Sessao.exigirLogin();
    if (!usuario) return;
    if (usuario.papel !== "CLIENTE") {
      alert("Apenas clientes podem solicitar tornar-se vendedores.");
      return;
    }
    const numeroDoCartao = formulario
      .querySelector("#numeroDoCartao")
      .value.trim();
    const idDoDepartamento = Number(select.value);
    if (
      !numeroDoCartao ||
      !Number.isInteger(idDoDepartamento) ||
      idDoDepartamento < 1
    ) {
      alert("Preencha o cartão e o departamento.");
      return;
    }
    const botao = formulario.querySelector('[type="submit"]');
    botao.disabled = true;
    try {
      const r = await Api.enviarRequisicao(
        "/solicitacoes/vendedor",
        { numeroDoCartao, idDoDepartamento },
        "POST",
      );
      alert(
        r.menssagem || "Pedido enviado. Aguarde a decisão do administrador.",
      );
      formulario.reset();
    } catch (erro) {
      alert(erro.message);
    } finally {
      botao.disabled = false;
    }
  });
}
