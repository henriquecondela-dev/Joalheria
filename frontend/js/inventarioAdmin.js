import { Api } from "./padrao/Api.js";
import { Mensagens } from "./padrao/Mensagens.js";

const formulario = document.getElementById("formulario-produto");
const formProduto = document.getElementById("form-produto");
const abrirFormularioBtn = document.getElementById("abrir-formulario");
const cancelarProdutoBtn = document.getElementById("cancelar-produto");
const departamento = document.getElementById("departamento");
const tipoJoia = document.getElementById("tipo-joia");
const camposEspecificos = document.getElementById("campos-especificos");
const nomeProduto = document.getElementById("nome-produto");
const custoProduto = document.getElementById("custo-produto");
const precoProduto = document.getElementById("preco-produto");
const stockProduto = document.getElementById("stock-produto");
const fotoProduto = document.getElementById("foto-produto");
const tabelaProdutos = document.getElementById("tabela-produtos");
const tabelaResumo = document.getElementById("tabela-resumo");
const totalProdutos = document.getElementById("total-produtos");
const valorInventario = document.getElementById("valor-inventario");
const pesquisarProduto = document.getElementById("pesquisar-produto");
const filtroTipo = document.getElementById("filtro-tipo");
const filtroDepartamento = document.getElementById("filtro-departamento");
const mensagem = (tipo, texto) => Mensagens.menssagemErroSucesso(tipo, texto);
const dinheiro = (valor) =>
    Number(valor || 0).toLocaleString("pt-MZ", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
let catalogos = null;
let produtos = [];
let descricaoProduto;
const rotaBase = Api.rotaBase;

function adicionarCampo(nome, identificador, tipo = "text", opcoes = null) {
    const grupo = document.createElement("div");
    const label = document.createElement("label");
    label.htmlFor = identificador;
    label.textContent = nome;
    const campo = document.createElement(opcoes ? "select" : "input");
    campo.id = identificador;
    campo.required = true;
    if (opcoes) {
        campo.add(new Option("Seleccionar", ""));
        opcoes.forEach((item) => campo.add(new Option(item.nome, item.id)));
    } else {
        campo.type = tipo;
        if (tipo === "number") {
            campo.min = "0.001";
            campo.step = "any";
        }
    }
    grupo.append(label, campo);
    camposEspecificos.appendChild(grupo);
    return campo;
}

function criarCamposDaCategoria() {
    camposEspecificos.replaceChildren();
    const categoria = tipoJoia.selectedOptions[0]?.dataset.nome;
    if (!categoria) return;
    adicionarCampo("Marca", "car-marca");
    const materiais = (catalogos.materiais || []).map((m) => ({
        id: m.id,
        nome: m.nome,
    }));
    if (["COLARES", "PULSEIRAS"].includes(categoria)) {
        adicionarCampo("Comprimento (cm)", "car-comprimento", "number");
        adicionarCampo("Material", "car-material", "text", materiais);
    } else if (categoria === "ANEIS") {
        adicionarCampo("Diâmetro (mm)", "car-diametro", "number");
        adicionarCampo("Material", "car-material", "text", materiais);
        const pedra = adicionarCampo("Possui pedra?", "car-pedra", "text", [
            { id: "nao", nome: "Não" },
            { id: "sim", nome: "Sim" },
        ]);
        const tipoPedra = adicionarCampo("Tipo de pedra", "car-tipo-pedra");
        const quilates = adicionarCampo(
            "Quilates (apenas se for diamante)",
            "car-quilates",
            "number",
        );
        function alternarPedra() {
            const temPedra = pedra.value === "sim";
            tipoPedra.parentElement.hidden = !temPedra;
            quilates.parentElement.hidden = !temPedra;
            tipoPedra.required = temPedra;
            quilates.required = false;
            if (!temPedra) {
                tipoPedra.value = "";
                quilates.value = "";
            }
        }
        pedra.addEventListener("change", alternarPedra);
        alternarPedra();
    } else if (categoria === "BRINCOS") {
        adicionarCampo("Material", "car-material", "text", materiais);
        const pedra = adicionarCampo("Possui pedra?", "car-pedra", "text", [
            { id: "nao", nome: "Não" },
            { id: "sim", nome: "Sim" },
        ]);
        const tipoPedra = adicionarCampo("Tipo de pedra", "car-tipo-pedra");
        pedra.addEventListener("change", () => {
            tipoPedra.parentElement.hidden = pedra.value !== "sim";
            tipoPedra.required = pedra.value === "sim";
            if (pedra.value !== "sim") tipoPedra.value = "";
        });
        tipoPedra.parentElement.hidden = true;
        tipoPedra.required = false;
    } else if (categoria === "RELOGIOS") {
        adicionarCampo(
            "Material da alça",
            "car-material-alca",
            "text",
            (catalogos.materiaisDeAlca || []).map((m) => ({
                id: m.id,
                nome: m.nome,
            })),
        );
        adicionarCampo("Tipo de fecho", "car-fecho");
        adicionarCampo("Sexo", "car-sexo", "text", [
            { id: "MASCULINO", nome: "Masculino" },
            { id: "FEMININO", nome: "Feminino" },
            { id: "UNISSEX", nome: "Unissexo" },
        ]);
        adicionarCampo("Fabricante", "car-fabricante");
    } else if (categoria === "PEGAS") {
        adicionarCampo("Material", "car-material", "text", materiais);
        adicionarCampo("Tipo de fecho", "car-fecho");
    }
}

function preencherSelect(select, titulo, itens, nomeCampo = "nome") {
    select.replaceChildren(new Option(titulo, ""));
    itens.forEach((item) => {
        const opcao = new Option(item[nomeCampo], item.id);
        opcao.dataset.nome = item.nome;
        select.add(opcao);
    });
}

async function carregarCatalogos() {
    const resposta = await Api.enviarRequisicao("/admin/produtos/catalogos");
    catalogos = resposta.dados;
    if (!catalogos?.categorias || !catalogos?.departamentos)
        throw new Error("Catálogos inválidos.");
    preencherSelect(
        departamento,
        "Seleccionar departamento",
        catalogos.departamentos,
    );
    preencherSelect(
        filtroDepartamento,
        "Todos os departamentos",
        catalogos.departamentos,
    );
    preencherSelect(filtroTipo, "Todos os tipos de joia", catalogos.categorias);
    preencherSelect(tipoJoia, "Seleccionar tipo de joia", catalogos.categorias);
}

function actualizarCategorias() {
    const dep = catalogos.departamentos.find(
        (d) => Number(d.id) === Number(departamento.value),
    );
    const categorias = dep
        ? catalogos.categorias.filter(
            (c) => Number(c.id) === Number(dep.idDaCategoria),
        )
        : [];
    preencherSelect(tipoJoia, "Seleccionar tipo de joia", categorias);
    camposEspecificos.replaceChildren();
}

function valorCampo(id) {
    return document.getElementById(id)?.value.trim() || "";
}

function recolherCaracteristicas() {
    const categoria = tipoJoia.selectedOptions[0]?.dataset.nome;
    const c = {};
    const marca = valorCampo("car-marca");
    if (["COLARES", "PULSEIRAS"].includes(categoria)) {
        c.comprimentoEmCm = Number(valorCampo("car-comprimento"));
        c.idDoMaterial = Number(valorCampo("car-material"));
    } else if (categoria === "ANEIS") {
        c.diametroEmMm = Number(valorCampo("car-diametro"));
        c.idDoMaterial = Number(valorCampo("car-material"));
        c.possuiPedra = valorCampo("car-pedra") === "sim";
        if (c.possuiPedra) {
            c.tipoDePedra = valorCampo("car-tipo-pedra");
            if (valorCampo("car-quilates"))
                c.quilates = Number(valorCampo("car-quilates"));
        }
    } else if (categoria === "BRINCOS") {
        c.idDoMaterial = Number(valorCampo("car-material"));
        c.possuiPedra = valorCampo("car-pedra") === "sim";
        if (c.possuiPedra) c.tipoDePedra = valorCampo("car-tipo-pedra");
    } else if (categoria === "RELOGIOS") {
        c.idDoMaterialDaAlca = Number(valorCampo("car-material-alca"));
        c.tipoDeFecho = valorCampo("car-fecho");
        c.sexo = valorCampo("car-sexo");
        c.fabricante = valorCampo("car-fabricante");
    } else if (categoria === "PEGAS") {
        c.idDoMaterial = Number(valorCampo("car-material"));
        c.tipoDeFecho = valorCampo("car-fecho");
    }
    return { marca, caracteristicas: c };
}

async function enviarImagem(id, ficheiro) {
    const dados = new FormData();
    dados.append("imagem", ficheiro);
    const resposta = await fetch(`${rotaBase}/admin/produtos/${id}/imagens`, {
        method: "POST",
        body: dados,
        credentials: "include",
    });
    const resultado = await resposta.json();
    if (!resposta.ok || resultado?.sucesso === false)
        throw new Error(
            resultado?.menssagem || resultado?.ERRO || "Falha ao enviar fotografia.",
        );
    return resultado;
}

async function guardarProduto(evento) {
    evento.preventDefault();
    if (!formProduto.reportValidity()) return;
    if (!catalogos)
        return mensagem("e", "Não foi possível carregar os catálogos.");
    const foto = fotoProduto.files[0];
    if (
        !foto ||
        !["image/jpeg", "image/png", "image/webp"].includes(foto.type) ||
        foto.size > 5 * 1024 * 1024
    ) {
        return mensagem("e", "Escolha uma imagem JPEG, PNG ou WebP com até 5 MB.");
    }
    const { marca, caracteristicas } = recolherCaracteristicas();
    const dados = {
        idDaCategoria: Number(tipoJoia.value),
        idDoDepartamento: Number(departamento.value),
        nome: nomeProduto.value.trim(),
        descricao: descricaoProduto.value.trim(),
        marca,
        custoDeAquisicao: Number(custoProduto.value),
        precoDeVenda: Number(precoProduto.value),
        stockFisico: Number(stockProduto.value),
        caracteristicas,
    };
    const botao = formProduto.querySelector('button[type="submit"]');
    botao.disabled = true;
    try {
        const resposta = await Api.enviarRequisicao(
            "/admin/produtos",
            dados,
            "POST",
        );
        const id = Number(resposta?.dados?.id);
        if (!Number.isInteger(id) || id <= 0)
            throw new Error("Produto criado, mas a API não devolveu o ID.");
        try {
            await enviarImagem(id, foto);
            mensagem("s", "Produto e fotografia guardados com sucesso!");
        } catch (erro) {
            mensagem(
                "e",
                `Produto ${id} criado, mas a fotografia falhou: ${erro.message}`,
            );
        }
        fecharFormulario();
        await carregarInventario();
    } catch (erro) {
        mensagem("e", erro.message);
    } finally {
        botao.disabled = false;
    }
}

function fecharFormulario() {
    formProduto.reset();
    camposEspecificos.replaceChildren();
    preencherSelect(tipoJoia, "Seleccionar tipo de joia", []);
    formulario.hidden = true;
}

function filtrarProdutos() {
    const texto = pesquisarProduto.value.trim().toLocaleLowerCase();
    const tipo = filtroTipo.value;
    const dep = filtroDepartamento.value;
    for (const linha of tabelaProdutos.tBodies[0].rows) {
        if (!linha.dataset.id) continue;
        linha.hidden = !(
            (!tipo || linha.dataset.categoria === tipo) &&
            (!dep || linha.dataset.departamento === dep) &&
            linha.cells[0].textContent.toLocaleLowerCase().includes(texto)
        );
    }
}

function actualizarResumo() {
    totalProdutos.textContent = produtos.length;
    const total = produtos.reduce(
        (acc, p) => acc + Number(p.custoDeAquisicao) * Number(p.stockFisico),
        0,
    );
    valorInventario.textContent = dinheiro(total) + " MT";
    const corpo = tabelaResumo.tBodies[0];
    corpo.replaceChildren();
    const grupos = new Map();
    produtos.forEach((p) => {
        const chave = JSON.stringify([p.categoria, p.departamento]);
        grupos.set(
            chave,
            (grupos.get(chave) || 0) +
            Number(p.custoDeAquisicao) * Number(p.stockFisico),
        );
    });
    grupos.forEach((valor, chave) => {
        const [categoria, dep] = JSON.parse(chave);
        const linha = corpo.insertRow();
        linha.insertCell().textContent = categoria;
        linha.insertCell().textContent = dep;
        linha.insertCell().textContent = dinheiro(valor);
    });
    const linha = corpo.insertRow();
    const celula = linha.insertCell();
    celula.colSpan = 2;
    celula.textContent = "Total da loja";
    linha.insertCell().textContent = dinheiro(total) + " MT";
}

async function carregarInventario() {
    const resposta = await Api.enviarRequisicao("/admin/inventario");
    if (!Array.isArray(resposta.dados))
        throw new Error("A API não devolveu uma lista de produtos.");
    produtos = resposta.dados;
    const corpo = tabelaProdutos.tBodies[0];
    corpo.replaceChildren();
    produtos.forEach((p) => {
        const linha = corpo.insertRow();
        linha.dataset.id = p.id;
        linha.dataset.categoria = p.idDaCategoria;
        linha.dataset.departamento = p.idDoDepartamento;
        const produto = linha.insertCell();
        if (p.imagemPrincipal) {
            const imagem = document.createElement("img");
            imagem.src = p.imagemPrincipal;
            imagem.alt = p.nome;
            produto.appendChild(imagem);
        }
        console.log("Produto recebido:", p);
        console.log("Imagem recebida:", p.imagemPrincipal);
        produto.append(document.createTextNode(" " + p.nome));
        [
            p.categoria,
            p.departamento,
            dinheiro(p.custoDeAquisicao),
            p.stockFisico,
            dinheiro(Number(p.custoDeAquisicao) * Number(p.stockFisico)),
        ].forEach((valor) => (linha.insertCell().textContent = valor));
        const accoes = linha.insertCell();
        const botao = document.createElement("button");
        botao.type = "button";
        botao.textContent = "Abastecer";
        botao.className = "botao-adicionar";
        botao.dataset.abastecer = p.id;
        accoes.appendChild(botao);
    });
    if (!produtos.length) {
        const linha = corpo.insertRow();
        const celula = linha.insertCell();
        celula.colSpan = 7;
        celula.textContent = "Ainda não existem produtos registados.";
    }
    actualizarResumo();
    filtrarProdutos();
}

async function abastecer(id) {
    const quantidadeTexto = prompt(
        "Quantas unidades pretende acrescentar ao stock?",
    );
    if (quantidadeTexto === null) return;
    const quantidade = Number(quantidadeTexto);
    if (!Number.isInteger(quantidade) || quantidade < 1)
        return mensagem("e", "Indique uma quantidade inteira positiva.");
    if (!confirm(`Confirmar abastecimento de ${quantidade} unidade(s)?`)) return;
    try {
        await Api.enviarRequisicao(
            `/admin/produtos/${id}/stock`,
            { quantidade },
            "PATCH",
        );
        mensagem("s", "Stock actualizado com sucesso!");
        await carregarInventario();
    } catch (erro) {
        mensagem("e", erro.message);
    }
}

function prepararDescricao() {
    descricaoProduto = document.getElementById("descricao-produto");
    if (descricaoProduto) return;
    const label = document.createElement("label");
    label.htmlFor = "descricao-produto";
    label.textContent = "Descrição";
    descricaoProduto = document.createElement("input");
    descricaoProduto.type = "text";
    descricaoProduto.id = "descricao-produto";
    descricaoProduto.required = true;
    const referencia = custoProduto.previousElementSibling;
    formProduto.insertBefore(label, referencia);
    formProduto.insertBefore(descricaoProduto, referencia);
}

function prepararColunaAccoes() {
    const cabecalho = tabelaProdutos.tHead?.rows[0];
    if (cabecalho && cabecalho.cells.length === 6) {
        const th = document.createElement("th");
        th.textContent = "Acções";
        cabecalho.appendChild(th);
    }
}

abrirFormularioBtn.addEventListener("click", () => (formulario.hidden = false));
cancelarProdutoBtn.addEventListener("click", fecharFormulario);
departamento.addEventListener("change", actualizarCategorias);
tipoJoia.addEventListener("change", criarCamposDaCategoria);
formProduto.addEventListener("submit", guardarProduto);
tabelaProdutos.addEventListener("click", (e) => {
    const botao = e.target.closest("button[data-abastecer]");
    if (botao) abastecer(Number(botao.dataset.abastecer));
});
pesquisarProduto.addEventListener("input", filtrarProdutos);
filtroTipo.addEventListener("change", filtrarProdutos);
filtroDepartamento.addEventListener("change", filtrarProdutos);

async function iniciar() {
    formulario.hidden = true;
    prepararDescricao();
    prepararColunaAccoes();
    try {
        await Promise.all([carregarCatalogos(), carregarInventario()]);
    } catch (erro) {
        mensagem("e", erro.message);
        console.error(erro);
    }
}
iniciar();
