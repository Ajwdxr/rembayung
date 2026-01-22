# 🍛 Rembayung

<div align="center">

![Rembayung](https://img.shields.io/badge/Restaurant-Booking%20System-brown?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.0+-blue?style=for-the-badge&logo=php)
![Supabase](https://img.shields.io/badge/Supabase-Database-green?style=for-the-badge&logo=supabase)

**A modern Malaysian restaurant booking system with elegant design and robust admin management.**

[Features](#-features) • [Tech Stack](#-tech-stack) • [Installation](#-installation) • [Admin Guide](#-admin-panel) • [API](#-api-endpoints)

</div>

---

## ✨ Features

### 🌐 Public Website

- **Beautiful Landing Page** - Stunning hero section with Malaysian-inspired design
- **About Section** - Restaurant story and signature quote
- **Menu Showcase** - Featured dishes with dynamic content from database
- **Gallery** - Photo gallery showcasing restaurant ambiance
- **Online Booking** - Interactive calendar-based reservation system
- **Contact Page** - Contact information and form
- **Multi-Theme Support** - 5 distinct visual themes:
  - Modern Malaysian
  - Rustic Elegance
  - Editorial Food
  - Dark & Moody
  - Signature Rembayung

### 📅 Booking System

- **Interactive Calendar** - Visual date picker with availability status
- **Session Management** - Lunch and dinner sessions with capacity limits
- **Time Slot Selection** - Multiple time slots per session
- **Real-time Availability** - Checks capacity before accepting bookings
- **FIFO Queue System** - First-come-first-served booking with database triggers
- **Form Validation** - Client and server-side validation
- **Booking Confirmation** - Email-ready booking details

### 🔐 Admin Panel

- **Secure Login** - Password-protected admin access
- **Dashboard** - Overview of today's bookings and pending approvals
- **Booking Management** - View, approve, or cancel reservations
- **Session Management** - Configure sessions, time slots, and capacity
- **Closure Management** - Set restaurant closure dates
- **Content Management** - Update Hero, About, Menu, and Gallery content
- **Image Uploads** - Upload and manage images for menu and gallery

---

## 🛠 Tech Stack

| Component    | Technology                             |
| ------------ | -------------------------------------- |
| **Backend**  | PHP 8.0+                               |
| **Database** | Supabase (PostgreSQL)                  |
| **Frontend** | HTML5, CSS3, JavaScript                |
| **Styling**  | TailwindCSS (CDN)                      |
| **Server**   | Apache (XAMPP)                         |
| **Fonts**    | Google Fonts (Inter, Playfair Display) |

---

## 📦 Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (PHP 8.0+)
- [Supabase](https://supabase.com/) account (free tier works)
- Modern web browser

### Step 1: Clone the Repository

```bash
# Navigate to XAMPP htdocs
cd C:\xampp\htdocs

# Clone the repository
git clone https://github.com/ajwdxr/rembayung.git
cd rembayung
```

### Step 2: Configure Supabase

1. Create a new project in [Supabase Dashboard](https://app.supabase.com/)
2. Copy your project **URL** and **anon key**
3. Update `includes/config.php`:

```php
// Supabase Configuration
define('SUPABASE_URL', 'https://your-project.supabase.co');
define('SUPABASE_KEY', 'your-anon-key');
```

### Step 3: Set Up Database

Run the SQL scripts in your Supabase SQL Editor in the following order:

```sql
-- 1. Base schema
database/schema.sql

-- 2. Session tables
database/session_tables.sql

-- 3. Session capacity
database/add_session_capacity.sql

-- 4. Booking capacity trigger (prevents overbooking)
database/add_booking_capacity_trigger.sql

-- 5. Closures table
database/add_closures_table.sql

-- 6. Settings table
database/add_settings_table.sql

-- 7. Content tables (Hero, About, Menu, Gallery)
database/add_content_tables.sql
```

### Step 4: Configure Upload Folders

Ensure the upload directories exist and are writable:

```bash
# Create upload directories
mkdir -p assets/uploads/hero
mkdir -p assets/uploads/about
mkdir -p assets/uploads/menu
mkdir -p assets/uploads/gallery
```

### Step 5: Access the Application

1. Start Apache in XAMPP
2. Open your browser and navigate to:
   - **Website**: `http://localhost/rembayung/`
   - **Admin Panel**: `http://localhost/rembayung/admin/`

---

## 👨‍💼 Admin Panel

### Default Login

| Field        | Value              |
| ------------ | ------------------ |
| **Email**    | admin@rembayung.my |
| **Password** | admin123           |

⚠️ **Important**: Change the default password after first login!

### Admin Features

#### 📊 Dashboard (`/admin/dashboard.php`)

- View today's booking count
- See pending approvals
- Quick access to recent bookings

#### 📋 Bookings Management (`/admin/bookings.php`)

- View all reservations
- Filter by date and status
- Update booking status (pending → confirmed/cancelled)
- View customer details

#### ⏰ Sessions Management (`/admin/sessions.php`)

- Configure lunch/dinner sessions
- Set session start/end times
- Define capacity per session
- Manage time slots within sessions
- Set maximum guests per time slot

#### 🚫 Closures Management (`/admin/closures.php`)

- Set restaurant closure dates
- Add closure reason
- Prevent bookings on closed dates

#### 📝 Content Management (`/admin/content.php`)

Manage website content dynamically:

**Hero Banner**

- Upload hero background image
- Set title, subtitle, and tagline
- Toggle active status

**About Section**

- Update title and description
- Set signature quote and author
- Upload background image

**Menu Items**

- Add/edit/delete menu items
- Set item name, description, price
- Mark items as featured
- Upload item images
- Set display order

**Gallery**

- Upload gallery images
- Set captions
- Manage display order

---

## 📡 API Endpoints

### Public APIs

| Endpoint                    | Method | Description                          |
| --------------------------- | ------ | ------------------------------------ |
| `/api/get_calendar.php`     | GET    | Get calendar data with availability  |
| `/api/get_sessions.php`     | GET    | Get available sessions for a date    |
| `/api/get_availability.php` | GET    | Get detailed availability for a date |
| `/api/booking_submit.php`   | POST   | Submit a new booking                 |

### Admin APIs

| Endpoint                        | Method              | Description           |
| ------------------------------- | ------------------- | --------------------- |
| `/admin/api/update_booking.php` | POST                | Update booking status |
| `/admin/api/session_api.php`    | GET/POST/PUT/DELETE | CRUD for sessions     |
| `/admin/api/closures_api.php`   | GET/POST/DELETE     | Manage closures       |
| `/admin/api/content.php`        | GET/POST/PUT/DELETE | Manage content        |
| `/admin/api/settings_api.php`   | GET/POST            | App settings          |

---

## 📁 Project Structure

```
rembayung/
├── admin/                    # Admin panel
│   ├── api/                  # Admin API endpoints
│   │   ├── closures_api.php
│   │   ├── content.php
│   │   ├── session_api.php
│   │   ├── settings_api.php
│   │   └── update_booking.php
│   ├── includes/             # Admin includes
│   │   ├── header.php
│   │   └── footer.php
│   ├── bookings.php          # Booking management
│   ├── closures.php          # Closure management
│   ├── content.php           # Content management
│   ├── dashboard.php         # Admin dashboard
│   ├── login.php             # Admin login
│   ├── logout.php            # Admin logout
│   └── sessions.php          # Session management
│
├── api/                      # Public API endpoints
│   ├── booking_submit.php
│   ├── get_availability.php
│   ├── get_calendar.php
│   └── get_sessions.php
│
├── assets/                   # Static assets
│   ├── css/
│   │   ├── style.css
│   │   └── themes.css
│   ├── images/
│   ├── js/
│   │   └── app.js
│   └── uploads/              # User uploads
│       ├── hero/
│       ├── about/
│       ├── menu/
│       └── gallery/
│
├── database/                 # SQL schema files
│   ├── schema.sql
│   ├── session_tables.sql
│   ├── add_session_capacity.sql
│   ├── add_booking_capacity_trigger.sql
│   ├── add_closures_table.sql
│   ├── add_settings_table.sql
│   └── add_content_tables.sql
│
├── includes/                 # PHP includes
│   ├── config.php            # Configuration
│   ├── footer.php            # Site footer
│   ├── header.php            # Site header
│   └── supabase.php          # Supabase client
│
├── booking.php               # Booking page
├── contact.php               # Contact page
├── gallery.php               # Gallery page
├── index.php                 # Landing page
├── .gitignore
└── README.md
```

---

## 🗄 Database Schema

### Tables Overview

| Table            | Description              |
| ---------------- | ------------------------ |
| `bookings`       | Customer reservations    |
| `admins`         | Admin users              |
| `sessions`       | Lunch/dinner sessions    |
| `time_slots`     | Time slots per session   |
| `closures`       | Restaurant closure dates |
| `settings`       | App configuration        |
| `hero_content`   | Hero banner content      |
| `about_content`  | About section content    |
| `menu_items`     | Menu items               |
| `gallery_images` | Gallery images           |

### Key Relationships

- `bookings` → `sessions` (session_id)
- `bookings` → `time_slots` (time_slot_id)
- `time_slots` → `sessions` (session_id)

---

## 🎨 Themes

Switch between 5 beautiful themes from the header menu:

| Theme                   | Description                        |
| ----------------------- | ---------------------------------- |
| **Modern Malaysian**    | Vibrant colors with batik patterns |
| **Rustic Elegance**     | Warm, earthy tones                 |
| **Editorial Food**      | Clean, magazine-style layout       |
| **Dark & Moody**        | Sophisticated dark theme           |
| **Signature Rembayung** | Fusion of all themes               |

---

## 🔧 Configuration

### Environment Variables (`includes/config.php`)

```php
// Base URL
define('BASE_URL', 'http://localhost/rembayung');

// Supabase
define('SUPABASE_URL', 'https://your-project.supabase.co');
define('SUPABASE_KEY', 'your-anon-key');

// Admin session timeout (seconds)
define('SESSION_TIMEOUT', 3600);
```

---

## 🐛 Troubleshooting

### Common Issues

**1. Supabase Connection Failed**

- Check your Supabase URL and key in `config.php`
- Ensure your Supabase project is active

**2. 500 Internal Server Error**

- Check PHP error logs in XAMPP
- Verify all required tables exist in Supabase

**3. Images Not Uploading**

- Ensure upload directories exist and are writable
- Check PHP `upload_max_filesize` setting

**4. Booking Not Submitting**

- Check browser console for JavaScript errors
- Verify the `booking_submit.php` API is accessible

---

## 📜 License

This project is open source and available under the [MIT License](LICENSE).

---

## 👨‍💻 Author

**Created by [@ajwdxr](https://github.com/ajwdxr)**

---

<div align="center">

**[⬆ Back to Top](#-rembayung)**

</div>
