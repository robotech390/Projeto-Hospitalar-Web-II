Este projeto tem como conceito a criação de um sistema hospitalar completo, com login,
agendamento, prontuário médico, portal do paciente, financeiro, farmácia... utilizando frameworks, bibliotecas atuais, padrões de projeto e estilização.

Aqui vai uma lista de frameworks e bibliotecas padrões para utilizarmos, isso são bases, os grupos podem usar bibliotecas adicionais para cumprir seus objetivos de projeto:
    Backend: Laravel.

    Frontend: React.

    Segurança: JWT via tymon/jwt-auth.

    Comunicação Front->Back: Axios + React Query.

    Documentação Obrigatória: L5-Swagger

    Design: Tailwind CSS.

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

-> Editar arquivo.env na pasta principal para incluir mysql com as informações do banco de dados (.env é gitignore, precisa adicionar/editar o arquivo depois de clonar)

-> Executar na pasta: php artisan serve

-> Depois só acessar http://localhost:8000