USE joalheria;
START TRANSACTION;

-- Produto 01: /produtos/relogios/relogio_analogico_azul_pulseira_escura.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico azul pulseira escura','Relógio analógico azul pulseira escura, com design elegante e versátil.','Casio',300.00,500.00,6,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Casio');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_azul_pulseira_escura.jpg',1,0);

-- Produto 02: /produtos/relogios/relogio_cronografo_azul_pulseira_azul.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio cronógrafo azul pulseira azul','Relógio cronógrafo azul pulseira azul, com design elegante e versátil.','Seiko',440.00,730.00,7,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Seiko');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_cronografo_azul_pulseira_azul.jpg',1,0);

-- Produto 03: /produtos/relogios/relogio_minimalista_preto_pulseira_castanha.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio minimalista preto pulseira castanha','Relógio minimalista preto pulseira castanha, com design elegante e versátil.','Fossil',580.00,960.00,8,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Fossil');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_minimalista_preto_pulseira_castanha.jpg',1,0);

-- Produto 04: /produtos/relogios/relogio_cronografo_preto_aco.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio cronógrafo preto aço','Relógio cronógrafo preto aço, com design elegante e versátil.','Citizen',850.00,1400.00,9,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Citizen');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_cronografo_preto_aco.jpg',1,0);

-- Produto 05: /produtos/relogios/relogio_minimalista_branco_pulseira_rosa.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio minimalista branco pulseira rosa','Relógio minimalista branco pulseira rosa, com design elegante e versátil.','Timex',670.00,1110.00,10,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='SILICONE'),'Fecho ajustavel','FEMININO','Timex');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_minimalista_branco_pulseira_rosa.jpg',1,0);

-- Produto 06: /produtos/relogios/relogio_inteligente_rosa_malha_metalica.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio inteligente rosa malha metálica','Relógio inteligente rosa malha metálica, com design elegante e versátil.','Xiaomi',780.00,1290.00,11,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','FEMININO','Xiaomi');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_inteligente_rosa_malha_metalica.jpg',1,0);

-- Produto 07: /produtos/relogios/relogio_analogico_azul_pulseira_bege.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico azul pulseira bege','Relógio analógico azul pulseira bege, com design elegante e versátil.','Orient',930.00,1530.00,12,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Orient');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_azul_pulseira_bege.jpg',1,0);

-- Produto 08: /produtos/relogios/relogio_analogico_azul_aco.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico azul aço','Relógio analógico azul aço, com design elegante e versátil.','Casio',1020.00,1680.00,13,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Casio');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_azul_aco.jpg',1,0);

-- Produto 09: /produtos/relogios/relogio_analogico_azul_detalhes_dourados.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico azul detalhes dourados','Relógio analógico azul detalhes dourados, com design elegante e versátil.','Seiko',620.00,1020.00,14,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Seiko');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_azul_detalhes_dourados.jpg',1,0);

-- Produto 10: /produtos/relogios/relogio_cronografo_preto_aco_prateado.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio cronógrafo preto aço prateado','Relógio cronógrafo preto aço prateado, com design elegante e versátil.','Fossil',1060.00,1750.00,15,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Fossil');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_cronografo_preto_aco_prateado.jpg',1,0);

-- Produto 11: /produtos/relogios/relogio_analogico_cinzento_pulseira_castanha.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico cinzento pulseira castanha','Relógio analógico cinzento pulseira castanha, com design elegante e versátil.','Citizen',760.00,1250.00,16,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Citizen');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_cinzento_pulseira_castanha.jpg',1,0);

-- Produto 12: /produtos/relogios/relogio_digital_preto_dourado.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio digital preto dourado','Relógio digital preto dourado, com design elegante e versátil.','Casio',1150.00,1900.00,17,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Casio');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_digital_preto_dourado.jpg',1,0);

-- Produto 13: /produtos/relogios/relogio_analogico_branco_aco.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico branco aço','Relógio analógico branco aço, com design elegante e versátil.','Timex',1040.00,1720.00,5,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','FEMININO','Timex');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_branco_aco.jpg',1,0);

