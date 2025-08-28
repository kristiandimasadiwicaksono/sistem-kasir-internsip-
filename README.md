# Sistem Kasir (Internsip)

A web-based Point of Sale (POS) system developed for internship purposes using Laravel. This application supports product management, sales transactions, and basic financial reporting.

---

## Table of Contents

- [Features](#features)  
- [Tech Stack](#tech-stack)  
- [Requirements](#requirements)  
- [Installation](#installation)  
- [License](#license)

---

## Features

- **Produk**: Tambah, edit, hapus, dan kelola produk (nama produk, barcode, harga beli, harga jual, stok).  
- **Transaksi Kasir**: Input transaksi penjualan, cetak nota (receipt atau A4), dengan pilihan print format.  
- **Laporan**: Lihat ringkasan penjualan harian, bulanan, export laporan sederhana.  
- **Manajemen Pengguna**: (Opsional) Login & logout untuk staf atau petugas kasir.  
- **Pengaturan**: (Opsional) Pengaturan dasar seperti format mata uang atau pajak.

---

## Tech Stack

- [Laravel](https://laravel.com) (PHP framework)  
- Database: MySQL / MariaDB 
- Frontend: Blade templating, TailwindCSS / Vite
- Printer support: Thermal 58mm / 80mm / Dot matrix / A4

---

## Requirements

- PHP >= 8.x  
- Composer  
- MySQL / MariaDB  
- (Opsional) Node.js & npm (jika menggunakan frontend bundler seperti Vite)  
- Web server (built-in `php artisan serve`, atau Apache/Nginx via XAMPP/WAMP/LAMP)

---

## Installation

1. Clone atau fork repositori:  
   ```bash
   git clone https://github.com/kristiandimasadiwicaksono/sistem-kasir-internsip-.git
   cd sistem-kasir-internsip-
   ```
2. Install dependencies via Composer:
    ```bash
    composer install
    ```
3. Copy .env.example ke .env dan sesuaikan konfigurasi database:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4. Buat database MySQL baru, misalnya sistem_kasir, lalu jalankan migration & seeder:
    ```bash
    php artisan migrate --seed
    ```
5. 