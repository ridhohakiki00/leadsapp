# Laravel Project - Leads Management System

Sistem manajemen leads untuk keperluan internal dan laporan. Dibangun menggunakan Laravel.

## 🚀 Fitur

-   Manajemen leads
-   Export laporan ke Excel & PDF
-   Filter laporan dinamis

## 📦 Requirements

-   Laravel 10
-   barryvdh/laravel-dompdf: v3.1
-   maatwebsite/excel: v3.1

## ⚙️ Instalasi

```bash
git clone https://github.com/ridhohakiki00/leadsapp.git
cd leadsapp
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
# Edit .env untuk DB_NAME, DB_USER, DB_PASS
php artisan migrate
php artisan db:seed --class=LeadSeeder
php artisan serve
```