-- Produto 14: /produtos/relogios/relogio_cronografo_preto_rose_pulseira_escura.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio cronógrafo preto rosé pulseira escura','Relógio cronógrafo preto rosé pulseira escura, com design elegante e versátil.','Fossil',1090.00,1800.00,6,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Fossil');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_cronografo_preto_rose_pulseira_escura.jpg',1,0);

-- Produto 15: /produtos/relogios/relogio_cronografo_azul_pulseira_castanha.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio cronógrafo azul pulseira castanha','Relógio cronógrafo azul pulseira castanha, com design elegante e versátil.','Seiko',1100.00,1820.00,7,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Seiko');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_cronografo_azul_pulseira_castanha.jpg',1,0);

-- Produto 16: /produtos/relogios/relogio_analogico_preto_aco_bicolor.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio analógico preto aço bicolor','Relógio analógico preto aço bicolor, com design elegante e versátil.','Orient',1070.00,1770.00,8,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='ACO_INOXIDAVEL'),'Fecho de encaixe','UNISSEX','Orient');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_analogico_preto_aco_bicolor.jpg',1,0);

-- Produto 17: /produtos/relogios/relogio_cronografo_preto_pulseira_escura.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (4,4,'Relógio cronógrafo preto pulseira escura','Relógio cronógrafo preto pulseira escura, com design elegante e versátil.','Citizen',1300.00,2140.00,9,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO relogios (idDoProduto,idDoMaterialDaAlca,tipoDeFecho,sexo,fabricante)
VALUES (@idProduto,(SELECT id FROM materiaisDeAlca WHERE nome='COURO'),'Fecho de fivela','UNISSEX','Citizen');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/relogios/relogio_cronografo_preto_pulseira_escura.jpg',1,0);

-- Produto 18: /produtos/aneis/anel_prateado_pedra_branca_entrelacado.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado pedra branca entrelacado','Anel prateado pedra branca entrelacado, com acabamento delicado.','Pandora',1230.00,2030.00,10,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.00,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_pedra_branca_entrelacado.jpg',1,0);

-- Produto 19: /produtos/aneis/anel_prateado_flores_pedras_claras.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado flores pedras claras','Anel prateado flores pedras claras, com acabamento delicado.','Swarovski',1260.00,2080.00,11,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.50,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_flores_pedras_claras.jpg',1,0);

-- Produto 20: /produtos/aneis/anel_dourado_pedra_rosa.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado pedra rosa','Anel dourado pedra rosa, com acabamento delicado.','Tous',1450.00,2390.00,12,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,20.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'ROSA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_pedra_rosa.jpg',1,0);

-- Produto 21: /produtos/aneis/anel_prateado_pedra_branca_dupla_faixa.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado pedra branca dupla faixa','Anel prateado pedra branca dupla faixa, com acabamento delicado.','Thomas Sabo',1440.00,2380.00,13,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,17.00,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_pedra_branca_dupla_faixa.jpg',1,0);

-- Produto 22: /produtos/aneis/conjunto_aneis_prateados_pedras_claras.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Conjunto de anéis prateados pedras claras','Conjunto de anéis prateados pedras claras, com acabamento delicado.','Pandora',1500.00,2480.00,14,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,17.50,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/conjunto_aneis_prateados_pedras_claras.jpg',1,0);

-- Produto 23: /produtos/aneis/anel_prateado_pedra_clara_folhas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado pedra clara folhas','Anel prateado pedra clara folhas, com acabamento delicado.','Swarovski',1540.00,2540.00,15,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,18.00,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_pedra_clara_folhas.jpg',1,0);

-- Produto 24: /produtos/aneis/anel_prateado_flores_pedras_claras_02.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado flores pedras claras 02','Anel prateado flores pedras claras 02, com acabamento delicado.','Tous',1440.00,2380.00,16,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,18.50,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_flores_pedras_claras_02.jpg',1,0);

