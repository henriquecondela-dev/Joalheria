
USE joalheria;
START TRANSACTION;

INSERT INTO usuarios (nome, sobrenome, email, telefone, senhaHash, enderecoResidencial, genero, papel)
VALUES
('Henrique', 'Guambe Admin', 'admin@joalheria.test', '840000101', '$2y$12$/WCrD6XmZ4mJqBEc1xnXoO7RXhm2QZcgKWQPuph4lLsiQ6eUPx3gi', 'Beira', 'MASCULINO', 'ADMIN'),
('Henrique', 'Guambe Vendedor', 'vendedor@joalheria.test', '840000102', '$2y$12$/WCrD6XmZ4mJqBEc1xnXoO7RXhm2QZcgKWQPuph4lLsiQ6eUPx3gi', 'Beira', 'MASCULINO', 'VENDEDOR'),
('Henrique', 'Guambe Cliente', 'cliente@joalheria.test', '840000103', '$2y$12$/WCrD6XmZ4mJqBEc1xnXoO7RXhm2QZcgKWQPuph4lLsiQ6eUPx3gi', 'Beira', 'MASCULINO', 'CLIENTE');

INSERT INTO clientes (idDoUsuario, numeroDoBI, dataDeNascimento, localDeTrabalhoOuEstudo)
SELECT id, 'BI-TESTE-ADM-01', '2000-01-01', 'Universidade Zambeze' FROM usuarios WHERE email = 'admin@joalheria.test'
UNION ALL
SELECT id, 'BI-TESTE-VEN-01', '2000-01-01', 'Universidade Zambeze' FROM usuarios WHERE email = 'vendedor@joalheria.test'
UNION ALL
SELECT id, 'BI-TESTE-CLI-01', '2000-01-01', 'Universidade Zambeze' FROM usuarios WHERE email = 'cliente@joalheria.test';

INSERT INTO gerentes (idDoUsuario, numeroDoCartao, nivelEducacional)
SELECT id, 'CARTAO-ADM-TESTE', 'Superior'
FROM usuarios WHERE email = 'admin@joalheria.test';

INSERT INTO vendedores (idDoUsuario, numeroDoCartao, idDoDepartamento)
SELECT u.id, 'CARTAO-VEN-TESTE', d.id
FROM usuarios u
JOIN departamentos d ON d.nome = 'ANEIS'
WHERE u.email = 'vendedor@joalheria.test';

COMMIT;
