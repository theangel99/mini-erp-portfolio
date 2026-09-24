# Hitrost.com Setup - Korak po korak navodila

## 🎯 Predpogoji
- ✅ Baza podatkov je že ustvarjena v DirectAdmin
- ✅ GitHub repository je pripravljen
- ✅ Projekt je lokalno pripravljen za deployment

---

## 1️⃣ PUSH NA GITHUB

### A. Ustvari GitHub repository (če še ni)

1. Odpri: https://github.com/new
2. Repository name: `mini-erp-portfolio`
3. Visibility: **Private** (priporočeno)
4. **NE** dodajaj README, .gitignore ali license
5. Klikni **Create repository**

### B. Poveži in pushaj

V **lokalnem terminalu** (v mapi projekta):

```bash
# Dodaj GitHub remote (zamenjaj USERNAME s svojim!)
git remote add origin https://github.com/USERNAME/mini-erp-portfolio.git

# Preimenuj branch v main
git branch -M main

# Push vse na GitHub
git push -u origin main
```

**Opomba:** Če GitHub zahteva authentication, uporabi **Personal Access Token** (ne gesla):
- GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
- Generate new token → Izberi `repo` scope → Generate
- Uporabi ta token kot geslo pri `git push`

---

## 2️⃣ SSH V DIRECTADMIN TERMINAL

1. Prijavi se v DirectAdmin panel
2. Pojdi na: **Advanced Features → Terminal**
3. Odpre se terminal v browseru

---

## 3️⃣ KLONIRAJ PROJEKT NA STREŽNIK

V DirectAdmin Terminal izvedi:

```bash
# Pojdi v mapo domene
cd ~/domains/veberdigital.com/

# Kloniraj projekt (zamenjaj USERNAME!)
git clone https://github.com/USERNAME/mini-erp-portfolio.git mini-erp

# Pojdi v projekt
cd mini-erp
```

**Če GitHub prosi za credentials:**
- Username: tvoj GitHub username
- Password: tvoj Personal Access Token (ne pravo geslo!)

---

## 4️⃣ NASTAVI .ENV DATOTEKO

```bash
# Kopiraj primer datoteko
cp .env.example .env

# Uredi .env datoteko
nano .env
```

V nano editoru **spremeni** naslednje vrstice:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tvoja-domena.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=eek32789_mini_erp_db
DB_USERNAME=eek32789_mini_erp_user
DB_PASSWORD=8rWt3xr8n9rrP3SEpkDx

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mail.veberdigital.com
MAIL_PORT=587
MAIL_USERNAME=info@veberdigital.com
MAIL_PASSWORD=TVOJE_MAIL_GESLO
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@veberdigital.com"
MAIL_FROM_NAME="Veber Digital ERP"
```

**Za shranit in zapret nano:**
- `Ctrl + O` (shrani)
- `Enter` (potrdi ime datoteke)
- `Ctrl + X` (zapri)

---

## 5️⃣ NAMESTI ODVISNOSTI IN INICIALIZIRAJ

```bash
# Namesti Composer pakete (brez dev dependencies)
composer install --no-dev --optimize-autoloader --no-interaction

# Generiraj APP_KEY
php artisan key:generate

# Ustvari storage link
php artisan storage:link

# Poženi migracije
php artisan migrate --force

# Seedaj začetne podatke
php artisan db:seed --force
```

---

## 6️⃣ NASTAVI PRAVICE

```bash
# Storage in cache mape morajo biti writeable
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

---

## 7️⃣ NASTAVI SYMLINK ZA PUBLIC MAPO

**Opcija A: Zamenjaj public_html s symlinkom** (priporočeno)

```bash
# Varnostna kopija obstoječe mape
mv ~/domains/veberdigital.com/public_html ~/domains/veberdigital.com/public_html.backup

# Ustvari symlink na Laravel public mapo
ln -s ~/domains/veberdigital.com/mini-erp/public ~/domains/veberdigital.com/public_html
```

**Opcija B: Nastavi v DirectAdmin**

1. DirectAdmin → Domain Setup
2. Najdi svojo (pod)domeno
3. Spremeni **Document Root** v: `mini-erp/public`
4. Shrani

---

## 8️⃣ USTVARI ADMIN UPORABNIKA

```bash
# Odpri Tinker
php artisan tinker
```

V Tinkerju vnesi (spremeni geslo!):

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@veberdigital.com';
$user->password = bcrypt('TvojeVarnoGeslo123!');
$user->save();
exit
```

---

## 9️⃣ NASTAVI CRON JOB

1. DirectAdmin → **Cron Jobs**
2. Klikni **Add Cron Job**
3. Nastavi:
   - **Minute:** `*` (vsako minuto)
   - **Hour:** `*`
   - **Day:** `*`
   - **Month:** `*`
   - **Weekday:** `*`
   - **Command:**
   ```bash
   cd ~/domains/veberdigital.com/mini-erp && php artisan schedule:run >> /dev/null 2>&1
   ```
4. Shrani

---

## 🎉 TESTIRANJE

Odpri browser in obišči:

```
https://tvoja-domena.com/admin
```

Prijavi se z:
- **Email:** `admin@veberdigital.com`
- **Geslo:** tisto kar si nastavil v koraku 8

---

## 🔄 DEPLOYMENT POSODOBITEV (naslednjič)

Ko naredišš spremembe in jih pushaš na GitHub:

**Na strežniku (DirectAdmin Terminal):**

```bash
cd ~/domains/veberdigital.com/mini-erp
bash deploy.sh
```

To avtomatsko:
1. Aktivira maintenance mode
2. Potegne spremembe z GitHuba
3. Posodobi Composer pakete
4. Požene migracije
5. Počisti cache
6. Vrne aplikacijo online

---

## ⚠️ TROUBLESHOOTING

### Napaka: "500 Internal Server Error"

```bash
tail -n 50 ~/domains/veberdigital.com/mini-erp/storage/logs/laravel.log
```

### Cache problemi

```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
```

### Storage link ne deluje

```bash
# Ročno ustvari symlink
ln -s ~/domains/veberdigital.com/mini-erp/storage/app/public ~/domains/veberdigital.com/mini-erp/public/storage
```

### Permissions napake

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 📝 POMEMBNO

- ✅ **Assete gradimo LOKALNO** (`npm run build`), ne na strežniku
- ✅ **`.env` datoteka NI v git** - varna je na strežniku
- ✅ **Cron job skrbi za queue** - ni potreben supervisor
- ✅ **Database drivers** za vse (cache, session, queue)

---

## ✅ CHECKLIST

- [ ] GitHub repository ustvarjen
- [ ] Projekt pushtan na GitHub
- [ ] Projekt kloniran na strežnik
- [ ] `.env` datoteka nastavljena
- [ ] Composer paketi nameščeni
- [ ] Migracije požgane
- [ ] Pravice nastavljene
- [ ] Public symlink ustvarjen
- [ ] Admin uporabnik ustvarjen
- [ ] Cron job nastavljen
- [ ] Aplikacija deluje v browserju

---

Ko vse deluje, lahko nadaljuješ z **Fazo 2 - Ponudbe**! 🚀
