# E‑carpool

A lightweight PHP web application that connects drivers and passengers for shared rides.  
The platform includes an admin dashboard, email notifications via **PHPMailer**, and a MySQL database schema ready to import.

---

## Overview

E‑carpool enables users to:

* **Create** and **search** ride offers.
* **Book** seats in existing rides.
* **Receive** email confirmations and reminders.
* **Manage** rides, users, and notifications from a secure admin panel.

The project is built with plain PHP (no heavy frameworks) to keep the codebase easy to understand and extend.

---

## Features

| ✅ | Feature |
|---|---------|
| ✔️ | User registration & login |
| ✔️ | Driver ride posting (date, time, route, seats) |
| ✔️ | Passenger ride search & booking |
| ✔️ | Email notifications (registration, booking, cancellation) |
| ✔️ | Admin dashboard for managing users, rides, and email templates |
| ✔️ | Internationalised PHPMailer language files (over 30 languages) |
| ✔️ | Ready‑to‑import MySQL schema (`Database/ecarpool_db.sql`) |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP ≥ 7.4 |
| **Database** | MySQL / MariaDB |
| **Email** | PHPMailer (bundled in `admin/PHPMailer/`) |
| **Dependency Management** | Composer |
| **Front‑end** | HTML5, CSS3, minimal JavaScript (Bootstrap optional) |
| **Version Control** | Git |

---

## Installation

> **Prerequisites**  
> - PHP 7.4+ with `mysqli` and `openssl` extensions enabled  
> - Composer installed globally  
> - MySQL server (or MariaDB)  

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/E-carpool.git
   cd E-carpool
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

   This will install PHPMailer and its dependencies as defined in `admin/PHPMailer/composer.json`.

3. **Create the database**

   ```bash
   mysql -u root -p < Database/ecarpool_db.sql
   ```

   Adjust the credentials in `config.php` (or your environment file) to match your MySQL user.

4. **Configure the application**

   *Copy the sample config and edit the values.*

   ```bash
   cp config.sample.php config.php
   ```

   Edit `config.php`:

   ```php
   // Database
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ecarpool');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');

   // Email (PHPMailer)
   define('MAIL_HOST', 'smtp.example.com');
   define('MAIL_USERNAME', 'your_email@example.com');
   define('MAIL_PASSWORD', 'YOUR_OWN_API_KEY');   // <-- replace with your SMTP password or API key
   define('MAIL_PORT', 587);