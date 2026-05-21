Este projeto tem como conceito a criação de um sistema hospitalar completo, com login,
agendamento, prontuário médico, portal do paciente, financeiro, farmácia... utilizando frameworks, bibliotecas atuais, padrões de projeto e estilização.

Aqui vai uma lista de frameworks e bibliotecas padrões para utilizarmos, isso são bases, os grupos podem usar bibliotecas adicionais para cumprir seus objetivos de projeto:
    Backend: Laravel.

    Frontend: React.

    Segurança: JWT via tymon/jwt-auth.

    Comunicação Front->Back: Axios + React Query.

    Documentação Obrigatória: L5-Swagger

    Design: Tailwind CSS.

API:
Rotas web:
GET    /consultas                                       retorna a view com a lista de consultas através do método index de ConsultaController
GET    /consultas/form                                  retorna a view com o formulário para criar uma nova consulta através do método create de ConsultaController
POST   /consultas                                       recebe os dados do formulário e salva a nova consulta no banco de dados através do método store
DELETE /consultas/{consulta}                            recebe o id da consulta a ser deletada e remove do banco de dados através do método destroy
GET    /consultas/{consulta}                            recebe o id da consulta e retorna a view com os detalhes da consulta através do método show
GET    /consultas/{consulta}/edit                       recebe o id da consulta e retorna a view com o formulário para editar a consulta através do método edit
PUT    /consultas/{consulta}                            recebe os dados do formulário de edição e atualiza a consulta no banco de dados através do método update

Rotas api:
GET    consultas/fila/hoje                              retorna a lista de consultas agendadas para o dia atual, incluindo os detalhes de cada consulta (diagnósticos, receitas e solicitações de exame)
GET    pacientes/{idPaciente}/historico                 retorna o histórico de consultas de um paciente específico através do método historico

GET    consultas/{idConsulta}/receitas                  retorna a lista de receitas associadas à consulta
GET    consultas/{idConsulta}/receitas/{id}             retorna os detalhes da receita, incluindo os medicamentos prescritos

GET    consultas/{idConsulta}/solicitacoes-exame        lista as solicitações de exame de uma consulta
GET    consultas/{idConsulta}/solicitacoes-exame/{id}   retorna a solicitação de exame com os detalhes, incluindo os itens solicitados

EQUIPE 3

para iniciar projeto
-> C:\php precisa ser versao 8.3 ou maior
-> Em php.ini precisa remover o ; (comentário) das extensões:
extension=curl
extension=fileinfo
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=sqlite3
extension=zip
extension=iconv

-> Copiar arquivo .env.example e renomear para .env

-> Editar arquivo .env na pasta principal para incluir mysql ao invés de sqlite com as informações do banco de dados (.env é gitignore, precisa adicionar/editar o arquivo depois de clonar)

-> Executar na pasta: php artisan serve

-> Depois só acessar http://localhost:8000
    -> adicionar /consultas para acessar a lista de consultas