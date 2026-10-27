**JOALHERIA ZAMBEZE**  
Sistema web de gestão de joalheria desenvolvido como projecto académico com PHP, MySQL, HTML, CSS e JavaScript.  
**Requisitos**  
- PHP 8.3 ou superior, com PDO, PDO MySQL e extensão Fileinfo (para fotografias).  
- MySQL 8.  
- Navegador web.  
- Visual Studio Code com Live Server ou outro servidor HTTP para o frontend.  
**Configuração da base de dados**  
Executar os scripts pela seguinte ordem:  
1. `populacaoBD/criacao da BD.sql`  cria as tabelas da base de dados `joalheria`. (o scripot elemina a base de dados se existir, depois recria.)  
2. `populacaoBD/populacao.sql` insere os dados iniciais.  
3. `populacaoBD/Usuarios de teste.sql`  opcional; cria contas de demonstração.  
É possível criar clientes pelo frontend ou pela API. Os clientes podem candidatar-se a vendedores, sujeitos à aprovação de um administrador e    atribuição do papel `ADMIN` não é pública: a primeira conta administrativa e preparada pela BD.  
**Configuração do backend**  
Na pasta `backend`, configurar o ficheiro `.env` com os dados locais do MySQL (nomes das variáveis utilizados pelo projecto):  
```  
BDHost=127.0.0.1  
 BDNome=joalheria  
 BDUsuario=utilizador_mysql  
 BDSenha=senha_mysql  
   
```  
**Iniciar a API**  
O projecto utiliza uma API HTTP independente do frontend, organizada em Controllers, Services e Repositories. A partir da raiz do projecto, executar:  
```  
php -S 127.0.0.1:8000 -t backend/public backend/public/index.php  
   
```  
A URL base dos endpoints é:  
```  
http://127.0.0.1:8000/joalheria/api  
   
```  
**Iniciar o frontend**  
Abrir a pasta principal `Joalheria` no Visual Studio Code e iniciar o Live Server na porta `5500`, servindo **a raiz do projecto**, para que os caminhos `/frontend/` e `/produtos/` funcionem.  
Página inicial:  
`http://127.0.0.1:5500/frontend/pages/inicial.html`  
**Acesso ao sistema**  
- **Cliente:** consulta produtos, efectua reservas e pode candidatar-se a vendedor.  
- **Vendedor:** consulta o inventário do seu departamento, gere reservas e regista vendas.  
- **Administrador:** gere o inventário, regista e abastece produtos, consulta vendedores e decide candidaturas. A funcionalidade de relatórios não foi totalmente concluída.  
**Endpoints da API**  
Os caminhos da tabela são **relativos** à URL base `/joalheria/api`. `{id}` representa um número inteiro positivo. `Público` indica que a rota não exige sessão; `Autenticado` indica que requer uma sessão PHP válida. As rotas assinaladas `ADMIN` ou `VENDEDOR` exigem o respectivo papel no nosso backend.........  
**Autenticação e gestão de papéis**  
  
|  |  |  |  |  
| - | - | - | - |  
| **Método** |  **Endpoint** |  **Acesso** |  **Finalidade** |  
| `POST` |  `/auth/signup` |  Público |  Registar um utilizador. |  
| `POST` |  `/auth/login` |  Público |  Iniciar sessão. |  
| `POST` |  `/auth/login/vendedor` |  Público (credenciais de vendedor) |  Iniciar sessão na área de vendedor. |  
| `POST` |  `/auth/login/admin` |  Público (credenciais de administrador) |  Iniciar sessão na área administrativa. |  
| `POST` |  `/auth/logout` |  Sessão existente |  Terminar sessão. |  
| `GET` |  `/auth/eu` |  Autenticado |  Obter dados do utilizador autenticado. |  
| `POST` |  `/auth/recuperarSenha` |  Público |  Actualizar senha |  
| `PUT` |  `/actualizar/vendedor` |  `ADMIN` |  Atribuir papel de vendedor directamente. |  
| `PUT` |  `/actualizar/admin` |  `ADMIN` |  Atribuir papel de administrador. |  
  
   
**Catálogo e reservas**  
  
