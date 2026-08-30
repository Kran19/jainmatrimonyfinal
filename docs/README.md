# Digambar Jain Matrimony (Jain Matrimony Platform)
## End-to-End System Documentation & Developer Guide

---

## 📌 1. Project Overview

**Digambar Jain Matrimony** (`digambarjainparichay.com` / `jainmatrimony`) is an exclusive, full-stack community matrimonial platform dedicated to the Digambar Jain Samaj across India and worldwide. It enables eligible candidates and their families to register, build comprehensive horoscope and family-verified profiles, search for prospective life partners using granular community-specific filters, shortlist matches, download printable bio-data PDFs, and manage memberships.

The platform provides a secure, two-stage registration and review workflow, role-based administration (Super Admin vs Sub Admin), dynamic form field customization (EAV), multiple advertisement banner slots, gallery and success story CMS, automated email dispatch with socket-level fallbacks, and real-time visitor tracking.

---

## 🛠️ 2. Core Technology Stack

| Layer | Technology | Description |
|---|---|---|
| **Framework** | **Laravel 12.x** (PHP 8.2+) | Modern MVC framework with strict typing, middleware pipelines, and Eloquent ORM. |
| **Database** | **MySQL 8.0+ / MariaDB** | Relational database with legacy schema backward-compatibility patches and dynamic EAV tables. |
| **Frontend Framework** | **Blade Templates + TailwindCSS CDN** | Responsive mobile-first UI with custom animations and palette tokens (`#1E3A5F` Navy, `#C97B84` Rose Gold). |
| **UI Components** | **FontAwesome 6.7, Swiper 11, AOS, Fancybox 5, SweetAlert2** | Interactive sliders, modal bio-data previews, image galleries, and toast notifications. |
| **Authentication** | **Laravel Multi-Guard (Session)** | Dual guards: `web` (Candidates via `CustomUserProvider`) and `admin` (Admins via Eloquent). |
| **Email & Delivery** | **Multi-Tier SMTP Engine** | Tier 1 Direct SSL Socket SMTP (`smtp.hostinger.com:465`) &rarr; Tier 2 Laravel Mailer &rarr; Tier 3 Native `mail()`. |
| **Deployment** | **Bash / Linux Shared Hosting (Hostinger)** | Automated Git pull, directory provisioning, storage symlink, migration, and permission automation. |

---

## 📂 3. Repository Architecture & Directory Map

```tree
jainmatrimony/
├── app/
│   ├── Auth/
│   │   └── CustomUserProvider.php          # Custom auth provider (Email/Mobile login + legacy fallback)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                      # 17 Admin controllers (Members, Approvals, Payments, CMS, Settings)
│   │   │   │   ├── AccountApprovalController.php
│   │   │   │   ├── AdvertisementController.php
│   │   │   │   ├── BulkEmailController.php
│   │   │   │   ├── BulkWhatsAppController.php
│   │   │   │   ├── CommitteeController.php
│   │   │   │   ├── ContactMessageController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── GalleryController.php
│   │   │   │   ├── MemberController.php
│   │   │   │   ├── MembershipController.php
│   │   │   │   ├── NewsController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── ProfileApprovalController.php
│   │   │   │   ├── RegistrationFieldController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── SuccessStoryController.php
│   │   │   ├── Auth/                       # Candidate and Admin Authentication Controllers
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── User/                       # Authenticated Candidate Controllers
│   │   │   │   ├── ChangePasswordController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── MediaController.php
│   │   │   │   ├── PhotoEditorController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── ProfileSearchController.php
│   │   │   ├── CmsController.php           # Public CMS Pages (About, Contact, Terms, Privacy, Committee)
│   │   │   ├── HomeController.php          # Landing page with dynamic ads & cached listings
│   │   │   ├── ImageController.php         # Secure media streaming & cross-directory path resolution
│   │   │   ├── ProfileWizardController.php # 4-Step Registration Profile Wizard
│   │   │   └── VisitorCountController.php  # Atomic asynchronous unique visitor tracking
│   │   └── Middleware/
│   │       ├── CheckSuperAdmin.php         # RBAC guard for super_admin routes
│   │       ├── EnsureProfileIsCompleted.php# Stage-based routing guard for candidates
│   │       └── PerformanceOptimizationMiddleware.php # Security headers & HTTP caching
│   ├── Models/                             # 21 Eloquent Models (User, Admin, Payment, etc.)
│   ├── Notifications/                      # Mail & Database Notification triggers
│   ├── Providers/
│   │   └── AppServiceProvider.php          # Boot-time schema patches, custom auth provider, view composers
│   ├── Services/
│   │   ├── EmailService.php                # Resilient multi-tier socket email dispatch
│   │   └── OTPService.php                  # 6-digit verification code generator & validator
│   └── helpers.php                         # Global media path resolver, currency & date/time formatters
├── config/                                 # Laravel configurations (auth, app, database, mail, etc.)
├── database/
│   ├── migrations/                         # 17 Migration files (core tables, RBAC, legacy patches)
│   └── seeders/                            # Database seeders
├── docs/                                   # In-depth system documentation (Architecture, Schema, APIs)
├── public/                                 # Web root (assets, entry points, uploaded media)
├── resources/
│   └── views/
│       ├── admin/                          # Blade views for admin panel
│       ├── auth/                           # Authentication & OTP verification views
│       ├── cms/                            # Static/dynamic CMS pages
│       ├── emails/                         # Email Blade templates
│       ├── layouts/                        # App, Admin, Header, and Footer layouts
│       ├── user/                           # User dashboard, profile, search, wizard, pdf-view
│       └── home.blade.php                  # Main landing page
├── routes/
│   ├── console.php                         # Artisan CLI commands
│   └── web.php                             # All web, auth, candidate, admin & API routes
├── deploy.sh                               # Production deployment bash script
└── composer.json                           # PHP dependencies and autoloading
```

