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

👉 Basta executar:

```bash
docker compose up --build
```

# 👤 Utilizadores de teste

Admin  
admin@example.com / password  

Agent  
agent@example.com / password  

Employee  
employee@example.com / password  


# 🔑 Roles

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
