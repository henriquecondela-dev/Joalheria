import { AreaAdmin } from "./padrao/AreaAdmin.js";
async function carregar() {
  if (!(await AreaAdmin.verificarAcesso())) return;
  try {
    const [vendas, produtos, vendedores, destaques] = await Promise.all([
      AreaAdmin.obter("/admin/vendas"),
      AreaAdmin.obter("/admin/inventario"),
      AreaAdmin.obter("/admin/produtividade"),
      AreaAdmin.obter("/admin/destaques"),
    ]);
    const cards = document.querySelectorAll(".resumo-card h2");
    cards[0].textContent = AreaAdmin.preco(
      vendas.reduce((s, v) => s + Number(v.valorTotal), 0),
    );
    cards[1].textContent = produtos.filter(
      (p) => Number(p.stockDisponivel) > 0,
    ).length;
    cards[2].textContent = produtos.filter(
      (p) => Number(p.stockDisponivel) > 0 && Number(p.stockDisponivel) <= 5,
    ).length;
    cards[3].textContent = vendedores[0]?.vendedor || "Sem vendas";
    AreaAdmin.tabela(
      document.querySelector(".administrador .tabela table"),
      vendedores
        .slice(0, 5)
        .map((v) => [
          v.vendedor,
          v.quantidadeDeVendas,
          AreaAdmin.preco(v.valorTotal),
        ]),
      ["Vendedor", "Quantidade de vendas", "Total vendido"],
    );
    const caixa = document.querySelector(".administrador .produtos-destaque");
    caixa.querySelectorAll(".produto-admin").forEach((e) => e.remove());
    destaques.forEach((p) => {
      const div = document.createElement("div");
      div.className = "produto-admin";
      const h = document.createElement("h4");
      h.textContent = p.nome;
      const qtd = document.createElement("p");
      qtd.textContent = `${p.vendidos} vendidos`;
      const val = document.createElement("p");
      val.textContent = AreaAdmin.preco(p.precoDeVenda);
      div.append(h, qtd, val);
      caixa.append(div);
    });
  } catch (e) {
    AreaAdmin.erro(e);
  }
}

carregar();
