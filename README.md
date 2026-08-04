<p align="center">
  <h1 align="center">🏥 Smart Healthcare Management System</h1>
  <p align="center">
    <strong>A Modern, Multi-Role Hospital Platform, Practice Management & Real-Time Telehealth Portal built with Laravel 12</strong>
  </p>
  <p align="center">
    <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/Laravel-v12.x-FF2D20?style=flat-square&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/Bootstrap-v5.3-7952B3?style=flat-square&logo=bootstrap" alt="Bootstrap 5">
    <img src="https://img.shields.io/badge/Test%20Suite-77%20Passed-brightgreen?style=flat-square&logo=pest" alt="Pest Tests">
    <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License">
  </p>
</p>

---

## 📌 Project Overview

**Smart Healthcare Management System** is a complete, enterprise-grade web application tailored for medical facilities, clinic administrators, doctors, and patients. It unifies hospital administration, department hierarchies, practice shift scheduling, atomic appointment bookings, medical prescriptions with PDF generation, online billing, real-time consultation chat, financial revenue analytics, audit trail logging, and global system settings.

---

## ✨ 17 Core Modules & Features

### 1. 🔑 Multi-Role Authentication
- Role-Based Access Control powered by `spatie/laravel-permission` with **Admin**, **Doctor**, and **Patient** roles.
- Dedicated patient registration entryway (`/register/patient`) preventing unauthorized role elevation.

### 2. 📊 Executive Dashboards
- **Admin Dashboard**: Real-time stats (Total Doctors, Pending Approvals, Total Patients, Total Revenue link), recent appointment monitor, and doctor approval queue.
- **Doctor Dashboard**: Today's scheduled visits, completed consultations count, monthly earnings, and upcoming practice shift metrics.
- **Patient Dashboard**: Active upcoming appointments, recent prescriptions, and doctor search shortcuts.

### 3. 👨‍⚕️ Doctor Management
- Interactive **Yajra DataTables** for reviewing, approving, and assigning departments and specializations to doctor registrations.
- Doctor availability toggle and profile management (qualification, experience, fee, phone, address, bio).

### 4. 👤 Patient Management & Profiles
- Comprehensive patient profile management (Phone, Address, Date of Birth, Gender, Blood Group, Emergency Contacts).
- Active vs completed appointment history views and prescription history.

### 5. 🏥 Department & Specialization Hierarchy
- Full CRUD for medical **Departments** (e.g. Cardiology, Dermatology) and cascading **Specializations** (e.g. Pediatric Cardiology) with dynamic AJAX dropdown population.

### 6. 📅 Doctor Schedule & Slot Engine
- Practice shift configuration: select work date, shift start/end hours, and consultation fee.
- **Automated Slot Generator (`SlotGenerationService`)**: Generates 15, 30, 45, or 60-minute time slots automatically with duplicate protection.

### 7. 🗓️ Atomic Appointment Booking
- Chronologically ordered time slot selection (`04 Aug` → `05 Aug` → `06 Aug`).
- Pessimistic row locking (`lockForUpdate()`) wrapped in database transactions to eliminate double-booking.
- Rescheduling workflow and automatic slot auto-release on cancellation.

### 8. 💳 Online Payment Gateway
- Integrated checkout workflow (`PaymentController`) handling credit card/gateway checkout, payment status tracking, and paid invoice linking.

### 9. 🧾 Invoice Generation & PDF Receipts
- Automatic invoice number generation (`INV-YYYYMMDD-XXXX`) upon appointment payment.
- Invoice receipt view (`invoices.show`) and printable receipt layouts.

### 10. 💊 Prescription Builder & PDF Download
- Comprehensive digital prescription builder:
  - **Symptoms**, **Diagnosis**, **Dynamic Medicine Table** (Name, Dosage, Duration, Instructions), **Advice**, **Flatpickr Follow-up Date** (`d M Y` format), and **Doctor Signature**.
- Instant PDF generation & streaming via `barryvdh/laravel-dompdf` for both Doctors and Patients.

### 11. 📜 Medical & Consultation History
- Patient appointment history grouped by status (`?type=booked` vs `?type=completed`).
- Doctor completed consultations history (`/doctor/completed-consultations`).
- Admin appointment history filterable by status (`pending`, `confirmed`, `completed`).

### 12. 🩸 Health Profile & Clinical Records
- Patient clinical profile tracking blood group, emergency contact details, allergies, and profile editing.

