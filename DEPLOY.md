# Navodila za deployment na Hitrost.com DirectAdmin

Projekt je optimiziran za shared hosting okolje brez Node.js, Redis ali supervisorja.

## Predpogoji

- DirectAdmin dostop do hostinga
- SSH/Terminal dostop (prek DirectAdmin Terminal)
- Git nameščen na strežniku
- PHP 8.3+
- MySQL/MariaDB dostop

## Enkratna nastavitev

### 1. Pripravi bazo podatkov

V DirectAdmin ustvari novo MySQL bazo:
- Ime baze: `mini_erp_db`
- Uporabnik: `mini_erp_user`
- Geslo: (generiraj močno geslo)

### 2. Kloniraj projekt na strežnik

SSH v DirectAdmin Terminal:

```bash
cd ~/domains/veberdigital.com/
git clone https://github.com/USERNAME/mini-erp-portfolio.git mini-erp
cd mini-erp
```

### 3. Nastavitev .env datoteke

```bash
cp .env.example .env
nano .env
```

Nastavi naslednje vrednosti:

```env
APP_NAME="Veber Digital ERP"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://erp.veberdigital.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mini_erp_db
DB_USERNAME=mini_erp_user
DB_PASSWORD=tvoje_geslo

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mail.veberdigital.com
MAIL_PORT=587
MAIL_USERNAME=info@veberdigital.com
MAIL_PASSWORD=tvoje_mail_geslo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@veberdigital.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Namestitev odvisnosti in inicializacija

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force
```

### 5. Nastavi pravice za mape

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

### 6. Ustvari symlink za public mapo

DirectAdmin poddomene pogosto kažejo na `public_html`. Projekt potrebuje, da kažejo na `mini-erp/public`.

**Možnost A: Zamenjaj mapo s symlinkom** (priporočeno)

```bash
# Varnostna kopija originalne mape
mv ~/domains/veberdigital.com/erp/public_html ~/domains/veberdigital.com/erp/public_html.backup

# Ustvari symlink
ln -s ~/domains/veberdigital.com/mini-erp/public ~/domains/veberdigital.com/erp/public_html
```

**Možnost B: Spremeni document root v DirectAdmin**

V DirectAdmin panel → Domains → Manage → Domain Setup → spremeni Document Root na:
```
mini-erp/public
```

### 7. Ustvari admin uporabnika

```bash
php artisan tinker
```

V tinkerju izvedi:

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@veberdigital.com';
$user->password = bcrypt('geslo123'); // Spremeni!
$user->save();
exit
```

### 8. Nastavi cron job

V DirectAdmin → Cron Jobs → Add Cron Job:

```
Minute: *
Hour: *
Day: *
Month: *
Weekday: *
Command: cd ~/domains/veberdigital.com/mini-erp && php artisan schedule:run >> /dev/null 2>&1
```

Ta cron job izvaja:
- Queue obdelavo (`queue:work --stop-when-empty`)
- Preverjanje zapadlih računov
- Preverjanje poteklih ponudb

### 9. Test delovanja

Odpri browser in obišči: `https://erp.veberdigital.com/admin`

## Deployment sprememb (posodobitve)

Ko pushate spremembe na GitHub, za deploy uporabite:

```bash
cd ~/domains/veberdigital.com/mini-erp
bash deploy.sh
```

Script avtomatsko:
1. Aktivira maintenance mode
2. Potegne najnovejše spremembe z GitHuba
3. Namesti/posodobi composer pakete
4. Požene migracije
5. Počisti in optimizira cache
6. Deaktivira maintenance mode

## Pomembno za razvoj

### Lokalna gradnja assetov

**Frontend assete gradimo LOKALNO** (ne na strežniku):

```bash
# Lokalno
npm install
npm run build
git add public/build
git commit -m "Build assets"
git push
```

Strežnik **NE** gradí assetov - uporablja že zgrajene iz `public/build` mape.

### Struktura datotek na strežniku

```
~/domains/veberdigital.com/
├── mini-erp/                  # Laravel projekt
│   ├── app/
│   ├── public/               # Public files
│   │   └── build/           # Built assets (commitano v git)
│   ├── storage/
│   └── ...
└── erp/
    └── public_html → ../mini-erp/public  # Symlink
```

## Queue system

Queue sistem deluje prek database driverja in cron job-a:

- Ni potreben supervisor ali stalno tekoč proces
- Cron job vsako minuto požene `queue:work --stop-when-empty`
- Job-i se obdelajo v minuti ali dveh (odvisno od cron frequence)
- Za trenutno obdelavo: `php artisan queue:work --once`

## Troubleshooting

### Napaka: "500 Internal Server Error"

Preveri:
```bash
tail -n 50 storage/logs/laravel.log
```

### Napaka: "Storage link not found"

```bash
php artisan storage:link
```

Če ne deluje:
```bash
# Ročno ustvari symlink
ln -s ~/domains/veberdigital.com/mini-erp/storage/app/public ~/domains/veberdigital.com/mini-erp/public/storage
```

### Cache problemi

```bash
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permissions napake

```bash
chmod -R 775 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

### Queue job-i se ne izvajajo

Preveri cron log:
```bash
grep CRON /var/log/syslog
```

Ročno poženi queue:
```bash
php artisan queue:work --once
```

## Varnost

- ✅ `.env` datoteka ni v git repositoryju
- ✅ `APP_DEBUG=false` v produkciji
- ✅ Močna gesla za baze in admin račune
- ✅ HTTPS obvezen (Let's Encrypt v DirectAdmin)
- ✅ Redno posodabljanje Composer odvisnosti
- ✅ Firewall pravila za SSH dostop

## Monitoring

### Preveri aplikacijo

```bash
php artisan about
```

### Preveri queue status

```bash
php artisan queue:failed
php artisan queue:monitor
```

### Preveri log datoteke

```bash
tail -f storage/logs/laravel.log
```

## Backup

Redni backupi:
1. Baza podatkov (DirectAdmin → MySQL Management → Backup)
2. Storage datoteke (`storage/app`)
3. `.env` datoteka
4. Git repository (že varovan na GitHubu)

Priporočen urnik: dnevno ob 2:00 zjutraj
