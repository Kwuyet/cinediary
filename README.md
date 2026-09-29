# CineDiary

A film diary and review community. Members rate and review films, discuss them in threaded comments and build their own film lists. Admins moderate content from a dedicated dashboard.

Solo project by **Nguyễn Khắc Quyết** (Management Information Systems, Thuong Mai University).

## Features

| Area | What it does |
|---|---|
| Accounts | Sign-up verified by email OTP (with resend), password reset by OTP, Google sign-in, profile with avatar and bio, password change |
| Community | Film catalogue with search and filters, reviews with half-star ratings and an optional image, threaded replies and likes on comments, public or private film lists |
| Administration | Dashboard with date-range KPIs and trend chart, moderation queue (hide, restore or delete reviews and comments in bulk), lock accounts and change roles, edit films, import films from CSV |

## Tech stack

- PHP 8.1+ with a hand-written MVC: front controller, router (59 routes), controllers, models, views
- MySQL 5.7+ / MariaDB 10.3+ through PDO prepared statements
- Bootstrap 5 for the public site, Tailwind CSS for the admin area, Chart.js for charts
- SMTP email for OTP, Google OAuth 2.0

```
app/
  controllers/   Auth, Movie, Review, Comment, Library, Profile, Discover, Admin
  core/          Router, Database (PDO singleton), Auth, Validator
  models/        User, Movie, Review, Comment, ListModel
  views/         public pages + admin/ layouts
config/          config.php, routes.php, config.local.example.php
database/        schema.sql, seed.sql
public/          index.php (front controller), assets, uploads
```

## Database

Seven tables: `NguoiDung` (users), `Phim` (films), `DanhGia` (reviews), `BinhLuan` (comments), `BinhLuanThich` (comment likes), `DanhSachTuyChinh` (lists), `DanhSachPhim` (list items).

## Run it locally (XAMPP)

1. Copy the project to `htdocs/CineDiary`.
2. In phpMyAdmin create a database `cinediary` (utf8mb4_unicode_ci), then import `database/schema.sql` and `database/seed.sql`.
3. Copy `config/config.local.example.php` to `config/config.local.php` and set your database password and `'base_path' => '/CineDiary'`.
4. Open http://localhost/CineDiary

Sample accounts (password for all: `password`)

| Role | Username |
|---|---|
| Admin | `admin1` |
| Member | `quyetphim` |

## Notes

- Film posters in the sample data are typographic placeholders, not the official artwork.
- Email OTP and Google sign-in need your own SMTP and Google OAuth credentials in `config.local.php`.
