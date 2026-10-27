import { AreaAdmin } from "./padrao/AreaAdmin.js";
async function iniciar() {
  if (!(await AreaAdmin.verificarAcesso())) return;
  try {
    const dados = await AreaAdmin.obter("/admin/departamentos");
    AreaAdmin.tabela(
      document.querySelector(".tabela-departamentos table"),
      dados.map((d) => [
        d.nome,
        d.categoria,
        AreaAdmin.preco(d.metaDeVendas),
        AreaAdmin.preco(d.planoDeCompras),
      ]),
      [
        "Departamento",
        "Tipo de joia",
        "Plano mensal de vendas (MT)",
        "Plano de compras (MT)",
      ],
    );
  } catch (e) {
    AreaAdmin.erro(e);
  }
}

iniciar();