|  |  |  |  |  
| - | - | - | - |  
| **Método** |  **Endpoint** |  **Acesso** |  **Finalidade** |  
| `GET` |  `/produtos` |  Público |  Listar produtos. |  
| `GET` |  `/produtos/{id}` |  Público |  Consultar um produto. |  
| `GET` |  `/categorias` |  Público |  Consultar categorias. |  
| `GET` |  `/departamentos` |  Público |  Consultar departamentos. |  
| `POST` |  `/reservas` |  Autenticado |  Criar uma reserva. |  
| `GET` |  `/reservas/minhas` |  Autenticado |  Listar as próprias reservas. |  
| `GET` |  `/reservas/{id}` |  Autenticado |  Consultar uma reserva do utilizador. |  
| `DELETE` |  `/reservas/{id}/cancelar` |  Autenticado |  Cancelar a própria reserva. |  
  
   
**Área do vendedor**  
  
|  |  |  |  |  
| - | - | - | - |  
| **Método** |  **Endpoint** |  **Acesso** |  **Finalidade** |  
| `GET` |  `/vendedor/perfil` |  `VENDEDOR` |  Consultar perfil. |  
| `GET` |  `/vendedor/painel` |  `VENDEDOR` |  Consultar indicadores. |  
| `GET` |  `/vendedor/inventario` |  `VENDEDOR` |  Consultar stock do departamento. |  
| `GET` |  `/vendedor/vendas` |  `VENDEDOR` |  Listar vendas. |  
| `POST` |  `/vendedor/vendas` |  `VENDEDOR` |  Registar uma venda. |  
| `GET` |  `/vendedor/reservas` |  `VENDEDOR` |  Consultar reservas do departamento. |  
| `POST` |  `/vendedor/reservas/{id}/finalizar` |  `VENDEDOR` |  Finalizar uma reserva. |  
  
   
**Candidaturas a vendedor**  
  
|  |  |  |  |  
| - | - | - | - |  
| **Método** |  **Endpoint** |  **Acesso** |  **Finalidade** |  
| `POST` |  `/solicitacoes/vendedor` |  `CLIENTE` |  Enviar candidatura a vendedor. |  
| `GET` |  `/solicitacoes/vendedor/minhas` |  Autenticado |  Consultar as próprias candidaturas. |  
| `GET` |  `/admin/solicitacoes/vendedor` |  `ADMIN` |  Consultar candidaturas, com filtro opcional `?estado=PENDENTE`. |  
| `POST` |  `/admin/solicitacoes/vendedor/{id}/decidir` |  `ADMIN` |  Aprovar ou rejeitar. |  
| `GET` |  `/admin/solicitacoes` |  `ADMIN` |  Caminho alternativo para listar candidaturas. |  
  
   
**Administração e inventário**  
  
