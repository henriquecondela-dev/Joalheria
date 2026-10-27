Drop Database if exists joalheria;

CREATE DATABASE IF NOT EXISTS joalheria
CHARACTER SET utf8mb4;
USE joalheria;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(30) NOT NULL,
    senhaHash VARCHAR(255) NOT NULL,
    enderecoResidencial VARCHAR(255) NOT NULL,
    genero ENUM('MASCULINO', 'FEMININO', 'OUTRO') NOT NULL,
    papel ENUM('CLIENTE', 'VENDEDOR', 'ADMIN') NOT NULL DEFAULT 'CLIENTE',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    dataDeCriacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE clientes (
    idDoUsuario INT PRIMARY KEY,
    numeroDoBI VARCHAR(50) NOT NULL UNIQUE,
    dataDeNascimento DATE NOT NULL,
    localDeTrabalhoOuEstudo VARCHAR(255) NULL,

    CONSTRAINT fkClienteUsuario
        FOREIGN KEY (idDoUsuario)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE gerentes (
    idDoUsuario INT PRIMARY KEY,
    numeroDoCartao VARCHAR(50) NOT NULL UNIQUE,
    nivelEducacional VARCHAR(120) NOT NULL,
    CONSTRAINT fkGerenteUsuario
        FOREIGN KEY (idDoUsuario)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT NULL
) ENGINE=InnoDB;


CREATE TABLE departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL UNIQUE,
    idDaCategoria INT NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT fkDepartamentoCategoria
        FOREIGN KEY (idDaCategoria)
        REFERENCES categorias(id)
) ENGINE=InnoDB;


CREATE TABLE planosDeDepartamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDoDepartamento INT NOT NULL,
    ano SMALLINT NOT NULL,
    mes TINYINT NOT NULL,
    metaDeVendas DECIMAL(14,2) NOT NULL DEFAULT 0,
    planoDeCompras DECIMAL(14,2) NOT NULL DEFAULT 0,

    CONSTRAINT fkPlanoDepartamento
        FOREIGN KEY (idDoDepartamento)
        REFERENCES departamentos(id),

    CONSTRAINT uqPlanoMensal
        UNIQUE (idDoDepartamento, ano, mes),

    CONSTRAINT chkMesPlano
        CHECK (mes BETWEEN 1 AND 12),

    CONSTRAINT chkValoresPlano
        CHECK (metaDeVendas >= 0 AND planoDeCompras >= 0)
) ENGINE=InnoDB;


CREATE TABLE vendedores (
    idDoUsuario INT PRIMARY KEY,
    numeroDoCartao VARCHAR(50) NOT NULL UNIQUE,
    idDoDepartamento INT NOT NULL,

    CONSTRAINT fkVendedorUsuario
        FOREIGN KEY (idDoUsuario)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fkVendedorDepartamento
        FOREIGN KEY (idDoDepartamento)
        REFERENCES departamentos(id)
) ENGINE=InnoDB;


-- CATALOGOS DE MATERIAIS 
CREATE TABLE materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE materiaisDeAlca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDaCategoria INT NOT NULL,
    idDoDepartamento INT NOT NULL,

    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    marca VARCHAR(120) NOT NULL,

    custoDeAquisicao DECIMAL(12,2) NOT NULL,
    precoDeVenda DECIMAL(12,2) NOT NULL,

    stockFisico INT NOT NULL DEFAULT 0,
    stockReservado INT NOT NULL DEFAULT 0,

    activo BOOLEAN NOT NULL DEFAULT TRUE,
    dataDeCriacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fkProdutoCategoria
        FOREIGN KEY (idDaCategoria) REFERENCES categorias(id),

    CONSTRAINT fkProdutoDepartamento
        FOREIGN KEY (idDoDepartamento) REFERENCES departamentos(id),

    CONSTRAINT chkValoresProduto
        CHECK (
            custoDeAquisicao >= 0
            AND precoDeVenda >= 0
            AND stockFisico >= 0
            AND stockReservado >= 0
            AND stockReservado <= stockFisico
        )
) ENGINE=InnoDB;