---

## 📚 4. Documentation Index

For exhaustive details on specific subsystems, refer to the individual documents:

1. **[System Architecture & Design Patterns](file:///c:/Users/PC/Desktop/Karan%20Sir/jainmatrimony/docs/ARCHITECTURE.md)**: Deep dive into multi-auth, custom user provider, middleware pipelines, and security layers.
2. **[Database Schema & Models](file:///c:/Users/PC/Desktop/Karan%20Sir/jainmatrimony/docs/DATABASE_SCHEMA.md)**: Full catalog of tables, fields, legacy patches, data types, and Eloquent relationships.
3. **[Registration & Verification Workflow](file:///c:/Users/PC/Desktop/Karan%20Sir/jainmatrimony/docs/REGISTRATION_AND_APPROVAL_WORKFLOW.md)**: Stage 1 OTP flow, 4-step wizard, status state machine, and approval lifecycle.
4. **[Modules & Feature Catalog](file:///c:/Users/PC/Desktop/Karan%20Sir/jainmatrimony/docs/MODULES_AND_FEATURES.md)**: Search algorithms, EAV custom fields, media streamer, visitor tracker, ad rotators, and CMS.
5. **[Routes & API Reference](file:///c:/Users/PC/Desktop/Karan%20Sir/jainmatrimony/docs/API_AND_ROUTES.md)**: Complete list of web endpoints, methods, parameters, and middleware authorization.
6. **[Deployment & Operations](file:///c:/Users/PC/Desktop/Karan%20Sir/jainmatrimony/docs/DEPLOYMENT_AND_OPERATIONS.md)**: Deployment script instructions, server requirements, storage permissions, and maintenance recipes.

---

## 🚀 5. Quick Start for Developers

### Prerequisites
- PHP 8.2 or higher (with `pdo_mysql`, `curl`, `gd`, `mbstring`, `openssl`, `sockets`, `xml` extensions enabled).
- Composer 2.x
- MySQL 8.0+ or MariaDB 10.4+

### Local Setup
```bash
# 1. Clone repository
git clone https://github.com/Kran19/jainmatrimonyfinal.git
cd jainmatrimony

# 2. Install dependencies
composer install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Configure Database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=digambarfinal
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run migrations & seeders
php artisan migrate
php artisan db:seed

# 6. Start local development server
php artisan serve
```

---
*Maintained by the Digambar Jain Matrimony Engineering Team.*
