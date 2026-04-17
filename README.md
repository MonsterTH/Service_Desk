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

# 🚀 EXECUÇÃO DO PROJETO

## ✔️ IMPORTANTE

O projeto está totalmente automatizado.

### 1. Clonar o projeto
git clone https://github.com/MonsterTH/Service_Desk.git  
cd Service_Desk  

### 2. Configurar o .env (Renomear o .env.example para .env primeiro)
```bash
APP_NAME="Service Desk"  
APP_URL=http://localhost:8000  

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost/api
```

### 3. Execução

```bash
docker compose up -d --build
```

## 👤 Utilizadores de teste

Admin  
admin@example.com / password  

Agent  
agent@example.com / password  

Employee  
employee@example.com / password  


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


## 🛡️ Segurança
auth:sanctum protege os endpoints autenticados  


## 📊 HTTP status
200 OK  
201 Created  
400 Bad Request  
401 Unauthorized  
404 Not Found  

# 🌐 Acesso

API: http://localhost:8000/api  
Swagger: http://localhost:8000/api/documentation  
Aplicação: http://localhost:8000  
Adminer: http://localhost:8080  
