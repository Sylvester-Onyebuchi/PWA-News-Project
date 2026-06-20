# PWA Vijesti

A PHP and MySQL news portal built for a progressive web applications coursework project. The application lets users publish news articles, browse public categories, register accounts, and manage articles through an administrator panel.

The interface and labels are written in Croatian.

## Features

- Homepage with the latest published news grouped by category
- Category pages for `Sport` and `Kultura`
- Full article pages with images, summaries, and article text
- Authenticated news submission form with image upload
- User registration with hashed passwords
- Administrator login and protected article management
- Article editing, deletion, image replacement, and archive control
- Archived articles are hidden from public listings and article pages
- Basic security practices: prepared SQL statements, output escaping, password hashing, session-based access control, and upload type/size checks

## Requirements

- PHP 8.x, or a recent PHP 7.x version
- MySQL or MariaDB
- Apache, for example through XAMPP
- `mysqli` PHP extension

This project is designed to run from an XAMPP `htdocs` directory.

## Installation

1. Copy or clone the project into your XAMPP web root:

   ```text
   /Applications/XAMPP/xamppfiles/htdocs/pwa_project
   ```

2. Start Apache and MySQL from the XAMPP control panel.

3. Open phpMyAdmin:

   ```text
   http://localhost/phpmyadmin
   ```

4. Import `database.sql`. It creates the `pwa_project` database and the required tables:

   - `vijesti`
   - `korisnik`

5. Make sure the `img/` directory is writable by PHP, because uploaded article images are stored there.

6. Open the application:

   ```text
   http://localhost/pwa_project/index.php
   ```

## Database Configuration

The database connection is configured in `connect.php`:

```php
$servername = 'localhost';
$username = 'root';
$password = '';
$basename = 'pwa_project';
```

These are the default XAMPP MySQL settings. If your local MySQL user, password, host, or database name is different, update this file before running the app.

## Administrator Access

The SQL file creates the database structure only. It does not create a default administrator account.

To create an administrator:

1. Register a new user through:

   ```text
   http://localhost/pwa_project/registracija.php
   ```

2. In phpMyAdmin, set that user's `razina` value to `1` in the `korisnik` table:

   ```sql
   UPDATE korisnik
   SET razina = 1
   WHERE korisnicko_ime = 'your_username';
   ```

3. Sign in through:

   ```text
   http://localhost/pwa_project/administrator.php
   ```

Users with `razina = 0` can register, log in, and post news. Users with `razina = 1` are sent to the administrator panel, where they can view posted news and edit, archive, or delete it.

## Project Structure

```text
pwa_project/
├── administrator.php   # Admin login and article management
├── assets/             # Static project assets
├── clanak.php          # Public single-article page
├── connect.php         # MySQL connection settings
├── database.sql        # Database and table schema
├── footer.php          # Shared footer template
├── functions.php       # Helpers, category data, uploads, and queries
├── header.php          # Shared header and navigation
├── img/                # Uploaded article images
├── index.php           # Homepage
├── kategorija.php      # Public category listing
├── logout.php          # Session logout
├── registracija.php    # User registration
├── skripta.php         # News submission processing
├── style.css           # Main stylesheet
└── unos.php            # News submission form
```

## Main Workflows

### Add a News Article

1. Log in, then open `unos.php`.
2. Enter the title, summary, full text, category, and optional image.
3. Submit the form.
4. The article is inserted into `vijesti` and displayed publicly.

Administrators manage posted news from `administrator.php`.

### Manage Articles as an Admin

1. Log in through `administrator.php`.
2. Edit article title, summary, text, category, image, or archive status.
3. Save changes or delete articles from the same screen.

## Security Notes

The project includes several baseline protections:

- SQL queries use prepared statements where user input is involved.
- Passwords are stored with `password_hash()` and checked with `password_verify()`.
- Public output is escaped with `htmlspecialchars()`.
- Posting requires a logged-in user session.
- Admin actions are checked with session data and the `razina` role field.
- Uploaded files are limited to JPEG, PNG, GIF, and WebP images.
- Uploaded files are limited to 3 MB.
- Admin sessions expire after 30 minutes of inactivity.

For production use, add HTTPS, CSRF protection, stricter validation, non-root database credentials, server-side upload directory hardening, and environment-based configuration.

## Troubleshooting

### Database Connection Error

Check that MySQL is running and that the values in `connect.php` match your local database settings.

### Page Opens but No News Appears

Import `database.sql`, then add articles through `unos.php`. New articles are hidden from public pages when the archive checkbox is selected.

### Uploaded Images Do Not Appear

Check that the `img/` directory exists and is writable by the web server.

### Admin Login Works but Access Is Denied

Make sure the logged-in user has `razina = 1` in the `korisnik` table.