-- Produto 25: /produtos/aneis/anel_prateado_solitario_pedra_clara.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado solitário pedra clara','Anel prateado solitário pedra clara, com acabamento delicado.','Pandora',1610.00,2660.00,17,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.00,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_solitario_pedra_clara.jpg',1,0);

-- Produto 26: /produtos/aneis/anel_prateado_solitario_pedra_branca_02.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado solitário pedra branca 02','Anel prateado solitário pedra branca 02, com acabamento delicado.','Thomas Sabo',1660.00,2740.00,5,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.50,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_solitario_pedra_branca_02.jpg',1,0);

-- Produto 27: /produtos/aneis/anel_rose_pedra_rosa_cruzado.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel rosé pedra rosa cruzado','Anel rosé pedra rosa cruzado, com acabamento delicado.','Tous',1610.00,2660.00,6,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,20.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'ROSA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedra_rosa_cruzado.jpg',1,0);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedra_rosa_cruzado_sem_fundo.png',0,1);

-- Produto 28: /produtos/aneis/anel_dourado_pedras_claras_floral.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado pedras claras floral','Anel dourado pedras claras floral, com acabamento delicado.','Pandora',1570.00,2590.00,7,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,17.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_pedras_claras_floral.jpg',1,0);

-- Produto 29: /produtos/aneis/anel_dourado_pedra_clara_oval.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado pedra clara oval','Anel dourado pedra clara oval, com acabamento delicado.','Swarovski',1510.00,2490.00,8,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,17.50,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_pedra_clara_oval.jpg',1,0);

-- Produto 30: /produtos/aneis/anel_rose_pedra_clara_solitario.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel rosé pedra clara solitario','Anel rosé pedra clara solitario, com acabamento delicado.','Tous',1430.00,2360.00,9,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,18.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedra_clara_solitario.jpg',1,0);

-- Produto 31: /produtos/aneis/anel_dourado_solitario_pedra_clara.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado solitário pedra clara','Anel dourado solitário pedra clara, com acabamento delicado.','Thomas Sabo',1780.00,2940.00,10,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,18.50,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_solitario_pedra_clara.jpg',1,0);

-- Produto 32: /produtos/aneis/anel_prateado_pedra_clara_halo.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel prateado pedra clara halo','Anel prateado pedra clara halo, com acabamento delicado.','Pandora',1550.00,2560.00,11,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.00,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_prateado_pedra_clara_halo.jpg',1,0);

-- Produto 33: /produtos/aneis/anel_rose_pedras_escuras.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel rosé pedras escuras','Anel rosé pedras escuras, com acabamento delicado.','Tous',1610.00,2660.00,12,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.50,(SELECT id FROM materiais WHERE nome='OURO'),1,'ESCURA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedras_escuras.jpg',1,0);

-- Produto 34: /produtos/aneis/anel_dourado_pedra_clara_oval_detalhes.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado pedra clara oval detalhes','Anel dourado pedra clara oval detalhes, com acabamento delicado.','Swarovski',1740.00,2870.00,13,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,20.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_pedra_clara_oval_detalhes.jpg',1,0);

-- Produto 35: /produtos/aneis/anel_rose_pedra_azul_clara.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel rosé pedra azul clara','Anel rosé pedra azul clara, com acabamento delicado.','Thomas Sabo',2050.00,3380.00,14,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,17.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'AZUL');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedra_azul_clara.jpg',1,0);

-- Produto 36: /produtos/aneis/anel_rose_pedra_verde.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel rosé pedra verde','Anel rosé pedra verde, com acabamento delicado.','Pandora',1730.00,2850.00,15,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,17.50,(SELECT id FROM materiais WHERE nome='OURO'),1,'VERDE');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedra_verde.jpg',1,0);

-- Produto 37: /produtos/aneis/anel_dourado_solitario_pedra_clara_02.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado solitário pedra clara 02','Anel dourado solitário pedra clara 02, com acabamento delicado.','Tous',1950.00,3220.00,16,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,18.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_solitario_pedra_clara_02.jpg',1,0);

