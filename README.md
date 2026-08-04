<p align="center">
  <h1 align="center">🏥 Smart Healthcare Management System</h1>
  <p align="center">
    <strong>A Modern, Multi-Role Hospital & Appointment Booking Platform built with Laravel 12</strong>
  </p>
  <p align="center">
    <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/Laravel-v12.x-FF2D20?style=flat-square&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/Bootstrap-v5.3-7952B3?style=flat-square&logo=bootstrap" alt="Bootstrap 5">
    <img src="https://img.shields.io/badge/Test%20Suite-46%20Passed-brightgreen?style=flat-square&logo=pest" alt="Pest Tests">
    <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License">
  </p>
</p>

---

## 📌 Project Overview

**Smart Healthcare Management System** is a full-featured, secure, multi-role web application designed for healthcare facilities, clinic administrators, doctors, and patients. It streamlines hospital management, department hierarchy, doctor shift schedules, and real-time appointment booking with race-condition prevention.

---

## ✨ Key Features & Portals

### 🛡️ Administrator Control Center
- **Hospital Hierarchy Management:** Full CRUD operations for hospital **Departments** (e.g. Cardiology, Neurology) and cascading **Specializations** (e.g. Pediatric Cardiology).
- **Doctor Directory & Verification:** Interactive **Yajra DataTables** for reviewing, approving, and assigning departments/specializations to newly registered doctors.
- **Doctor Schedule Monitoring:** View doctor availability and shift schedules.
- **Real-Time Analytics:** Dashboard overview tracking total doctors, pending verification approvals, total patients, appointment logs, and revenue metrics.

### 🩺 Doctor Portal
- **Shift & Availability Engine:** Select work dates, start/end shift hours, and consultation fee.
- **Automated Slot Generator:** Generate custom 15, 30, 45, or 60-minute time slots automatically with duplicate prevention.
- **Practice Dashboard:** Track daily consultation appointments, completed visits, and monthly earnings.
- **Approval Protection Banner:** Pending doctor registrations display a verification status alert until reviewed by an Administrator.

### 👤 Patient Portal
- **Separate Patient Portal Registration:** Dedicated patient registration entry (`/register/patient`) preventing unauthorized role elevation.
- **AJAX Doctor Search:** Search doctors by name, medical department, or specialization with live slot availability filtering.
- **Atomic Booking Engine:** Reserve doctor consultation slots wrapped in database transactions using pessimistic row locking (`lockForUpdate()`) to eliminate double-booking.
- **My Booked Appointments:** View scheduled appointments, update visit notes, or reschedule to a new time slot.
- **Slot Auto-Release on Cancellation:** Cancelling an appointment automatically releases the time slot back to `available` status on the doctor's schedule.

---

## 🛠️ Technology Stack

- **Backend Framework:** [Laravel 12](https://laravel.com/) (PHP 8.2+)
- **Role-Based Access Control (RBAC):** [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Frontend & Styling:** Bootstrap 5.3, Bootstrap Icons, Vanilla CSS
- **DataTables Engine:** Yajra DataTables (`yajra/laravel-datatables-oracle`)
- **Alerts & Dialogs:** SweetAlert2
- **Testing Framework:** [Pest PHP 3](https://pestphp.com/)
- **Database:** MySQL 8.0+

---

## 🔑 Pre-Seeded Default Credentials

Executing the database seeder (`php artisan migrate:fresh --seed`) populates the system with initial roles and ready-to-use accounts:

| User Role | Email Address | Password | Access Rights |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@smarthealthcare.com` | `admin12345` | Full System Management & Doctor Approvals |
| **Doctor** | `doctor@smarthealthcare.com` | `doctor12345` | Shifts, Slot Generation, Practice Dashboard |
| **Patient** | Register at `/register/patient` | Custom | Doctor Search, Appointment Booking & Management |

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

6. **Run Database Migrations & Seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compile Assets & Start Local Server:**
   ```bash
   npm run build
   php artisan serve
   ```

   Visit the application at: **`http://127.0.0.1:8000`**

---

## 🧪 Testing & Verification

The application maintains comprehensive Pest feature test coverage for core business logic, role security, booking transactions, and slot reversion.

Run the test suite using Artisan:
```bash
php artisan test --compact
```

---

