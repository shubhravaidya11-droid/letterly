# Letterly

Letterly is a PHP 8+ and MySQL letter-writing app. Accounts use PHP sessions and password hashing; letters, settings, photo paths, and sticker positions are stored in MySQL. Uploaded photos are stored as files under `uploads/photos/`.

## Run with XAMPP

1. Install and open XAMPP, then start **Apache** and **MySQL**.
2. Copy the `letterly` folder into `C:\xampp\htdocs\` (or your XAMPP `htdocs` directory).
3. Open `http://localhost/phpmyadmin`, choose **Import**, select `letterly/database/letterly.sql`, and run the import. The script creates the `letterly_db` database and tables.
4. Check `config/database.php`. The defaults are host `127.0.0.1`, database `letterly_db`, user `root`, and an empty password, which match a typical local XAMPP install. Change these for your MySQL setup.
5. Make sure Apache can write to `uploads/photos/`. The upload limit in the app is 8 MB; PHP's `upload_max_filesize` and `post_max_size` must be at least that large.
6. Visit `http://localhost/letterly/`.

Alternatively, with PHP and MySQL installed, import the SQL file using the MySQL client and run `php -S 127.0.0.1:8000 -t letterly` from the parent folder. The PHP built-in server is for local development only.

## Included flows

- Register and sign in with email or username; optional 30-day remember-me token is stored hashed in MySQL.
- Create, edit, preview, print to PDF, and delete letters.
- Customize templates, paper/ink colors, font, size, alignment, uploaded photos, and draggable/resizable stickers.
- See only the signed-in user's letters in the dashboard and library.
- Change account details and password in account settings.

The **Download** action opens the browser print dialog. Choose **Save as PDF** as the destination to save a portable copy with the page styling. For production, serve over HTTPS, set a non-default database account/password, and configure backups and PHP upload limits for the deployment.

## Project map

```text
letterly/
├── account.php
├── create-letter.php
├── dashboard.php
├── edit-letter.php
├── index.php
├── library.php
├── login.php
├── logout.php
├── register.php
├── view-letter.php
├── config/database.php
├── database/letterly.sql
├── includes/ (auth, shared functions, header, footer)
├── css/ (site, auth, editor)
├── js/ (navigation, live editor)
└── uploads/photos/
```