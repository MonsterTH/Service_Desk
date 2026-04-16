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

---

## 🚀 Instalação e execução (Docker)

### 1. Clonar o projeto

```bash
git clone https://github.com/MonsterTH/Service_Desk.git
cd service-desk
```
### 2. Subir os Containers

```bash
docker-compose up -d --build
```

### 3. Verificar os containers ativos

```bash
docker ps
```
Deves ver: 
- Service_Desk (app)
- laravel_db (MariaDB)
- adminer

### 4. Entrar no Container e instalar as dependêcias

```bash
docker exec -it Service_Desk bash
composer install
```

### 5. Configurar o ficheiro .env

```.env
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

### 7. Migrar a base de dados e fazer seed

```bash
php artisan migrate
php aritsan migrate:refresh --seed
```

### 8. Instalar e configurar Spatie Permissions

```bash
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
php artisan migrate
```

## Acesso ao sistema

### Api Laravel
http://localhost:8000/api

### Aplicação
http://localhost:8000

### Adminer
http://localhost:8080

O Login do adminer são os dados da .env
