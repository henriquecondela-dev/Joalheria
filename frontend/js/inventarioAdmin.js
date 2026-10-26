document.addEventListener("DOMContentLoaded", () => {
    const $ = id => document.getElementById(id);

    const formulario = $("formulario-produto");
    const form = $("form-produto");
    const departamento = $("departamento");
    const tipo = $("tipo-joia");
    const campos = $("campos-especificos");
    const tabela = $("tabela-produtos");

    const caracteristicas = {
        "Colares": ["Marca", "Comprimento", "Material"],
        "Pulseiras": ["Marca", "Comprimento", "Material"],
        "Anéis": ["Marca", "Diâmetro", "Material", "Possui pedra", "Tipo de pedra"],
        "Anéis de diamante": ["Marca", "Diâmetro", "Material", "Quilates"],
        "Relógios": ["Marca", "Material da pulseira", "Tipo de fecho", "Sexo", "Fabricante"],
        "Brincos": ["Marca", "Material", "Possui pedra", "Tipo de pedra"],
        "Pegas/fechos": ["Marca", "Material", "Tipo de fecho"]
    };

    const departamentos = {
        "Joias": Object.keys(caracteristicas),
        "Jóias": Object.keys(caracteristicas),
        "Relógios": ["Relógios"],
        "Acessórios": ["Pegas/fechos"]
    };

    $("abrir-formulario").onclick = () => formulario.hidden = false;

    $("cancelar-produto").onclick = () => {
        form.reset();
        campos.replaceChildren();
        formulario.hidden = true;
        tipo.replaceChildren(new Option("Selecionar tipo de joia", ""));
    };

    departamento.onchange = () => {
        tipo.replaceChildren(new Option("Selecionar tipo de joia", ""));
        campos.replaceChildren();

        (departamentos[departamento.value] || Object.keys(caracteristicas))
            .forEach(nome => tipo.add(new Option(nome, nome)));
    };

    tipo.onchange = () => {
        campos.replaceChildren();

        (caracteristicas[tipo.value] || []).forEach(nome => {
            const grupo = document.createElement("div");
            const label = document.createElement("label");
            const id = "campo-" + nome.toLowerCase().replace(/\s+/g, "-");

            label.textContent = nome;
            label.htmlFor = id;

            const input = document.createElement(
                ["Possui pedra", "Sexo"].includes(nome) ? "select" : "input"
            );

            if (input.tagName === "SELECT") {
                input.add(new Option("Selecionar", ""));
                (nome === "Sexo"
                    ? ["Masculino", "Feminino", "Unissexo"]
                    : ["Sim", "Não"]
                ).forEach(opcao => input.add(new Option(opcao, opcao)));
            } else {
                input.type = nome === "Quilates" ? "number" : "text";
            }

            input.id = id;
            input.name = nome;
            input.required = true;
            grupo.append(label, input);
            campos.append(grupo);

            if (nome === "Tipo de pedra") {
                const pedra = campos.querySelector('[name="Possui pedra"]');

                if (pedra) {
                    const atualizar = () => {
                        grupo.hidden = pedra.value !== "Sim";
                        input.required = pedra.value === "Sim";
                        if (grupo.hidden) input.value = "";
                    };

                    pedra.onchange = atualizar;
                    atualizar();
                }
            }
        });
    };

    const moeda = valor => valor.toLocaleString("pt-MZ", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    function atualizarResumo() {
        const linhas = [...tabela.rows].slice(1);
        $("total-produtos").textContent = linhas.length;

        const total = linhas.reduce((soma, linha) => {
            const custo = Number(linha.cells[3].textContent.replace(/\s/g, "").replace(",", ".")) || 0;
            const stock = Number(linha.cells[4].textContent) || 0;
            return soma + custo * stock;
        }, 0);

        $("valor-inventario").textContent = moeda(total) + " MT";
    }

    form.onsubmit = evento => {
        evento.preventDefault();

        const nome = $("nome-produto").value.trim();
        const custo = Number($("custo-produto").value);
        const stock = Number($("stock-produto").value);
        const foto = $("foto-produto").files[0];

        if (!nome || !departamento.value || !tipo.value || !foto ||
            !Number.isFinite(custo) || custo < 0 ||
            !Number.isFinite(stock) || stock < 0) {
            alert("Verifique os campos obrigatórios, o custo, o stock e a foto.");
            return;
        }

        const linha = tabela.insertRow();
        const produto = linha.insertCell();
        const imagem = document.createElement("img");

        imagem.src = URL.createObjectURL(foto);
        imagem.alt = nome;
        imagem.width = imagem.height = 35;
        produto.append(imagem, document.createTextNode(" " + nome));

        [tipo.value, departamento.value, moeda(custo), stock, moeda(custo * stock)]
            .forEach(valor => linha.insertCell().textContent = valor);

        atualizarResumo();
        form.reset();
        campos.replaceChildren();
        tipo.replaceChildren(new Option("Selecionar tipo de joia", ""));
        formulario.hidden = true;
        alert("Produto adicionado com sucesso!");
    };

    formulario.hidden = true;
    tipo.replaceChildren(new Option("Selecionar tipo de joia", ""));
    atualizarResumo();
});