CREATE TABLE colares (
    idDoProduto INT PRIMARY KEY,
    comprimentoEmCm DECIMAL(8,2) NOT NULL,
    -- Valores iniciais em materiais: OURO, PRATA, PLATINA, ACO_INOXIDAVEL, TITANIO, COBRE, BRONZEO.
    idDoMaterial INT NOT NULL,

    CONSTRAINT fkColarMaterial
        FOREIGN KEY (idDoMaterial)
        REFERENCES materiais(id),

    CONSTRAINT fkColarProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE,

    CONSTRAINT chkComprimentoColar
        CHECK (comprimentoEmCm > 0)
) ENGINE=InnoDB;


CREATE TABLE pulseiras (
    idDoProduto INT PRIMARY KEY,
    comprimentoEmCm DECIMAL(8,2) NOT NULL,
    -- Valores iniciais em materiais: OURO, PRATA, PLATINA, ACO_INOXIDAVEL, TITANIO, COBRE, BRONZE.
    idDoMaterial INT NOT NULL,

    CONSTRAINT fkPulseiraMaterial
        FOREIGN KEY (idDoMaterial)
        REFERENCES materiais(id),

    CONSTRAINT fkPulseiraProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE,

    CONSTRAINT chkComprimentoPulseira
        CHECK (comprimentoEmCm > 0)
) ENGINE=InnoDB;


CREATE TABLE aneis (
    idDoProduto INT PRIMARY KEY,
    diametroEmMm DECIMAL(8,2) NOT NULL,
    -- Valores iniciais em materiais: OURO, PRATA, PLATINA, ACO_INOXIDAVEL, TITANIO, COBRE, BRONZE.
    idDoMaterial INT NOT NULL,
    possuiPedra BOOLEAN NOT NULL DEFAULT FALSE,
    tipoDePedra VARCHAR(120) NULL,

    CONSTRAINT fkAnelMaterial
        FOREIGN KEY (idDoMaterial)
        REFERENCES materiais(id),

    CONSTRAINT fkAnelProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE,

    CONSTRAINT chkDiametroAnel
        CHECK (diametroEmMm > 0)
) ENGINE=InnoDB;


CREATE TABLE aneisDeDiamante (
    idDoProduto INT PRIMARY KEY,
    quilates DECIMAL(8,3) NOT NULL,

    CONSTRAINT fkAnelDiamante
        FOREIGN KEY (idDoProduto)
        REFERENCES aneis(idDoProduto)
        ON DELETE CASCADE,

    CONSTRAINT chkQuilates
        CHECK (quilates > 0)
) ENGINE=InnoDB;


CREATE TABLE relogios (
    idDoProduto INT PRIMARY KEY,
    -- Valores iniciais:COURO, ACO_INOXIDAVEL, SILICONE, BORRACHA, NYLON, TITANIO, CERAMICA, TECIDO.
    idDoMaterialDaAlca INT NOT NULL,
    tipoDeFecho VARCHAR(120) NOT NULL,
    sexo ENUM('MASCULINO', 'FEMININO', 'UNISSEX') NOT NULL,
    fabricante VARCHAR(120) NOT NULL,

    CONSTRAINT fkRelogioMaterialDaAlca
        FOREIGN KEY (idDoMaterialDaAlca)
        REFERENCES materiaisDeAlca(id),

    CONSTRAINT fkRelogioProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE brincos (
    idDoProduto INT PRIMARY KEY,
    -- Valores iniciais em materiais: OURO, PRATA, PLATINA, ACO_INOXIDAVEL, TITANIO, COBRE, BRONZE.
    idDoMaterial INT NOT NULL,
    possuiPedra BOOLEAN NOT NULL DEFAULT FALSE,
    tipoDePedra VARCHAR(120) NULL,

    CONSTRAINT fkBrincoMaterial
        FOREIGN KEY (idDoMaterial)
        REFERENCES materiais(id),

    CONSTRAINT fkBrincoProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE pegas (
    idDoProduto INT PRIMARY KEY,
    -- Valores iniciais: OURO, PRATA, PLATINA, ACO_INOXIDAVEL, TITANIO, COBRE, BRONZE,
    idDoMaterial INT NOT NULL,
    tipoDeFecho VARCHAR(120) NOT NULL,

    CONSTRAINT fkPegaMaterial
        FOREIGN KEY (idDoMaterial)
        REFERENCES materiais(id),

    CONSTRAINT fkPegaProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE imagensDeProdutos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDoProduto INT NOT NULL,
    caminhoDaImagem VARCHAR(500) NOT NULL,
    imagemPrincipal BOOLEAN NOT NULL DEFAULT TRUE,
    ordemDeExibicao INT NOT NULL DEFAULT 0,

    CONSTRAINT fkImagemProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id)
        ON DELETE CASCADE,

    CONSTRAINT uqImagemDoProduto
        UNIQUE (idDoProduto, caminhoDaImagem)
) ENGINE=InnoDB;


CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDoDepartamento INT NOT NULL,
    idDoUsuarioResponsavel INT NOT NULL,
    dataDaCompra DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,
    valorTotal DECIMAL(14,2) NOT NULL DEFAULT 0,

    CONSTRAINT fkCompraDepartamento
        FOREIGN KEY (idDoDepartamento)
        REFERENCES departamentos(id),

    CONSTRAINT fkCompraResponsavel
        FOREIGN KEY (idDoUsuarioResponsavel)
        REFERENCES usuarios(id),

    CONSTRAINT chkTotalCompra
        CHECK (valorTotal >= 0)
) ENGINE=InnoDB;


