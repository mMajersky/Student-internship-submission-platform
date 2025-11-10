# 🎓 Student Internship Submission Platform (SISP)

Webová platforma pre správu študentských stáží a praxí na Univerzite Konštantína Filozofa v Nitre.

## 🚀 Quick Start (Docker)

```powershell
# Build a spustite
docker compose build && docker compose up -d

# Otvorte v prehliadači
# Backend:  http://localhost:8080
# Frontend: http://localhost:5173
```

**Hotovo!** Všetko sa nakonfiguruje automaticky. ✨

## 📚 Dokumentácia

### Docker Setup
- **[🐳 Docker Tutorial](DOCKER_TUTORIAL.md)** - Kompletný návod na spustenie a riešenie problémov

### Architektúra
- **Backend:** Laravel 12 (PHP 8.3)
- **Frontend:** Vue 3 + Vite
- **Database:** MariaDB 11
- **Auth:** Laravel Passport (OAuth 2.0)
- **Queue:** Database driver (pre emaily)
- **PDF:** DomPDF

## 🎯 Features

### ✅ Implementované
- 🔐 **Autentifikácia** - OAuth 2.0 cez Passport
- 👤 **Role-based access** - Admin, Garant, Student, Company
- 📝 **Registrácia študentov** - s overením UKF emailu
- 🏢 **Správa firiem** - kontaktné osoby, adresy
- 📋 **Správa stáží** - vytvorenie, editácia, sledovanie
- 💬 **Komentáre** - späté väzby od garantov
- 📄 **PDF export** - Dohoda o odbornej praxi
- 📧 **Email queue** - asynchrónne posielanie emailov
- 🎨 **Moderné UI** - Bootstrap 5, responsive design

## Auth Setup (Required)

Po prvom štarte aplikácie je potrebné nastaviť Passport a spustiť seed. Vyberte si buď lokálne príkazy, alebo Docker variant podľa toho, ako aplikáciu spúšťate.

- Lokálne:
  - `php artisan migrate:fresh --seed`
  - `php artisan passport:keys --force`
  - `php artisan passport:client --personal --name="Laravel Personal Access Client"`

- Docker:
  - `docker compose exec app php artisan migrate:fresh --seed`
  - `docker compose exec app php artisan passport:keys --force`
  - `docker compose exec app php artisan passport:client --personal --name="Laravel Personal Access Client"`

Poznámka: Ak migrácie zlyhávajú kvôli odlišným timestampom, upravte kolidujúce timestamps v súboroch migrácií manuálne (alebo sa dohodnite v tíme na jednotných timestampoch).

## Default Admin

- Email: `admin@test.com`
- Heslo: `password123`

## Frontend .env (Vite)

Každý dev má iné URL na backend. Nastavte proxy cieľ vo `frontend/.env`:

- Pre Docker: `VITE_PROXY_TARGET=http://localhost:XXXX` (v závislosti od individuálneho configu)
- Pre lokálny backend: `VITE_API_URL=http://localhost:XXXX` (v závislosti od individuálneho configu)

Vite proxy presmeruje požiadavky z frontendu na backend (`/api -> VITE_*`).

## Rýchly Prehľad Príkazov

- Re-run seed + Passport lokálne: 
`php artisan migrate:fresh --seed`
`php artisan passport:keys --force && php artisan passport:client --personal --name="Laravel Personal Access Client"`

- Re-run seed + Passport v Dockeri: 
`docker compose exec app php artisan migrate:fresh --seed` 
`docker compose exec app php artisan passport:keys --force`
`docker compose exec app php artisan passport:client --personal --name="Laravel Personal Access Client"`

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 12
- **PHP:** 8.3+
- **Database:** MariaDB 11 / MySQL 8+
- **Auth:** Laravel Passport
- **PDF:** barryvdh/laravel-dompdf
- **HTML Purifier:** mews/purifier

### Frontend
- **Framework:** Vue 3 (Composition API)
- **Build Tool:** Vite
- **Router:** Vue Router 4
- **State:** Pinia
- **UI:** Bootstrap 5 + Bootstrap Icons
- **Editor:** Quill (Vue Quill)

