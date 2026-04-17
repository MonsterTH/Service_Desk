# 📌 Service Desk API (Laravel + Docker)

Sistema de gestão de tickets com comentários, autenticação e controlo de permissões por roles usando Spatie Laravel Permission.

---

## 🧱 Stack

- Laravel 13
- PHP 8.3 (Apache)
- MariaDB 10.8
- Laravel Sanctum
- Spatie Laravel Permission
- Docker & Docker Compose
- Adminer (DB UI)
- L5-Swagger

---

## 📌 Endpoints principais

POST /api/login  
GET /api/tickets  
POST /api/tickets  

---

## 🚀 Instalação e execução (Docker)

### 1. Clonar o projeto
git clone https://github.com/MonsterTH/Service_Desk.git  
cd Service_Desk  

### 2. Subir os containers
docker compose up -d --build  

### 3. Verificar containers
docker ps  

Deves ver:
- Service_Desk (app)
- laravel_db (MariaDB)
- adminer

### 4. Entrar no container
docker exec -it Service_Desk bash  

### 5. Instalar dependências
composer install  

### 6. Configurar o .env

APP_NAME="Service Desk"  
APP_URL=http://localhost:8000  

DB_CONNECTION=mysql  
DB_HOST=db  
DB_PORT=3306  
DB_DATABASE=laravel  
DB_USERNAME=laravel  
DB_PASSWORD=secret  

CACHE_DRIVER=file  
QUEUE_CONNECTION=sync  
SESSION_DRIVER=file  

L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost/api
L5_SWAGGER_CONST_HOST=http://localhost/api

### 7. Gerar chave da aplicação
php artisan key:generate  

### 8. Migrar e popular base de dados
php artisan migrate:fresh --seed  

### 9. Instalar Swagger (caso não esteja instalado)
composer require darkaonline/l5-swagger  

### 10. Publicar Swagger
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider"  

### 11. Gerar documentação Swagger
php artisan l5-swagger:generate  

---

## 📄 Swagger
http://localhost:8000/api/documentation  

---

## 🔐 Autenticação

POST /api/login  

Body:
{
  "email": "admin@example.com",
  "password": "password"
}

Header:
Authorization: Bearer {token}

---

## 👤 Utilizadores de teste

Admin  
admin@example.com / password  

Agent  
agent@example.com / password  

Employee  
employee@example.com / password  

---

## 🔑 Roles

Admin:
- gerir tickets
- gerir utilizadores
- ver tudo

Agent:
- gerir os tickets que lhe foram atribuídos
- comentar internamente ou não
- ver todos os tickets criados por si ou atribuídos a si

Employee:
- criar tickets
- comentar
- ver apenas os seus

---

## 🛡️ Segurança
auth:sanctum protege os endpoints autenticados  

---

## 📊 HTTP status
200 OK  
201 Created  
400 Bad Request  
401 Unauthorized  
404 Not Found  

---

## 🌐 Acesso

API: http://localhost:8000/api  
Swagger: http://localhost:8000/api/documentation  
Aplicação: http://localhost:8000  
Adminer: http://localhost:8080  

---

## Como obter o token

Fazer login → copiar o token → utilizar no header Bearer

---

## ⚠️ Nota final

Após clonar:

docker compose up -d --build  
docker exec -it Service_Desk bash  
composer install  
php artisan key:generate  
php artisan migrate:fresh --seed  
php artisan l5-swagger:generate  

O projeto deve funcionar sem erros.
