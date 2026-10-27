import { AreaAdmin } from "./padrao/AreaAdmin.js";
async function iniciar() {
  if (!(await AreaAdmin.verificarAcesso())) return;
  try {
    const p = await AreaAdmin.obter("/admin/perfil");
    const rows = document.querySelectorAll(
      "main.perfil .dados:first-child .tabela-perfil tr",
    );
    const valores = [
      [p.nome, p.sobrenome].join(" "),
      p.numeroDoCartao,
      p.enderecoResidencial,
      p.telefone,
      p.nivelEducacional,
    ];
    rows.forEach((r, i) => {
      const td = r.querySelectorAll("td")[1];
      if (td) td.textContent = valores[i] ?? "Não informado";
    });
  } catch (e) {
    AreaAdmin.erro(e);
  }
}
iniciar();