|  |  |  |  |  
| - | - | - | - |  
| **Método** |  **Endpoint** |  **Acesso** |  **Finalidade** |  
| `GET` |  `/admin/perfil` |  `ADMIN` |  Consultar perfil administrativo. |  
| `GET` |  `/admin/departamentos` |  `ADMIN` |  Consultar departamentos e respectivos dados. |  
| `GET` |  `/admin/inventario` |  `ADMIN` |  Consultar produtos e stock real. |  
| `GET` |  `/admin/produtos/catalogos` |  `ADMIN` |  Consultar catálogos necessários ao formulário de produtos. |  
| `POST` |  `/admin/produtos` |  `ADMIN` |  Registar um novo produto. |  
| `POST` |  `/admin/produtos/{id}/imagens` |  `ADMIN` |  Enviar fotografia de produto (`multipart/form-data`). |  
| `PATCH` |  `/admin/produtos/{id}/stock` |  `ADMIN` |  Acrescentar quantidade ao stock existente. |  
| `GET` |  `/admin/vendas` |  `ADMIN` |  Consultar vendas. |  
| `GET` |  `/admin/vendedores` |  `ADMIN` |  Consultar vendedores. |  
| `GET` |  `/admin/destaques` |  `ADMIN` |  Consultar produtos em destaque por vendas. |  
| `GET` |  `/admin/produtividade?inicio=AAAA-MM-DD&fim=AAAA-MM-DD` |  `ADMIN` |  Consultar produtividade no intervalo. |  
| `GET` |  `/admin/relatorio?inicio=AAAA-MM-DD&fim=AAAA-MM-DD&departamento=1&categoria=1` |  `ADMIN` |  Infelizmente nao conseguimos concluir a tempo essa funcionalidade. |  
  
   
**Formato padrão das requisições e respostas**  
A maioria das requisições que envia dados utiliza `Content-Type: application/json`. O envio de fotografias utiliza `multipart/form-data` com o campo `imagem`. O backend usa **sessões PHP com cookies**, por isso os pedidos autenticados precisam de enviar o cookie da sessão; no frontend utilizamos `credentials: "include"`.  
**Resposta de sucesso com dados**  
```  
{  
   "sucesso": true,  
   "menssagem": "Utilizador autenticado",  
   "dados": {  
     "id": 1,  
     "nome": "Henrique",  
     "sobrenome": "Guambe",  
     "papel": "ADMIN"  
   }  
 }  
   
```  
**Resposta de sucesso sem dados**  
```  
{  
   "sucesso": true,  
   "menssagem": "Sessao Terminada"  
 }  
   
```  
**Resposta de falha de negócio**  
```  
{  
   "sucesso": false,  
   "menssagem": "Reserva não encontrada."  
 }  
   
```  
**Resposta de erro por excepção**  
```  
{  
   "sucesso": false,  
   "ERRO": "É necessário enviar JSON."  
 }  
   
```  
**Códigos HTTP utilizados**  
  
|  |  |  
| - | - |  
| **Código** |  **Significado** |  
| `200` |  Operação efectuada com sucesso. |  
| `201` |  Recurso criado, como utilizador, candidatura ou produto. |  
| `204` |  Resposta à requisição CORS `OPTIONS`, sem conteúdo., tivemos um probelma por isso acababos usando ela. |  
| `400` |  Requisição inválida. |  
| `401` |  Falha de autenticação. |  
| `403` |  Acesso não autorizado para o papel. |  
| `404` |  Recurso ou rota não encontrados. |  
| `409` |  Conflito com o estado actual dos dados. |  
| `415` |  Tipo de conteúdo não aceite. |  
| `422` |  Dados inválidos ou em falta. |  
| `500` |  Erro interno do servidor. |  
  
   
**Exemplos de utilização da API**  
**Login**  
```  
POST /joalheria/api/auth/login  
 Content-Type: application/json  
   
```  
```  
{  
   "email": "admin@joalheria.test",  
   "senha": "12345678"  
 }  
   
```  
**Enviar candidatura a vendedor**  
```  
POST /joalheria/api/solicitacoes/vendedor  
 Content-Type: application/json  
   
```  
```  
{  
   "numeroDoCartao": "CARTAO-123",  
   "idDoDepartamento": 2  
 }  
   
```  
Exemplo de resposta do **Controller**:  
```  
{  
   "sucesso": true,  
   "menssagem": "Candidatura enviada. Aguarda a decisão da administração.",  
   "dados": {  
     "id": 5,  
     "estado": "PENDENTE"  
   }  
 }  
   
```  
O `idDoUsuario` é obtido da sessão, não do corpo do pedido por isso nao pedmos memso que a tabela exiga...  
**Decidir candidatura**  
```  
POST /joalheria/api/admin/solicitacoes/5/decidir  
 Content-Type: application/json  
   
```  
```  
{  
   "decisao": "APROVADA"  
 }  
   
```  
Para rejeitar, utilizar `"REJEITADA"`.  
**Abastecer produto**  
```  
PATCH /joalheria/api/admin/produtos/15/stock  
 Content-Type: application/json  
   
```  
```  
{  
   "quantidade": 12  
 }  
   
```  
O backend acrescenta a quantidade ao stock físico do produto existente.  
**Enviar imagem de produto**  
```  
POST /joalheria/api/admin/produtos/15/imagens  
 Content-Type: multipart/form-data  
   
```  
Enviar um ficheiro JPEG, PNG ou WebP até 5 MB no campo `imagem`. O servidor guarda a fotografia numa pasta associada à categoria, em `/produtos/`, e regista o respectivo caminho na base de dados.  
