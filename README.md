# PayPilot — Web Application

### Digital Payments for Everyone

> **Send money. Instantly.**

PayPilot is a full-stack fintech web application built as part of CSC336 Web Technologies at COMSATS University Islamabad. Conceptually aligned with services like SadaPay, Easypaisa, and JazzCash — it gives users a digital wallet, real-time transaction dashboard, and complete payment history from their browser.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5 |
| Interactivity | JavaScript, jQuery |
| Backend | PHP 8 |
| Database | MySQL (XAMPP) |
| Communication | AJAX, JSON |
| Auth | PHP Sessions, Cookies |

---

## Pages & Features

**Public Pages**
- `index.html` — Landing page with Bootstrap navbar, hero section, feature cards
- `login.html` — User authentication with Remember Me cookie
- `register.html` — Registration with JS RegEx validation (CNIC, phone, email)

**App Pages**
- `dashboard.php` — Live balance card + AJAX transaction table (no page reload)
- `history.php` — Full transaction history with jQuery tab filter (All/Sent/Received/Top up)
- `profile.php` — User profile with JS view/edit toggle + PHP UPDATE
- `404.html` — Custom error page

**PHP Backend**
- `php/login.php` — Session start + Remember Me cookie
- `php/register.php` — Password hash + MySQL INSERT
- `php/session_check.php` — Auth guard for all protected pages
- `php/get_transactions.php` — AJAX endpoint returning JSON
- `php/profile_update.php` — Handles profile form submission
- `php/logout.php` — Session destroy

---

## Design

| Property | Value |
|----------|-------|
| Primary | `#1a6ef5` — Blue |
| Dark | `#0d2b6e` — Navy |
| Background | `#f0f3f9` — Light Blue-Grey |
| Success | `#00b37e` — Green |
| Error | `#e53e3e` — Red |

---

## Setup

**Prerequisites:**
- XAMPP installed ([apachefriends.org](https://apachefriends.org))
- Any modern browser

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/PayPilot.git

# 2. Move to XAMPP
# Place the PayPilot folder inside C:/xampp/htdocs/

# 3. Start XAMPP
# Enable Apache and MySQL

# 4. Create database
# Go to http://localhost/phpmyadmin
# Create database named 'paypilot'
# Run the SQL from db_setup.sql

# 5. Open the app
# Go to http://localhost/paypilot/index.html
```

---

## Contributors

- Eman Tahir
- Huma Ijaz 

---

## Related

- [PayPilot Mobile](https://github.com/iman-tahir/PayPilot-Mobile) — companion Flutter app (Android & iOS)

---

## License

Academic project. Built for CSC336 Web Technologies at COMSATS University Islamabad.