CREATE TABLE itensDeCompra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDaCompra INT NOT NULL,
    idDoProduto INT NOT NULL,
    quantidade INT NOT NULL,
    custoUnitario DECIMAL(12,2) NOT NULL,

    CONSTRAINT fkItemCompra
        FOREIGN KEY (idDaCompra) REFERENCES compras(id),

    CONSTRAINT fkItemCompraProduto
        FOREIGN KEY (idDoProduto) REFERENCES produtos(id),

    CONSTRAINT chkItemCompra
        CHECK (quantidade > 0 AND custoUnitario >= 0)
) ENGINE=InnoDB;


CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDoCliente INT NOT NULL,

    dataDaReserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    estado ENUM(
        'PENDENTE',
        'CONFIRMADA',
        'CONCLUIDA',
        'CANCELADA'
    ) NOT NULL DEFAULT 'PENDENTE',

    valorTotal DECIMAL(14,2) NOT NULL DEFAULT 0,

    CONSTRAINT fkReservaCliente
        FOREIGN KEY (idDoCliente) REFERENCES clientes(idDoUsuario),

    CONSTRAINT chkTotalReserva CHECK (valorTotal >= 0)
) ENGINE=InnoDB;


CREATE TABLE itensEmReserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDaReserva INT NOT NULL,
    idDoProduto INT NOT NULL,
    quantidade INT NOT NULL,
    precoUnitario DECIMAL(12,2) NOT NULL,

    CONSTRAINT fkItemReserva
        FOREIGN KEY (idDaReserva) REFERENCES reservas(id),

    CONSTRAINT fkItemReservaProduto
        FOREIGN KEY (idDoProduto) REFERENCES produtos(id),

    CONSTRAINT chkItemReserva
        CHECK (quantidade > 0 AND precoUnitario >= 0)
) ENGINE=InnoDB;



CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDoVendedor INT NOT NULL,
    idDoDepartamento INT NOT NULL,
    idDoCliente INT NULL,
    idDaReserva INT NULL,

    dataDaVenda DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    valorTotal DECIMAL(14,2) NOT NULL DEFAULT 0,

    CONSTRAINT fkVendaVendedor
        FOREIGN KEY (idDoVendedor)
        REFERENCES vendedores(idDoUsuario),

    CONSTRAINT fkVendaDepartamento
        FOREIGN KEY (idDoDepartamento)
        REFERENCES departamentos(id),

    CONSTRAINT fkVendaCliente
        FOREIGN KEY (idDoCliente)
        REFERENCES clientes(idDoUsuario),

    CONSTRAINT fkVendaReserva
        FOREIGN KEY (idDaReserva)
        REFERENCES reservas(id),

    CONSTRAINT chkTotalVenda
        CHECK (valorTotal >= 0)
) ENGINE=InnoDB;


CREATE TABLE itensDeVenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDaVenda INT NOT NULL,
    idDoProduto INT NOT NULL,
    quantidade INT NOT NULL,
    precoUnitario DECIMAL(12,2) NOT NULL,

    CONSTRAINT fkItemVenda
        FOREIGN KEY (idDaVenda)
        REFERENCES vendas(id),

    CONSTRAINT fkItemVendaProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id),

    CONSTRAINT chkItemVenda
        CHECK ( quantidade > 0 AND precoUnitario >= 0  )
) ENGINE=InnoDB;



