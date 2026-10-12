# Quran & Science — cPanel Deployment & Admin Setup Guide

This guide details how to deploy the **Teacher / Team Management Admin Panel** on a standard **cPanel hosting account** with PHP and MySQL/MariaDB.

---

## Prerequisites
- cPanel hosting account with PHP 7.4, 8.0, 8.1, 8.2, or 8.3
- MySQL or MariaDB database support
- phpMyAdmin access in cPanel

---

## Step 1: Create a MySQL Database in cPanel
1. Log in to your **cPanel dashboard**.
2. Under the **Databases** section, click **MySQL® Databases**.
3. Under **Create New Database**, enter a database name (for example, `quransci_db`).
4. Click **Create Database**.

---

## Step 2: Create a Database User
1. Scroll down to **MySQL Users — Add New User**.
2. Enter a username (for example, `quransci_user`).
3. Generate or enter a strong password. (Save this password securely).
4. Click **Create User**.

---

## Step 3: Assign the User to the Database
1. Scroll down to **Add User To Database**.
2. Select the user created in Step 2 and the database created in Step 1.
3. Click **Add**.
4. Check **ALL PRIVILEGES**.
5. Click **Make Changes**.

---

## Step 4: Import the Database Schema
1. Return to the cPanel main page and click **phpMyAdmin**.
2. Select your newly created database in the left sidebar.
3. Click the **Import** tab at the top.
4. Click **Choose File** and select `database/setup.sql` from your project folder.
5. Click **Import** (or **Go**) at the bottom.
6. The `admins` and `teachers` tables will be created cleanly. **No fake or placeholder teacher profiles are seeded.** The public homepage will display the neutral notice (`Tutor profiles will be available soon.`) until real instructor profiles are added.

---

## Step 5: Configure Production Database Credentials
1. In cPanel **File Manager** (or via FTP), open your website's root folder (`public_html` or domain root).
2. Locate `config.example.php`.
3. Copy or rename it to `config.php`:
   ```bash
   cp config.example.php config.php
   ```
4. Edit `config.php` and fill in your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'yourcpanel_quransci_db');
   define('DB_USER', 'yourcpanel_quransci_user');
   define('DB_PASSWORD', 'YOUR_STRONG_DATABASE_PASSWORD');
   define('DB_PORT', '3306');
   ```
5. Save the file.

> **Security Note:** `config.php` is listed in `.gitignore` so your database credentials will never be accidentally committed to Git.

---

## Step 6: Verify Image Upload Directory Permissions
Ensure the upload directory is writable by the web server:
- Path: `uploads/teachers/`
- Permissions: `0755` (standard cPanel writable directory)
- Note: A security `.htaccess` file is pre-configured inside `uploads/teachers/` to prevent the execution of PHP scripts in the upload folder.

---

## Step 7: Create the Initial Admin User Securely
1. In your browser, navigate to:
   ```
   https://YOUR-DOMAIN.com/admin/
   ```
2. On your first visit, the setup wizard will detect that no admin account exists yet and direct you to:
   ```
   https://YOUR-DOMAIN.com/admin/install.php
   ```
3. Enter your preferred:
   - **Admin Username**
   - **Admin Email**
   - **Admin Password** (at least 8 characters)
4. Click **Create Account & Complete Setup**.
5. Once created, the installer is permanently locked to prevent unauthorized changes.

---

## Step 8: Log In and Manage Teachers
1. Open:
   ```
   https://YOUR-DOMAIN.com/admin/login.php
   ```
2. Log in with your new admin username and password.
3. You will arrive at the **Admin Dashboard**:
   - **Dashboard**: View active vs inactive profile counts.
   - **Teachers / Team**: View all instructor profiles.
   - **Add Teacher**: Upload instructor photo, enter name, role, specialties, and display order.
   - **Edit / Delete / Visibility**: Toggle profiles active/inactive with one click or edit details.

---

## Step 9: Automatic Homepage Synchronization
- When an instructor profile is created or updated in the Admin Panel:
  - Active profiles (`status = active`) are automatically displayed on the public homepage in their specified `display_order`.
  - Inactive profiles (`status = inactive`) are immediately hidden from the public homepage without deleting their data.
  - If no active profiles exist in the database, the clean neutral message (`Tutor profiles will be available soon.`) is shown automatically.
  - No manual HTML editing is ever needed.

---

## Post-Deployment cPanel QA Checklist

Because local execution environments without PHP cannot execute PHP runtimes, **PHP runtime QA must be completed after deployment on cPanel.** Use this verification checklist:

| Test Scenario | Action to Perform | Expected Result |
| :--- | :--- | :--- |
| **1. Unauthenticated Redirect** | Visit `/admin/` or `/admin/teachers.php` while logged out | Automatically redirected to `/admin/login.php` |
| **2. `/admin/install.php` Setup** | Run initial setup on a fresh database | Creates first admin account with bcrypt password hash |
| **3. `/admin/install.php` Lock** | Attempt visiting `/admin/install.php` after admin exists | Immediately redirected to `login.php` (or blocked with 403 Forbidden) |
| **4. Login Authentication** | Sign in with valid credentials | Redirects to `/admin/index.php` dashboard with active session |
| **5. Invalid Login Rejection** | Attempt login with wrong password | Displays error `"Invalid username or password."` without entering |
| **6. Logout** | Click "Sign Out" in admin navigation | Clears session cookie and redirects to `login.php` |
| **7. Add Teacher** | Create a teacher profile via `/admin/teacher-add.php` | Successfully inserts profile with status `active` |
| **8. Image Upload** | Upload JPG, PNG, or WebP photo (< 3MB) | File saved in `uploads/teachers/` and thumbnail visible |
| **9. Invalid Upload Rejection** | Upload a `.txt`, `.php`, or file > 3MB | Rejects upload with clear error message; no file saved |
| **10. Edit Teacher** | Edit name, role, or upload replacement photo | Database record updated; old photo replaced safely |
| **11. Status Toggle** | Click "Set Inactive" button on a profile | Status toggles to `inactive`; profile hidden from public homepage |
| **12. Delete Teacher** | Click "Delete" button with confirmation | Profile deleted and image unlinked from disk |
| **13. CSRF Protection** | Submit a POST request without valid `csrf_token` | Request rejected with `"Invalid security token."` |
| **14. Public Empty State** | When 0 teachers exist in database | Public homepage displays `"Tutor profiles will be available soon."` |
| **15. Public Dynamic Sync** | When 1+ active teachers exist | Public homepage dynamically displays teacher cards in order |

---

## Security Best Practices
1. **Never commit `config.php`** or database passwords to public repositories.
2. Keep your hosting PHP version up to date (PHP 8.1+ recommended).
3. Ensure HTTPS is active across your cPanel domain (via cPanel AutoSSL or Let's Encrypt).
4. Admin passwords are encrypted using PHP's standard `password_hash()` algorithm.