### DevOps
- **Containerization:** Docker + Docker Compose
- **Web Server:** Nginx
- **PHP-FPM:** 8.3
- **Queue Worker:** Laravel Queue
- **Hot Reload:** Vite HMR

## 📋 Požiadavky

### Lokálne spustenie (bez Dockeru)
- PHP 8.3+
- Composer
- Node.js 20+
- MariaDB 11 / MySQL 8+
- PHP extensions: pdo_mysql, gd, intl, bcmath, exif, zip, pcntl

### Docker spustenie
- Docker Desktop 4.0+ (alebo Docker Engine 20.10+)
- Docker Compose V2
- Min. 4 GB RAM (odporúčame 8 GB)
- Min. 20 GB voľného miesta

## 🚀 Inštalácia

### A) Docker (odporúčané)

```powershell
# 1. Klonujte projekt
git clone <repository-url>
cd Student-internship-submission-platform

# 2. Spustite Docker
docker compose build
docker compose up -d

# 3. Otvorte v prehliadači
# Backend:  http://localhost:8080
# Frontend: http://localhost:5173
```

Hotovo! Všetko je automaticky nakonfigurované.

### B) Lokálne (bez Dockeru)

#### Backend
```bash
cd backend

# Composer dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Databáza (nastavte credentials v .env)
php artisan migrate

# Laravel Passport
php artisan passport:install

# Storage
php artisan storage:link

# Spustite server
php artisan serve
```

#### Frontend
```bash
cd frontend

# NPM dependencies
npm install

# Spustite dev server
npm run dev
```

## 🔧 Konfigurácia

### Environment Variables (`.env`)

```env
# App
APP_NAME=SISP
APP_URL=http://localhost:8080

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=sisp
DB_USERNAME=sisp
DB_PASSWORD=sisp

# Queue
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=log

# University
UNIVERSITY_EMAIL_DOMAINS=student.ukf.sk
```

## 📝 Použitie

### Artisan príkazy
```bash
# Migrácie
docker compose exec app php artisan migrate

# Seedovanie
docker compose exec app php artisan db:seed

# Cache clear
docker compose exec app php artisan cache:clear

# Queue worker
docker compose exec app php artisan queue:work

# Tinker
docker compose exec app php artisan tinker
```

### Databáza
```bash
# Pripojenie do DB
docker compose exec db mysql -u sisp -psisp sisp

# Alebo cez external port
Host: localhost:3307
User: sisp
Password: sisp
Database: sisp
```

### Logy
```bash
# Všetky logy
docker compose logs -f

# Backend
docker compose logs -f app

# Queue worker
docker compose logs -f queue

# Frontend
docker compose logs -f frontend
```

## 🧪 Testovanie

```bash
# Unit + Feature testy
docker compose exec app php artisan test

# Konkrétny test
docker compose exec app php artisan test --filter StudentRegistrationTest
```

## 📦 Produkcia

Pre produkčné nasadenie:

1. **Environment**
```env
APP_ENV=production
APP_DEBUG=false
```

2. **Bezpečnosť**
- Silné heslá
- HTTPS certifikáty
- Rate limiting
- Firewall pravidlá

3. **Optimalizácia**
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

4. **Monitoring**
- Logy (Sentry, Papertrail)
- Uptime monitoring
- Error tracking
- Performance metrics

## 🤝 Prispievanie

1. Fork projekt
2. Vytvorte feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit zmeny (`git commit -m 'Add some AmazingFeature'`)
4. Push do branch (`git push origin feature/AmazingFeature`)
5. Otvorte Pull Request

## 📄 Licencia

This project is part of university coursework at Constantine the Philosopher University in Nitra.

## 👥 Tým

Projekt vytvorený študentmi UKF v Nitre pre kurz **Tímový projekt**.

## 🆘 Podpora

- 📖 **[Docker Tutorial](DOCKER_TUTORIAL.md)** - Kompletný návod na spustenie a riešenie problémov.
- 📝 **[Changelog](backend/CHANGELOG.md)**
- 🐛 **[Issues](../../issues)**

---

**Made with ❤️ at UKF Nitra**