CREATE TABLE movimentosDeStock (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    idDoProduto INT NOT NULL,
    idDoUsuarioResponsavel INT NULL,

    tipoDeMovimento ENUM(
        'ENTRADA_POR_COMPRA',
        'SAIDA_POR_VENDA',
        'RESERVA',
        'LIBERTACAO_DE_RESERVA',
        'AJUSTE'
    ) NOT NULL,

    quantidade INT NOT NULL,
    observacao VARCHAR(500) NULL,

    dataDoMovimento DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fkMovimentoProduto
        FOREIGN KEY (idDoProduto)
        REFERENCES produtos(id),

    CONSTRAINT fkMovimentoResponsavel
        FOREIGN KEY (idDoUsuarioResponsavel)
        REFERENCES usuarios(id),

    CONSTRAINT chkQuantidadeMovimento
        CHECK (quantidade > 0)
) ENGINE=InnoDB;


CREATE TABLE configuracoesDaLoja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomeDaLoja VARCHAR(150) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    email VARCHAR(255) NOT NULL,
    horarioDeFuncionamento VARCHAR(255) NOT NULL,
    idDoGerente INT NULL,

    CONSTRAINT fkLojaGerente
        FOREIGN KEY (idDoGerente)
        REFERENCES gerentes(idDoUsuario)
) ENGINE=InnoDB;


CREATE TABLE imagensDaLoja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDaConfiguracao INT NOT NULL,
    caminhoDaImagem VARCHAR(500) NOT NULL,
    descricao VARCHAR(255) NULL,

    CONSTRAINT fkImagemLoja
        FOREIGN KEY (idDaConfiguracao)
        REFERENCES configuracoesDaLoja(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- POPULACAO INICIAL DOS MATERIAIS

INSERT INTO materiais (nome) VALUES
    ('OURO'),
    ('PRATA'),
    ('ACO_INOXIDAVEL'),
    ('TITANIO'),
    ('COBRE'),
    ('BRONZE');
    
INSERT INTO materiaisDeAlca (nome) VALUES
    ('COURO'),
    ('ACO_INOXIDAVEL'),
    ('SILICONE'),
    ('BORRACHA'),
    ('NYLON'),
    ('TITANIO'),
    ('CERAMICA'),
    ('TECIDO');
insert into categorias (nome) values 
('COLARES'),
('PULSEIRAS'),
('ANEIS'),
('RELOGIOS'),
('BRINCOS'),
('PEGAS');

insert into departamentos (nome,idDaCategoria) values
('COLARES',1),
('PULSEIRAS',2),
('ANEIS',3),
('RELOGIOS',4),
('BRINCOS',5),
('PEGAS',6);

CREATE TABLE solicitacoesDeVendedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDoUsuario INT NOT NULL,
    numeroDoCartao VARCHAR(50) NOT NULL,
    idDoDepartamento INT NOT NULL,
    estado ENUM('PENDENTE', 'APROVADA', 'REJEITADA') NOT NULL DEFAULT 'PENDENTE',
    dataDoPedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idDoAdministrador INT NULL,
    dataDaDecisao DATETIME NULL,
    motivoDaRejeicao VARCHAR(500) NULL,
    CONSTRAINT fkSolicitacaoUsuario FOREIGN KEY (idDoUsuario) REFERENCES usuarios(id),
    CONSTRAINT fkSolicitacaoDepartamento FOREIGN KEY (idDoDepartamento) REFERENCES departamentos(id),
    CONSTRAINT fkSolicitacaoAdministrador FOREIGN KEY (idDoAdministrador) REFERENCES usuarios(id),
    INDEX idxSolicitacoesEstado (estado, dataDoPedido),
    INDEX idxSolicitacoesUsuario (idDoUsuario)
) ENGINE=InnoDB;
insert into configuracoesDaLoja (nomeDaLoja,endereco,telefone,email,horarioDeFuncionamento)
values
('Joalheria zambeze','Beira, sofala','866970639','joalheriazambeze@gmail.com','08:00 as 18:00');
