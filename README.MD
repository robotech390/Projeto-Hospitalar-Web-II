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
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:SqLDBA+JLuDN+dbcm+nNBzIBZLS8rcEJ7JzQS1adIBg=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=hospital
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

-> Executar na pasta: php artisan serve

-> Depois só acessar http://localhost:8000