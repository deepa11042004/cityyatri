# 🏛️ Club Yatri — Modular PHP Vacation Ownership Platform

Welcome to the **Club Yatri** (यात्री) website platform, engineered as a high-performance, modular multi-page PHP web application designed for luxury vacation ownership, long-term holiday passes, and global timeshare exchanges.

**Legal Entity**: Club Yatri Hotel and Resort Pvt Ltd  
**Architecture**: Modular PHP 8.2+, Semantic HTML5, CSS3 Custom Properties, Vanilla JavaScript, Apache / XAMPP compatible.

---

## 📁 Project Architecture & File Hierarchy

```text
clubyatri/
│
├── index.php                      # Flagship Homepage
├── about.php                      # Company Heritage & Ethical Standards
├── itinerary.php                  # Curated Multi-Day Luxury Journeys
├── hotels-national.php            # Domestic India Luxury Resorts
├── hotels-international.php       # Worldwide Partner Resorts & Exchange
├── rooms.php                      # 4 Suite & Villa Categories Overview
├── room-details.php               # Suite Floorplans & Specifications
├── amenities.php                  # Resort Perks, Wellness Spas & Butlers
├── passes-subscriptions.php       # 4 Holiday Passes & Studio Seasons
├── pass-details.php               # Detailed Pass Dossier & Allocation
├── resort-network.php             # Asset-Backed Network Architecture
├── resort-details.php             # Resort Detail View & Reservation
├── destinations.php               # Destination Hubs Directory
├── gallery.php                    # High-Resolution Visual Showcase
├── faq.php                        # Comprehensive Timeshare FAQs
├── contact.php                    # 24/7 Concierge Hotline & Offices
├── login.php                      # Subscriber Portal Login
├── dashboard.php                  # Authenticated Member Points Ledger
├── booking-request.php            # Vacation Reservation Form
├── callback-request.php           # VIP Consultation Callback Scheduler
├── privacy-policy.php             # Data Protection Policy
├── terms-and-conditions.php       # General Website Terms
├── subscription-terms.php         # Pass Bylaws & Points Banking Rules
├── booking-policy.php             # Advance Notice & Check-in Rules
├── cancellation-refund-policy.php # Statutory Cooling-Off & Refund Rules
├── 404.php                        # Custom 404 Not Found
├── .htaccess                      # Apache Security & SEF Configuration
├── README.md                      # Operational Manual
│
├── includes/                      # Reusable Modular Components
│   ├── config.php                 # Global Configuration & Legal Details
│   ├── helpers.php                # Security, CSRF & Sanitization Utilities
│   ├── header.php                 # Shared HTML Head & Asset Inclusions
│   ├── navbar.php                 # Reusable Navigation Bar & Mobile Drawer
│   ├── footer.php                 # Shared Luxury Footer & Modal
│   ├── breadcrumbs.php            # Accessible Semantic Breadcrumb Trail
│   ├── booking-form.php           # Hero Search & Allocation Widget
│   ├── room-card.php              # Reusable Room Card Component
│   ├── subscription-card.php      # Reusable Holiday Pass Card Component
│   ├── resort-card.php            # Reusable Resort Card Component
│   ├── testimonial-card.php       # Reusable Testimonial Card Component
│   ├── gallery-card.php           # Reusable Gallery Card Component
│   ├── faq-item.php               # Reusable FAQ Accordion Component
│   └── seo.php                    # OpenGraph & Metadata Generator
│
├── actions/                       # Backend Form Handlers & Security
│   ├── submit-inquiry.php         # Pass & General Lead Capture
│   ├── submit-callback.php        # VIP Callback Processor
│   ├── submit-booking-request.php # Vacation Stay Reservation Handler
│   ├── authenticate.php           # Password-Verified Subscriber Auth
│   └── logout.php                 # Secure Session Destruction
│
├── data/                          # Centralized PHP Business Data
│   ├── subscriptions.php          # 4 Holiday Passes & 4 Seasonal Studios
│   ├── destinations.php           # Regional Destination Hubs
│   ├── resorts.php                # Resort Roster & Points Tariffs
│   ├── rooms.php                  # 4 Core Room & Suite Types
│   ├── amenities.php              # Resort Facilities & VIP Services
│   ├── faqs.php                   # Timeshare & Exchange Q&A
│   └── testimonials.php           # Verified Member Stories
│
├── assets/                        # Public Web Assets
│   ├── css/
│   │   ├── style.css              # Obsidian Dark & Warm Gold Luxury Theme
│   │   └── custom.css             # Supplementary Custom Styles
│   ├── js/
│   │   ├── main.js                # Sticky Nav, Modals, Tabs, ROI Calculator
│   │   └── custom.js              # Additional Client Behaviors
│   └── images/                    # Image Asset Directories
│
├── database/
│   └── schema.sql                 # MySQL Schema for Members, Bookings & Inquiries
│
├── storage/                       # Protected File Logs & Audit Trail
│   └── .gitkeep
│
└── project-docs/                  # Project Management & Quality Audits
    ├── PROJECT_PLAN.md
    ├── TASKS.md
    ├── PROGRESS.md
    ├── TEST_REPORT.md
    └── CONTENT_GAPS.md
```

