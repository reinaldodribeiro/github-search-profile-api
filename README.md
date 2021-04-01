# GitHub Search profile

## Reinaldo D. Ribeiro

Está é a API do GitHub search profile.
## Requisitos para rodar o projeto

É necessário ter algumas tecnologias instaladas na maquina como:
- Php 5.6 ou posterior.
- Composer.
- PostgreSQL

## Configuração Inicial:
Após clonar o projeto é necessário realizar algumas configurações:

- 1º: Rodar o seguinte comando:
```
composer install
````
- 2º: É necessário criar um banco de dados. O nome para o mesmo fica a critério, pois a conexão é configurada no arquivo ".env". Aqui em minha máquina criei um database com nome "github_profiles".
- 3º: Criar um arquivo de configuração na raiz do projeto com o nome ".env", e utilizar como base o arquivo ".env_example", e configurar as variaveis de conexão com banco de dados.
 
 ```
 DB_CONNECTION=pgsql
 DB_HOST=127.0.0.1
 DB_PORT=5432
 DB_DATABASE=github_profiles
 DB_USERNAME=postgres
 DB_PASSWORD=postgres
````

- 4º Feito isso, é necessário criar a estrutura do banco de dados. Para isso criei uma migration que já faz isso, então execute o seguinte comando:
```
php artisan migrate
````
- 5º Para rodar o projeto back-end basta rodar o seguinte comando na pasta do projeto:
```
php artisan serve
```` 
Será iniciado na porta 8000.

<b>BaseURL endpoint: http://127.0.0.1:8000/api/v1/ </b>

Endpoints:
````
POST http://127.0.0.1:8000/api/v1/login
````
````
POST http://127.0.0.1:8000/api/v1/logout
````
````
POST http://127.0.0.1:8000/api/v1/user
````
````
GET https://127.0.0.1:8000/api/vi/profiles
GET https://127.0.0.1:8000/api/vi/profiles?is_favorite=
````
````
GET https://127.0.0.1:8000/api/vi/profile/search/{username}
````