### 13. 💬 Real-Time Chat & Telehealth Communication
- Real-time messenger split interface between Patients and Doctors.
- **`ChatService`**: Image/document attachment processing with `Intervention\Image` scaling (1200px max width).
- Typing indicators ("Doctor is typing..."), read receipts ("Seen" badges), and 3.5s AJAX polling loop for zero UI freezing.

### 14. 🔔 Notifications & System Alerts
- Bootstrap alerts, SweetAlert2 popups, and toast flashes across booking, payments, and prescription creation.

### 15. 📈 Reports & Financial Analytics
- **Admin Revenue & Financial Reports (`/admin/reports/revenue`)**:
  - Stat cards: Total Gross Revenue, Selected Month Revenue, Today's Revenue, Paid Invoices Count.
  - Department revenue breakdown table (doctors count, paid consultations count, monthly & all-time revenue).
  - Detailed paid invoices & payment ledgers with month and department filters.

### 16. 🛡️ System Audit Trail & Activity Logs
- Audit logging system (`activity_logs` table & `ActivityLog::log()` helper) tracking system actions.
- Displays sequential row numbering (`#1`, `#2`, `#3`), User & Role pills, System Module badges, event action pills, detailed descriptions, and relative time diffs.
- Bulk deletion ("Delete Selected Logs") and "Clear All Logs" actions with safety confirmations.

### 17. ⚙️ Global System Settings
- Configurable clinic parameters (`/admin/settings`):
  - **Clinic Identity**: Hospital Name, Tagline, Contact Email, Phone, Emergency Hotline, Address.
  - **Financial Rules**: Currency Symbol (`$`), Tax Percentage (`%`), Default Consultation Fee, Payment Mode (`Sandbox` vs `Live`).
  - **Scheduling Rules**: Cancellation Window (Hours), Default Slot Duration (Minutes), Max Advance Days, Auto-Confirm Paid Appointments.
  - **Feature Toggles**: Email Notifications, SMS Alerts, Patient Chat Access, Legal Prescription Notice text.

---

## 🛠️ Technology Stack

- **Backend Framework:** [Laravel 12](https://laravel.com/) (PHP 8.2+)
- **Role-Based Access Control (RBAC):** [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Frontend & Styling:** Bootstrap 5.3, Bootstrap Icons, Vanilla CSS, Flatpickr Datepicker
- **DataTables Engine:** Yajra DataTables (`yajra/laravel-datatables-oracle`)
- **PDF Generation:** Barryvdh Laravel DomPDF (`barryvdh/laravel-dompdf`)
- **Image Processing:** Intervention Image (`intervention/image-laravel`)
- **Alerts & Dialogs:** SweetAlert2
- **Testing Framework:** [Pest PHP 3](https://pestphp.com/)
- **Database Engine:** MySQL 8.0+

---

## 🔑 Pre-Seeded Default Credentials

Executing the database seeder (`php artisan migrate:fresh --seed`) populates the system with initial roles and ready-to-use accounts:

| User Role | Email Address | Password | Access Rights |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@smarthealthcare.com` | `admin12345` | System Analytics, Doctor Approvals, Revenue Reports, Activity Logs, Settings |
| **Doctor** | `doctor@smarthealthcare.com` | `doctor12345` | Practice Shifts, Slot Generator, Consultations, Prescription Builder, Telehealth Chat |
| **Patient** | Register at `/register/patient` | Custom | Doctor Directory, Atomic Appointment Booking, Payments, Prescriptions, Real-Time Chat |

---

## 🚀 Quick Start & Installation

### Prerequisites
- PHP `>= 8.2`
- Composer `>= 2.x`
- Node.js `>= 18.x` & NPM
- MySQL Database Engine

### Setup Instructions

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/your-username/Smart-Healthcare.git
   cd "Smart Healthcare"
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies:**
   ```bash
   npm install
   ```

4. **Configure Environment File:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database Settings:**
   Open `.env` and set your MySQL database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=smart_healthcare
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Create Storage Symlink:**
   ```bash
   php artisan storage:link
   ```

7. **Run Database Migrations & Seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

8. **Compile Assets & Start Development Server:**
   ```bash
   npm run build
   php artisan serve
   ```

   Visit the application at: **`http://127.0.0.1:8000`**

---

## 🧪 Testing & Verification

The application maintains 100% passing Pest feature test coverage for core business logic, atomic row-locking transactions, prescriptions, chat room workflows, revenue analytics, and activity logs deletion.

Run the test suite using Artisan:
```bash
php artisan test --compact
```
*(All 77 Pest tests passing cleanly)*

---

## 📜 License

This project is open-sourced software licensed under the [MIT license](LICENSE).
