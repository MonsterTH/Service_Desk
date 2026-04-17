# 📌 Service Desk API (Laravel + Docker)

Sistema de gestão de tickets com comentários, autenticação e controlo de permissões por roles usando Spatie Laravel Permission.

---

## 🧱 Stack

* Laravel 13
* PHP 8.3 (Apache)
* MariaDB 10.8
* Laravel Sanctum
* Spatie Laravel Permission
* Docker & Docker Compose
* Adminer (DB UI)
* L5-Swagger

---

## 🚀 Instalação e execução (Docker)

### 1. Clonar o projeto

```bash
git clone https://github.com/MonsterTH/Service_Desk.git
cd service-desk
```

---

### 2. Subir os containers

```bash
docker-compose up -d --build
```

---

### 3. Verificar containers

```bash
docker ps
```

Deves ver:

* Service_Desk (app)
* laravel_db (MariaDB)
* adminer

---

### 4. Entrar no container

```bash
docker exec -it Service_Desk bash
```

---

### 5. Instalar dependências

```bash
composer install
```

---

### 6. Configurar o `.env`

```env
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
```

Gerar chave da aplicação:

```bash
php artisan key:generate
```

---

### 7. Migrar e popular base de dados

```bash
php artisan migrate:fresh --seed
```

---

### 8. Gerar documentação Swagger

```bash
php artisan l5-swagger:generate
```

---

## 📄 Documentação da API (Swagger)

Disponível em:

http://localhost:8000/api/documentation

---

## 🔐 Autenticação

A API utiliza Laravel Sanctum com autenticação via token.

### Login

```http
POST /api/login
```

Body exemplo:

```json
{
  "email": "admin@test.com",
  "password": "password"
}
```

### Utilizar o token

Adicionar no header:

```http
Authorization: Bearer {token}
```

---

## 👤 Utilizadores de teste

Criados automaticamente pelos seeders:

**Admin**

* Email: [admin@test.com](mailto:admin@test.com)
* Password: password

**User**

* Email: [user@test.com](mailto:user@test.com)
* Password: password

---

## 🔑 Roles e Permissões

Sistema gerido com Spatie Laravel Permission.

### Roles disponíveis:

* **Admin**

  * Criar, editar e apagar tickets
  * Gerir utilizadores
  * Ver todos os tickets

* **User**

  * Criar tickets
  * Comentar tickets
  * Ver apenas os seus tickets

---

## 🛡️ Proteção de rotas

Endpoints protegidos com middleware:

```php
auth:sanctum
```

Exemplo:

```http
GET /api/tickets
```

Requer autenticação.

---

## 📊 Validação e respostas HTTP

A API utiliza validação Laravel:

```php
$request->validate([
    'title' => 'required|string|max:255'
]);
```

Status codes utilizados:

* 200 OK
* 201 Created
* 400 Bad Request
* 401 Unauthorized
* 404 Not Found

---

## 🌐 Acesso ao sistema

### API

http://localhost:8000/api

### Swagger

http://localhost:8000/api/documentation

### Aplicação

http://localhost:8000

### Adminer

http://localhost:8080

Login com os dados do `.env`.

---

## ✅ Checklist de entrega

* ✔ API funcional
* ✔ Docker configurado
* ✔ Migrations
* ✔ Seeders com dados reais
* ✔ Autenticação com Sanctum
* ✔ Roles e permissões
* ✔ Utilizadores de teste
* ✔ Swagger funcional
* ✔ Validação e status codes
* ✔ README com instruções

---

## ⚠️ Notas

Após clonar o projeto, deve ser possível executar:

```bash
docker-compose up -d --build
```

E depois:

```bash
php artisan migrate:fresh --seed
```

Sem erros.

Caso o Swagger não esteja instalado, execute:
```bash
composer require "darkaonline/l5-swagger"
```
E depois para publicar:
```bash
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider"
```