---

## ⚙️ Requirements & Local Setup

### 1. Requirements
- **PHP**: 8.2 or higher
- **Web Server**: Apache (XAMPP / MAMP / LAMP or Built-in PHP CLI Server)
- **Database (Optional)**: MySQL 5.7+ / MariaDB (Only required if connecting live database; all public pages render without database dependency)

### 2. Local XAMPP Setup (macOS / Windows / Linux)

#### Option A: Running under XAMPP Apache
1. Copy or clone this folder into your XAMPP web root:
   - **macOS (XAMPP-VM / Native)**: `/Applications/XAMPP/xamppfiles/htdocs/timeshare/clubyatri/`
   - **Windows**: `C:\xampp\htdocs\timeshare\clubyatri\` or `C:\xampp\htdocs\club-yatri\`
2. Open the **XAMPP Control Panel** and start **Apache**.
3. Open your browser and navigate to:
   ```text
   http://localhost/timeshare/clubyatri/
   ```
   *(The base path auto-detects in `includes/config.php` and dynamically configures all links, stylesheets, and actions).*

#### Option B: Running with PHP Built-in Server
From inside the project folder:
```bash
php -S localhost:8080
```
Open your browser at `http://localhost:8080/`.

---

## 🔑 Demo Subscriber Credentials

To test the authenticated **Member Dashboard** (`dashboard.php`):
- **URL**: `http://localhost:8080/login.php`
- **Member ID**: `CY-8829-PLT`
- **Password**: `Yatri@2026`
*(Password authentication is verified using PHP `password_verify()` with server-side session protection).*

---

## 🔧 Configuring Business & Legal Information

All corporate details are centrally maintained in [`includes/config.php`](file:///Applications/XAMPP/xamppfiles/htdocs/timeshare/clubyatri/includes/config.php):
- **Legal Entity**: `LEGAL_ENTITY_NAME`
- **Toll-Free Helpline**: `CONTACT_PHONE_TOLL_FREE`
- **WhatsApp Concierge**: `CONTACT_WHATSAPP`
- **Executive Email**: `CONTACT_EMAIL`
- **Corporate Address**: `CONTACT_ADDRESS`

Modifying any value in `includes/config.php` automatically propagates across all 26 pages, headers, footers, and modal dialogs.

---

## 🗄️ Database Setup (Optional)

To initialize the MySQL database for persistent member logins, bookings, and inquiry storage:
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin/`).
2. Click **Import** and select [`database/schema.sql`](file:///Applications/XAMPP/xamppfiles/htdocs/timeshare/clubyatri/database/schema.sql).
3. The schema creates tables: `members`, `inquiries`, and `bookings` with sample seed data.

---

## 🚀 Production Deployment (Apache / cPanel)
1. Upload all project files to `public_html` or the desired subfolder.
2. Ensure the `.htaccess` file is present in the web root.
3. Verify that the `storage/` directory has write permissions (`775` or `755`) for inquiry logs.
4. Set production error reporting to log-only in `php.ini`.
