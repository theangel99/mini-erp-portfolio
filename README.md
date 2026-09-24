# Veber Digital Mini ERP

Portfolio demo aplikacija za Veber Digital - Mini ERP sistem za mala slovenska podjetja.

## 🎯 Namen projekta

Demonstracija obvladovanja Laravel + Filament stack-a z realističnim poslovnim primerom:
- ✅ Stranke in kontaktne osebe
- ✅ Artikli/produkti z DDV obračunom
- ✅ Ponudbe z različnimi statusi
- ✅ Računi in plačila
- ✅ PDF generiranje z UPN QR kodo
- ✅ E-pošta z queue sistemom
- ✅ Dashboard s statistiko
- ✅ Vloge in pravice
- ✅ Import/Export podatkov

## 🛠️ Tech Stack

- **Laravel 13** - PHP framework
- **Filament 5** - Admin panel in CRUD
- **MySQL/MariaDB** - Relacijska baza
- **Pest** - Testing framework
- **DomPDF** - PDF generiranje
- **Queue system** - Database driver (brez Redis)
- **Slovenščina** - Celoten UI v slovenskem jeziku

## 📋 Funkcionalnosti (Faza 1 - Osnova)

### ✅ Narejeno
- [x] Laravel 13 + Filament 5 setup
- [x] Slovenski jezik in lokalizacija
- [x] PHP Enumi za tipe in statuse
- [x] Podatkovni model (customers, contacts, products, company_settings)
- [x] Filament Resources z odnosi
- [x] Globalno iskanje
- [x] Factories za testne podatke
- [x] Deployment setup za shared hosting

### 🚧 V načrtu
- [ ] **Faza 2**: Ponudbe z reactive forms in kalkulatorjem
- [ ] **Faza 3**: Računi in plačila z avtomatskim status updateom
- [ ] **Faza 4**: PDF generiranje z UPN QR kodo
- [ ] **Faza 5**: Dashboard s statistiko in chartsi
- [ ] **Faza 6**: Vloge, import/export, demo mode

## 🚀 Deployment

### Lokalni razvoj

```bash
# Clone repository
git clone https://github.com/theangel99/mini-erp-portfolio.git
cd mini-erp-portfolio

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Build assets
npm run dev
```

### Production (Hitrost.com shared hosting)

Sledi navodilom v **`HITROST_SETUP.md`** datoteki - korak po korak setup za DirectAdmin okolje.

Hitri deployment:
```bash
cd ~/domains/veberdigital.com/mini-erp
bash deploy.sh
```

## 📁 Struktura projekta

```
app/
├── Enums/              # PHP enumi za tipe in statuse
├── Models/             # Eloquent modeli
├── Filament/
│   ├── Resources/      # CRUD resources
│   ├── Pages/          # Custom strani
│   └── Widgets/        # Dashboard widgeti
├── Services/           # Business logika
└── ...

database/
├── migrations/         # Shema baze
├── factories/          # Test podatki
└── seeders/            # Seedanje začetnih podatkov
```

## 🔒 Varnost

- ✅ Database drivers za cache/session/queue (ni Redis)
- ✅ CSRF zaščita
- ✅ SQL injection preprečevanje (Eloquent ORM)
- ✅ XSS zaščita (Blade templates)
- ✅ Mass assignment protection
- ✅ Soft deletes za kritične podatke

## 📄 Dokumentacija

- **`CLAUDE.md`** - Projektne specifikacije in način dela
- **`HITROST_SETUP.md`** - Korak-po-korak deployment navodila
- **`DEPLOY.md`** - Splošna deployment dokumentacija

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter=CustomerTest
```

## 📝 License

Portfolio projekt za demonstracijske namene.
