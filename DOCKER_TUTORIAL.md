# 🐳 Docker Setup - Kompletný návod

---

## 📋 Pred začatím

### Požiadavky
- ✅ Docker Desktop 4.0+ (stiahnite z [oficiálnej stránky](https://www.docker.com/products/docker-desktop/))
- ✅ Min. 4 GB RAM (odporúčame 8 GB)
- ✅ Min. 20 GB voľného miesta na disku

### Inštalácia Docker Desktop
1. Stiahnite a nainštalujte Docker Desktop.
2. Reštartujte počítač.
3. Spustite aplikáciu Docker Desktop.
4. Počkajte, kým sa v lište úloh (systray) objaví 🟢 zelená ikona s textom "Docker Desktop is running".

### Odporúčané nastavenia
Otvorte Docker Desktop → Settings (⚙️):
- **General:** ✅ `Use the WSL 2 based engine`
- **Resources → Advanced:**
  - CPUs: Min. 2 (odporúčame 4)
  - Memory: Min. 4 GB (odporúčame 8 GB)

---

## 🚀 Spustenie projektu

### Krok 1: Otvorte projekt v terminále
Otvorte **PowerShell** alebo **Windows Terminal**:
```powershell
cd C:\xampp\htdocs\Student-internship-submission-platform
```

### Krok 2: Build Docker images
Tento príkaz vytvorí obrazy pre všetky služby definované v `docker-compose.yml`.
```powershell
docker compose build
```
**Čo sa deje:** Sťahujú sa základné obrazy (PHP, Nginx, MariaDB, Node), inštalujú sa PHP rozšírenia a kopírujú konfiguračné súbory.
**Trvanie:** 5-10 minút (len pri prvom spustení).

**Očakávaný výstup:**
```
[+] Building XXs (YY/YY) FINISHED
 => [sisp-app ...] DONE
 => [sisp-queue ...] DONE
```

### Krok 3: Spustite kontajnery
Príkaz spustí všetky služby na pozadí (`-d` flag).
```powershell
docker compose up -d
```
**Očakávaný výstup:**
```
[+] Running 5/5
 ✔ Container sisp-db        Started
 ✔ Container sisp-app       Started
 ✔ Container sisp-nginx     Started
 ✔ Container sisp-queue     Started
 ✔ Container sisp-frontend  Started
```

### Krok 4: Počkajte na automatickú inicializáciu (30-60 sekúnd)
Pri prvom spustení sa automaticky:
- Nainštalujú Composer dependencies
- Vytvorí `.env` súbor z `.env.example`
- Vygeneruje `APP_KEY`
- Spustia databázové migrácie (vrátane `jobs`, `cache`, `sessions`)
- Nainštalujú Laravel Passport OAuth kľúče
- Vytvorí `storage` symlink

---

## ✅ Overenie funkčnosti

### 1. Skontrolujte status kontajnerov

docker compose ps

**Očakávaný výstup:** Všetkých 5 kontajnerov má `STATUS = Up`

NAME            STATUS          PORTS
sisp-app        Up X seconds    
sisp-db         Up X seconds    0.0.0.0:3307->3306/tcp
sisp-frontend   Up X seconds    0.0.0.0:5173->5173/tcp
sisp-nginx      Up X seconds    0.0.0.0:8080->80/tcp
sisp-queue      Up X seconds    


### 2. Skontrolujte logy inicializácie

docker compose logs app | Select-String "Application ready"

**Očakávaný výstup:** `Application ready!`

Alebo pozrite celé logy aplikácie:

docker compose logs app

Hľadajte tieto riadky:

sisp-app | Installing composer dependencies...
sisp-app | Creating .env from .env.example...
sisp-app | Generating application key...
sisp-app | Running database migrations...
sisp-app | Installing Passport keys...
sisp-app | Application ready!


### 3. Test Backend API
Otvorte v prehliadači: **http://localhost:8080**
Mali by ste vidieť úvodnú obrazovku Laravel.

**Test API endpointu:**
Otvorte: `http://localhost:8080/api/debug-auth`
Očakávaná odpoveď: JSON s informáciami o autentifikácii.

### 4. Test Frontend
Otvorte v prehliadači: **http://localhost:5173**
Mali by ste vidieť frontend aplikáciu postavenú na Vue.js.

### 5. Verifikácia databázy

docker compose exec app php artisan migrate:status

**Očakávaný výstup:** Všetky migrácie majú status `Ran`.

**Overenie tabuliek:**

docker compose exec db mysql -u sisp -psisp sisp -e "SHOW TABLES;"

Mali by ste vidieť tabuľky:
- `users`, `students`, `companies`, `internships`, `garants`
- `sessions`, `jobs`, `failed_jobs`, `cache`, `job_batches`
- `oauth_*` (5 tabuliek)

### 6. Test Queue Worker

docker compose logs queue | Select-String "Running"

**Očakávaný výstup:** `[INFO] Running...` alebo `Processing jobs...`

### 7. Test emailovej fronty (queue)

curl http://localhost:8080/test-mail

Potom skontrolujte logy fronty:

docker compose logs -f queue

Mali by ste vidieť informáciu o spracovaní jobu.

---

## 🎯 Hotovo!

Ak všetky testy prešli:
✅ **Backend API:** http://localhost:8080  
✅ **Frontend:** http://localhost:5173  
✅ **Databáza:** `localhost:3307` (user: `sisp`, pass: `sisp`)  
✅ **Všetky migrácie spustené**  
✅ **Queue worker funguje**  
✅ **Passport nakonfigurovaný**

---

## 🔄 Bežné operácie

### Zastavenie

docker compose down

Alebo v **Docker Desktop:** `Containers` → `sisp` → `Stop` (⏸️)

### Spustenie (po zastavení)

docker compose up -d

Alebo v **Docker Desktop:** `Containers` → `sisp` → `Start` (▶️)

### Reštart konkrétneho kontajnera

docker compose restart app
docker compose restart queue
docker compose restart frontend


### Zobrazenie logov

# Všetky logy
docker compose logs -f

# Konkrétny kontajner
docker compose logs -f app
docker compose logs -f queue
docker compose logs -f frontend

(Ukončite pomocou `Ctrl+C`)

### Artisan príkazy

# Migrácie
docker compose exec app php artisan migrate

# Cache clear
docker compose exec app php artisan cache:clear

# Tinker
docker compose exec app php artisan tinker

# Vytvorenie controllera
docker compose exec app php artisan make:controller TestController


### Composer / NPM

# PHP balíky
docker compose exec app composer require vendor/package
docker compose exec app composer update

# NPM balíky (frontend)
docker compose exec frontend npm install package-name


---

## 🛠️ Riešenie problémov

### ❌ Port 8080 je obsadený
**Symptóm:** `port 8080 is already allocated`

**Riešenie 1:** Zastavte službu, ktorá používa port 8080.

netstat -ano | findstr :8080
taskkill /PID <číslo_PID> /F


**Riešenie 2:** Zmeňte port v `docker-compose.yml`.
```yaml
nginx:
  ports:
    - "8081:80"  # namiesto 8080:80
```
Potom:

docker compose down && docker compose up -d

Backend bude dostupný na `http://localhost:8081`.

### ❌ Cannot connect to database
**Riešenie:**

# Reštartujte DB a app
docker compose restart db
Start-Sleep -Seconds 10
docker compose restart app

# Skontrolujte DB logy
docker compose logs db


### ❌ Frontend nefunguje
**Riešenie:**

# Reštartujte frontend
docker compose restart frontend

# Skontrolujte logy
docker compose logs -f frontend

Hľadajte: `VITE vX.X.X ready in XXX ms`. Ak chýba, rebuildnite:

docker compose down
docker compose build --no-cache frontend
docker compose up -d


### ❌ Queue worker nespracováva joby
**Riešenie:**

# Reštart
docker compose restart queue

# Skontrolujte logy
docker compose logs -f queue

# Manuálne spracovanie jedného jobu
docker compose exec app php artisan queue:work --once

# Overenie obsahu jobs tabuľky
docker compose exec db mysql -u sisp -psisp sisp -e "SELECT * FROM jobs;"


### ❌ Permission denied na storage/
**Riešenie:**

docker compose exec app chmod -R 775 storage bootstrap/cache


### ❌ Docker Desktop sa nespustí
**Riešenie:**
1. Ukončite Docker Desktop.
2. Otvorte Task Manager a ukončite všetky "Docker" procesy.
3. Spustite Docker Desktop znova a počkajte 2-3 minúty.

### ❌ Kontajner zlyhal (Status = Exited)
**Riešenie:**

# Pozrite logy zlyhaného kontajnera
docker compose logs <názov_kontajnera>

# Príklad
docker compose logs app

Hľadajte chybovú hlášku na konci logov.

### 🔄 Kompletný reset (POZOR: Vymaže databázu!)

# Zastavte a vymažte všetko vrátane volumes
docker compose down -v

# Rebuild
docker compose build --no-cache

# Spustite
docker compose up -d


---

## 📊 Docker Desktop UI

### Ako otvoriť Docker Desktop
1. Kliknite na ikonu 🐳 v lište úloh (systray).
2. Zvoľte "Dashboard".

### Kontrola statusu
1. V ľavom menu: **Containers**.
2. Nájdite: `student-internship-submission-platform`.
3. Rozkliknite → mali by ste vidieť 5 kontajnerov so zelenou bodkou (●).

### Zobrazenie logov v UI
1. `Containers` → kliknite na názov kontajnera (napr. `sisp-app`).
2. Tab **Logs** → real-time logy.
3. Funkcie: 🔍 Search, 📋 Copy, 💾 Save.

### Reštart kontajnera v UI
1. `Containers` → kliknite na kontajner.
2. Vpravo hore: 🔄 Restart.

### Štatistiky (CPU, RAM)
1. `Containers` → kliknite na kontajner.
2. Tab **Stats** → real-time grafy (CPU, Memory, Network, Disk).

---

## 💡 Užitočné tipy

### Rýchle čistenie miesta

# Vymaže nepoužívané images
docker image prune -a

# Vymaže nepoužívané volumes
docker volume prune

# Vymaže všetko nepoužívané
docker system prune -a


### Prístup do shellu kontajnera

docker compose exec app sh


### Prístup do databázy

docker compose exec db mysql -u sisp -psisp sisp

Alebo cez GUI tool (DBeaver, HeidiSQL):

Host: localhost
Port: 3307
User: sisp
Password: sisp
Database: sisp


### Sledovanie live logov

docker compose logs -f --tail=50


### Kopírovanie súborov z kontajnera

docker compose cp app:/var/www/html/storage/logs/laravel.log ./laravel.log


---

## 📚 Často kladené otázky

### Ako dlho trvá prvé spustenie?
- Build: 5-10 minút
- Startup + inicializácia: 30-60 sekúnd

### Musím niečo manuálne konfigurovať?
Nie! Všetko sa nakonfiguruje automaticky cez `entrypoint.sh`.

### Kde sa ukladajú dáta?
V Docker volumes, ktoré sú perzistentné:
- `db_data` (databáza), `backend_vendor`, `backend_storage`, `frontend_node_modules`.

### Ako vymažem databázu?

docker compose down -v  # -v vymaže volumes
docker compose up -d    # vytvorí novú, čistú DB


### Kde sú OAuth kľúče?
V `backend/storage/oauth-private.key` a `oauth-public.key`. Generujú sa automaticky.

### Ako pridám novú PHP extension?
1. Upravte `docker/php/Dockerfile`.
2. Pridajte `docker-php-ext-install <extension>`.
3. Rebuildnite image: `docker compose build app`.
4. Reštartujte: `docker compose up -d`.

### Docker je pomalý na Windows
**Odporúčania:**
1. Použite WSL2 backend (Settings → General).
2. Zvýšte pridelené zdroje (Settings → Resources → Advanced).
3. Vypnite antivírusovú kontrolu na Docker volumes.

---

## 🎓 Cheatsheet príkazov


# === Základné operácie ===
docker compose up -d              # Spustenie
docker compose down               # Zastavenie
docker compose restart <service>  # Reštart služby
docker compose ps                 # Status
docker compose logs -f            # Logy

# === Artisan ===
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan cache:clear

# === Composer ===
docker compose exec app composer install
docker compose exec app composer require vendor/package

# === Databáza ===
docker compose exec db mysql -u sisp -psisp sisp
docker compose exec app php artisan migrate:status

# === Debugging ===
docker compose logs -f app
docker compose exec app sh
docker compose exec app tail -f storage/logs/laravel.log

# === Čistenie ===
docker compose down -v           # Vymaže volumes (aj DB!)
docker image prune -a            # Vymaže nepoužívané images
docker system prune -a           # Vymaže všetko nepoužívané

# === Rebuild ===
docker compose build --no-cache
docker compose up -d


---

## 🆘 Stále nefunguje?

1. **Skontrolujte logy:** `docker compose logs -f`
2. **Overte status:** `docker compose ps`
3. **Skúste reset:** `docker compose down -v && docker compose build --no-cache && docker compose up -d`
4. **Pozrite Docker Desktop logy:** `Containers` → kliknite na červený kontajner → `Logs`.
5. **Reštartujte Docker Desktop:** Ikona v lište úloh → `Quit Docker Desktop` → Spustite znova.

---

**Projekt je pripravený na použitie! 🚀**

Backend: http://localhost:8080  
Frontend: http://localhost:5173