-- Produto 38: /produtos/aneis/anel_rose_pedra_vermelha.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel rosé pedra vermelha','Anel rosé pedra vermelha, com acabamento delicado.','Swarovski',1760.00,2900.00,17,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,18.50,(SELECT id FROM materiais WHERE nome='OURO'),1,'VERMELHA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_rose_pedra_vermelha.jpg',1,0);

-- Produto 39: /produtos/aneis/anel_dourado_pedra_clara_lateral.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (3,3,'Anel dourado pedra clara lateral','Anel dourado pedra clara lateral, com acabamento delicado.','Pandora',2220.00,3660.00,5,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO aneis (idDoProduto,diametroEmMm,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,19.00,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/aneis/anel_dourado_pedra_clara_lateral.jpg',1,0);

-- Produto 40: /produtos/brincos/brincos_dourados_flores_pendentes.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados flores pendentes','Brincos dourados flores pendentes, para um visual elegante.','Swarovski',2300.00,3800.00,6,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_flores_pendentes.jpg',1,0);

-- Produto 41: /produtos/brincos/brincos_prateados_pedras_claras_pendentes.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos prateados pedras claras pendentes','Brincos prateados pedras claras pendentes, para um visual elegante.','Pandora',2230.00,3680.00,7,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_prateados_pedras_claras_pendentes.jpg',1,0);

-- Produto 42: /produtos/brincos/brincos_prateados_pedras_azuis_pendentes.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos prateados pedras azuis pendentes','Brincos prateados pedras azuis pendentes, para um visual elegante.','Tous',2170.00,3580.00,8,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),1,'AZUL');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_prateados_pedras_azuis_pendentes.jpg',1,0);

-- Produto 43: /produtos/brincos/brincos_prateados_florais_pedras_claras.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos prateados florais pedras claras','Brincos prateados florais pedras claras, para um visual elegante.','Swarovski',2310.00,3810.00,9,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_prateados_florais_pedras_claras.jpg',1,0);

-- Produto 44: /produtos/brincos/brincos_dourados_flores_gotas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados flores gotas','Brincos dourados flores gotas, para um visual elegante.','Tous',2160.00,3560.00,10,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_flores_gotas.jpg',1,0);

-- Produto 45: /produtos/brincos/brincos_prateados_pedras_rosas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos prateados pedras rosas','Brincos prateados pedras rosas, para um visual elegante.','Pandora',2150.00,3550.00,11,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),1,'ROSA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_prateados_pedras_rosas.jpg',1,0);

-- Produto 46: /produtos/brincos/brincos_dourados_argolas_triplas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados argolas triplas','Brincos dourados argolas triplas, para um visual elegante.','Thomas Sabo',2290.00,3780.00,12,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_argolas_triplas.jpg',1,0);

-- Produto 47: /produtos/brincos/brincos_dourados_perolas_florais.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados pérolas florais','Brincos dourados pérolas florais, para um visual elegante.','Tous',2260.00,3730.00,13,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),1,'CLARA');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_perolas_florais.jpg',1,0);

-- Produto 48: /produtos/brincos/conjunto_brincos_dourados_argolas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Conjunto de brincos dourados argolas','Conjunto de brincos dourados argolas, para um visual elegante.','Pandora',2350.00,3880.00,14,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/conjunto_brincos_dourados_argolas.jpg',1,0);

-- Produto 49: /produtos/brincos/brincos_dourados_argolas_pequenas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados argolas pequenas','Brincos dourados argolas pequenas, para um visual elegante.','Thomas Sabo',2680.00,4420.00,15,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_argolas_pequenas.jpg',1,0);

-- Produto 50: /produtos/brincos/brincos_dourados_trevos_pretos.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados trevos pretos','Brincos dourados trevos pretos, para um visual elegante.','Swarovski',2430.00,4010.00,16,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_trevos_pretos.jpg',1,0);

-- Produto 51: /produtos/brincos/brincos_dourados_argolas_texturadas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (5,5,'Brincos dourados argolas texturadas','Brincos dourados argolas texturadas, para um visual elegante.','Pandora',2700.00,4460.00,17,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO brincos (idDoProduto,idDoMaterial,possuiPedra,tipoDePedra)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),0,NULL);
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/brincos/brincos_dourados_argolas_texturadas.jpg',1,0);

