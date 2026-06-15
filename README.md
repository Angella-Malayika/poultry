Here’s a comprehensive `README.md` for your **Kalungu Quality Feeds** project, based on the code we’ve fixed and the features we’ve implemented.

You can place this file in the root of your project (`C:\xampp\htdocs\poultry\README.md`).

---

# 🐔 Kalungu Quality Feeds

A full‑stack web application for a poultry feed business – customers can browse products, add to cart, place orders, and leave feedback. Admins manage products, orders, messages, and user feedback.

![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Folder Structure](#folder-structure)
- [Admin Panel](#admin-panel)
- [Common Fixes](#common-fixes)
- [Troubleshooting](#troubleshooting)
- [Credits](#credits)

---

## ✨ Features

### 👤 Customer Side
- User registration & login (email/password)
- Browse products by category (Broilers, Layers, Feeds, Kenbro Chicks, etc.)
- Product details page with description, benefits, usage info
- Add to cart & view cart
- Place orders with delivery address & preferred date
- View order history
- Submit complaints / appreciation (feedback) with order ID
- Contact form (sends email & stores in DB)

### 🔐 Admin Side
- Secure admin login (role‑based)
- Dashboard with order statistics & delivery trends (Chart.js)
- Add / edit / delete products (with image upload)
- Manage orders (update status: pending → approved → delivered)
- View customer messages (mark as read/unread)
- View feedback/complaints (resolve/reopen)
- Login activity log
- Upload poultry photos (stored in database BLOB)

---

## 🛠️ Tech Stack

| Layer       | Technology                                     |
|-------------|------------------------------------------------|
| Backend     | PHP 8.2 (native, no framework)                |
| Database    | MySQL 8.0                                      |
| Frontend    | HTML5, CSS3, Bootstrap 5.3, Chart.js           |
| Icons       | Font Awesome 6 / Bootstrap Icons               |
| Email       | PHPMailer (SMTP) + Gmail App Password          |
| Session     | PHP native sessions                            |
| Cart        | Session‑based cart helpers                     |

---

## 💻 Installation

### Prerequisites
- XAMPP / WAMP / LAMP with PHP ≥ 8.2 and MySQL
- Composer (for PHPMailer)

### Steps

1. **Clone the repository** into your web server’s root directory (e.g. `C:\xampp\htdocs\poultry`).

2. **Install PHPMailer** (for email functionality):
   ```bash
   cd C:\xampp\htdocs\poultry
   composer require phpmailer/phpmailer
   ```

3. **Start Apache & MySQL** from XAMPP control panel.

4. **Import the database** – use the provided `poultry.sql` file:
   - Open phpMyAdmin → create database `Poultry`
   - Import `poultry.sql` from the project root

   > If you need to populate products, run `setup_products.php` *after* logging in as admin (see below).

5. **Configure email** – edit `email_config.php` with your Gmail credentials:
   ```php
   'smtp_username' => 'your.email@gmail.com',
   'smtp_password' => 'your_app_password', // NOT your Gmail password
   ```
   Generate an App Password from your Google account (2‑factor required).

6. **Set file permissions** – ensure `images/products/` folder is writable.

7. **Open the site** in your browser:
   ```
   http://localhost/poultry/index.php
   ```

---

## 🗄️ Database Setup

The database `Poultry` contains these main tables:

| Table            | Purpose                                 |
|------------------|-----------------------------------------|
| `users`          | customer & admin accounts               |
| `categories`     | product categories (broilers, layers…)  |
| `products`       | product catalog                         |
| `orders`         | customer orders                         |
| `messages`       | contact form submissions                |
| `complaints`     | feedback / complaints                   |
| `login_activity` | user login/logout timestamps            |
| `photos`         | images uploaded via admin panel (BLOB)  |

### Initial Admin User
After importing the database, you can manually insert an admin user:

```sql
INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@example.com', '$2y$10$...', 'admin');
```
*(hash a password using `password_hash('your_password', PASSWORD_DEFAULT)`)*

Or use the signup page and then manually change `role` to `admin` in phpMyAdmin.

---

## ⚙️ Configuration

All environment‑specific settings are in `config.php`:

```php
define('BASE_URL', 'http://localhost/poultry');
define('BASE_PATH', dirname(__FILE__));
define('ADMIN_URL', BASE_URL . '/Admin');
define('ASSETS_URL', BASE_URL . '/assets');
```

- **BASE_URL** must match your local/server URL (no trailing slash).
- Session handling is automatically started.

For database credentials, edit `connection.php` (local vs production logic already included).

---

## 📁 Folder Structure

```
poultry/
├── Admin/                 # Admin panel files
│ 
├── assets/                # CSS, JS (only one CSS file: styles.css)
├── images/                # Product images, banners
├── includes/              # Header, footer, cart helpers
│   ├── header.php
│   ├── footer.php
│   └── cart_helpers.php
├── pages/                 # Frontend pages
│   ├── index.php
│   ├── about.php
│   ├── product.php
│   ├── product-details.php (in root)
│   ├── product-category.php
│   ├── cart.php
│   ├── order.php
│   ├── contact.php
│   ├── complaints.php
│   ├── login.php
│   ├── signup.php
│   ├── profile.php
│   ├── logout.php
│   ├── my_orders.php
│   └── ...
├── vendor/                # Composer dependencies (PHPMailer)
├── auth_required.php      # Login check
├── add_to_cart.php        # Cart handler
├── process_message.php    # Contact form processor
├── process_complaint.php  # Feedback processor
├── email_config.php       # SMTP & email functions
├── poultry.sql            # Database dump
├── setup_products.php     # One‑time product population
└── README.md
```

---

## 🧑‍💼 Admin Panel

| URL                                       | Description                     |
|-------------------------------------------|---------------------------------|
| `http://localhost/poultry/Admin/admin.php` | Dashboard with stats & trends   |


**Default admin credentials:**  
(you need to create them manually via SQL or signup + role change)

---

## 🧪 Common Fixes & Tips

### 1. **Images not showing**  
Make sure your product image paths are absolute. We added a helper `get_absolute_image_url()` in `product.php` and `cart.php` – use it for any image.

### 2. **Admin double‑path problem (e.g. `/Admin/Admin/...`)**  
Ensure every link in admin files uses `<?php echo BASE_URL; ?>/Admin/...` – **no extra `/Admin` inside the path** and **always include the slash after BASE_URL**.

### 3. **CSS broken**  
All pages must link **only** to `assets/styles.css`. Remove any `<link>` to `joy.css`, `head.css`, `foot.css`.

### 4. **Session lost after redirect**  
Verify `config.php` is included at the top of every PHP file that uses sessions. `session_start()` is called there.

### 5. **Cart empty after login**  
Cart is session‑based, not linked to user account. It persists as long as the session lives.

### 6. **Email not sending**  
Check `email_config.php` – use a valid Gmail App Password, not your regular password. Enable “less secure apps” is deprecated; use App Passwords.

---

## 🐞 Troubleshooting

| Symptom                               | Likely Solution                                                          |
|---------------------------------------|--------------------------------------------------------------------------|
| `BASE_URL` undefined                  | Add `require_once __DIR__ . '/config.php';` at the top of the file       |
| 404 on `add_to_cart.php`              | Make sure the file is in the **root** folder, not `/pages/`              |
| Product image broken in cart          | Use `get_absolute_image_url($row['image'])` as shown in the fixed code   |
| Admin redirects to `/pages/login.php` | Check that `role === 'admin'` is correct and `BASE_URL` has a slash      |
| `Cannot modify header` error          | Ensure no output (echo, spaces) before `session_start()` or `header()`   |
| PHPMailer class not found             | Run `composer require phpmailer/phpmailer` and check `vendor/autoload.php` |

---

## 🙏 Credits

- Developed by **Angella Malayika**  
- Icons: Font Awesome, Bootstrap Icons  
- Charts: Chart.js  
- Email: PHPMailer  
- Bootstrap framework  



---

## 📧 Contact

For support or inquiries:  
**Email**: nakanwagiangella61@gmail.com
**Phone**: +256 758 555 562 / +256 758 707 297

---

*Happy farming!* 🌾🐔