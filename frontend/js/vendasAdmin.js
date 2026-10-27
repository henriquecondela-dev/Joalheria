import { AreaAdmin } from "./padrao/AreaAdmin.js";
async function iniciar() {
  if (!(await AreaAdmin.verificarAcesso())) return;
  try {
    const vendas = await AreaAdmin.obter("/admin/vendas");
    document.getElementById("total-vendas").textContent = vendas.length;
    document.getElementById("valor-vendas").textContent = AreaAdmin.preco(
      vendas.reduce((s, v) => s + Number(v.valorTotal), 0),
    );
    const tabela = document.getElementById("tabela-vendas");
    const render = () => {
      const texto = document
        .getElementById("pesquisa-vendas")
        .value.toLowerCase()
        .trim();
      const filtro = vendas.filter((v) =>
        String(v.vendedor + " " + v.cliente + " " + v.departamento + " " + v.id)
          .toLowerCase()
          .includes(texto),
      );
      AreaAdmin.tabela(
        tabela,
        filtro.map((v) => [
          v.id,
          AreaAdmin.data(v.dataDaVenda),
          v.cliente || "Cliente presencial",
          v.vendedor,
          v.departamento,
          AreaAdmin.preco(v.valorTotal),
        ]),
        ["N.º", "Data", "Cliente", "Vendedor", "Departamento", "Total"],
      );
    };
    document
      .getElementById("pesquisa-vendas")
      .addEventListener("input", render);
    render();
  } catch (e) {
    AreaAdmin.erro(e);
  }
}
iniciar();