-- Produto 52: /produtos/colares/colar_prateado_contas_claras.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (1,1,'Colar prateado contas claras','Colar prateado contas claras, ideal para completar o visual.','Pandora',2520.00,4160.00,5,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO colares (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,48.00,(SELECT id FROM materiais WHERE nome='PRATA'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/colares/colar_prateado_contas_claras.jpg',1,0);

-- Produto 53: /produtos/colares/colar_dourado_camadas_pendentes.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (1,1,'Colar dourado camadas pendentes','Colar dourado camadas pendentes, ideal para completar o visual.','Swarovski',2750.00,4540.00,6,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO colares (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,50.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/colares/colar_dourado_camadas_pendentes.jpg',1,0);

-- Produto 54: /produtos/colares/colar_dourado_camadas_fundo_escuro.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (1,1,'Colar dourado camadas fundo escuro','Colar dourado camadas fundo escuro, ideal para completar o visual.','Tous',2680.00,4420.00,7,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO colares (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,52.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/colares/colar_dourado_camadas_fundo_escuro.jpg',1,0);

-- Produto 55: /produtos/colares/colar_prateado_camadas_pendentes.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (1,1,'Colar prateado camadas pendentes','Colar prateado camadas pendentes, ideal para completar o visual.','Thomas Sabo',2780.00,4590.00,8,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO colares (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,54.00,(SELECT id FROM materiais WHERE nome='PRATA'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/colares/colar_prateado_camadas_pendentes.jpg',1,0);

-- Produto 56: /produtos/colares/colar_dourado_camadas_delicadas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (1,1,'Colar dourado camadas delicadas','Colar dourado camadas delicadas, ideal para completar o visual.','Pandora',2540.00,4190.00,9,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO colares (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,42.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/colares/colar_dourado_camadas_delicadas.jpg',1,0);

-- Produto 57: /produtos/colares/colar_prateado_pendente_forma_y.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (1,1,'Colar prateado pendente forma y','Colar prateado pendente forma y, ideal para completar o visual.','Swarovski',2910.00,4800.00,10,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO colares (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,44.00,(SELECT id FROM materiais WHERE nome='PRATA'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/colares/colar_prateado_pendente_forma_y.jpg',1,0);

-- Produto 58: /produtos/acessorios/acessorio_pedras_vermelhas_pendente.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (6,6,'Acessório pedras vermelhas pendente','Acessório pedras vermelhas pendente, com detalhes decorativos de destaque.','Pandora',2750.00,4540.00,11,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pegas (idDoProduto,idDoMaterial,tipoDeFecho)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='ACO_INOXIDAVEL'),'Fecho decorativo');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/acessorios/acessorio_pedras_vermelhas_pendente.jpg',1,0);

-- Produto 59: /produtos/acessorios/acessorio_prateado_barras_cristais.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (6,6,'Acessório prateado barras cristais','Acessório prateado barras cristais, com detalhes decorativos de destaque.','Swarovski',2660.00,4390.00,12,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pegas (idDoProduto,idDoMaterial,tipoDeFecho)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),'Fecho decorativo');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/acessorios/acessorio_prateado_barras_cristais.jpg',1,0);

-- Produto 60: /produtos/acessorios/acessorio_prateado_cristais_pendente.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (6,6,'Acessório prateado cristais pendente','Acessório prateado cristais pendente, com detalhes decorativos de destaque.','Tous',2940.00,4850.00,13,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pegas (idDoProduto,idDoMaterial,tipoDeFecho)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),'Fecho decorativo');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/acessorios/acessorio_prateado_cristais_pendente.jpg',1,0);

-- Produto 61: /produtos/acessorios/acessorio_dourado_pendente_estrelas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (6,6,'Acessório dourado pendente estrelas','Acessório dourado pendente estrelas, com detalhes decorativos de destaque.','Pandora',2980.00,4920.00,14,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pegas (idDoProduto,idDoMaterial,tipoDeFecho)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='OURO'),'Fecho decorativo');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/acessorios/acessorio_dourado_pendente_estrelas.jpg',1,0);

-- Produto 62: /produtos/acessorios/acessorio_prateado_estrelas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (6,6,'Acessório prateado estrelas','Acessório prateado estrelas, com detalhes decorativos de destaque.','Thomas Sabo',2890.00,4770.00,15,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pegas (idDoProduto,idDoMaterial,tipoDeFecho)
VALUES (@idProduto,(SELECT id FROM materiais WHERE nome='PRATA'),'Fecho decorativo');
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/acessorios/acessorio_prateado_estrelas.jpg',1,0);

-- Produto 63: /produtos/pulseiras/pulseira_dourada_estrelas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseira dourada estrelas','Pulseira dourada estrelas, com acabamento elegante.','Pandora',2930.00,4830.00,16,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,20.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseira_dourada_estrelas.jpg',1,0);

-- Produto 64: /produtos/pulseiras/pulseira_dourada_coracoes_infinito.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseira dourada corações infinito','Pulseira dourada corações infinito, com acabamento elegante.','Tous',3060.00,5050.00,17,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,21.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseira_dourada_coracoes_infinito.jpg',1,0);

-- Produto 65: /produtos/pulseiras/pulseira_prateada_pedras_claras_borboleta.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseira prateada pedras claras borboleta','Pulseira prateada pedras claras borboleta, com acabamento elegante.','Swarovski',2940.00,4850.00,5,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,17.00,(SELECT id FROM materiais WHERE nome='PRATA'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseira_prateada_pedras_claras_borboleta.jpg',1,0);

-- Produto 66: /produtos/pulseiras/pulseira_dourada_flores_coloridas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseira dourada flores coloridas','Pulseira dourada flores coloridas, com acabamento elegante.','Thomas Sabo',3350.00,5530.00,6,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,18.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseira_dourada_flores_coloridas.jpg',1,0);

-- Produto 67: /produtos/pulseiras/pulseira_prateada_flores_brancas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseira prateada flores brancas','Pulseira prateada flores brancas, com acabamento elegante.','Pandora',3280.00,5410.00,7,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,19.00,(SELECT id FROM materiais WHERE nome='PRATA'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseira_prateada_flores_brancas.jpg',1,0);

-- Produto 68: /produtos/pulseiras/pulseira_dourada_larga_no_pulso.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseira dourada larga no pulso','Pulseira dourada larga no pulso, com acabamento elegante.','Tous',3350.00,5530.00,8,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,20.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseira_dourada_larga_no_pulso.jpg',1,0);

-- Produto 69: /produtos/pulseiras/conjunto_pulseiras_douradas_no_pulso.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Conjunto de pulseiras douradas no pulso','Conjunto de pulseiras douradas no pulso, para combinar com diferentes estilos.','Swarovski',3400.00,5610.00,9,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,21.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/conjunto_pulseiras_douradas_no_pulso.jpg',1,0);

-- Produto 70: /produtos/pulseiras/pulseiras_douradas_correntes_finas.jpg
INSERT INTO produtos (idDaCategoria,idDoDepartamento,nome,descricao,marca,custoDeAquisicao,precoDeVenda,stockFisico,stockReservado,activo)
VALUES (2,2,'Pulseiras douradas correntes finas','Pulseiras douradas correntes finas, com acabamento delicado.','Pandora',3360.00,5540.00,10,0,1);
SET @idProduto = LAST_INSERT_ID();
INSERT INTO pulseiras (idDoProduto,comprimentoEmCm,idDoMaterial)
VALUES (@idProduto,17.00,(SELECT id FROM materiais WHERE nome='OURO'));
INSERT INTO imagensDeProdutos (idDoProduto,caminhoDaImagem,imagemPrincipal,ordemDeExibicao)
VALUES (@idProduto,'/produtos/pulseiras/pulseiras_douradas_correntes_finas.jpg',1,0);

COMMIT;

