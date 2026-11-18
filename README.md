# URL Shortener

This is a URL Shortener project made with Laravel 12.

In this project, different companies can use the same application. Each
company has its own Admin and team members. Admins and Members can
create short links and share them with anyone.

## Technologies Used

-   Laravel 12
-   PHP 8.2 or above
-   MySQL
-   Laravel Breeze for login
-   Blade and Tailwind CSS
-   Vite

## User Roles

There are three main roles in this project.

### 1. SuperAdmin

-   Can invite an Admin.
-   Every invitation creates a new company.
-   Can see all short URLs from all companies.
-   Cannot create short URLs.

### 2. Admin

-   Can invite another Admin or a Member to their own company.
-   Can create short URLs.
-   Can see all short URLs created in their company.

### 3. Member

-   Can create short URLs.
-   Can see only the URLs created by themselves.
-   Cannot invite other users.



Anyone can open a short URL without logging in. There is no public
signup page. New users can join only through an invitation link.

## Requirements

Before running the project, make sure these are installed:

-   PHP 8.2 or above
-   Composer 2
-   Node.js 18 or above
-   npm
-   MySQL 8 or MariaDB 10.4+
-   Git

## Project Setup

Follow these steps to run the project on your computer.

### Step 1: Clone the project

Open your terminal and run:

``` bash
git clone https://github.com/thakur-thakur123/url_shortener_interview.git

Now go inside the project folder:

``` bash
cd url_shortener_interview
```

### Step 2: Install packages

Run these commands one by one:

``` bash
composer install
```

``` bash
npm install
```

### Step 3: Create the .env file

First, create the environment file:

``` bash
cp .env.example .env
```

Then generate the Laravel application key:

``` bash
php artisan key:generate
```

### Step 4: Create the database

Open MySQL or phpMyAdmin and create a new database with this name:

`url_shortener_interview`

You can also create it using the terminal:

``` bash
mysql -u root -p -e "CREATE DATABASE url_shortener_interview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 5: Add database details

Open the `.env` file and update the database details according to your
MySQL setup.

``` env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener_interview
DB_USERNAME=root
DB_PASSWORD=
```

If your MySQL username or password is different, update those values.

### Step 6: Run migrations and seeders

Now run:

``` bash
php artisan migrate --seed
```

This command creates the required database tables and adds the default
SuperAdmin account.

### Step 7: Build frontend files

Run:

``` bash
npm run build
```

### Step 8: Start the Laravel server

Run:

``` bash
php artisan serve
```

Now open this URL in your browser:

`http://127.0.0.1:8000`

The project should now be running.

For frontend development, you can use `npm run dev` in another terminal
instead of `npm run build`.

## Default SuperAdmin Login

After running `php artisan migrate --seed`, the seeder creates the SuperAdmin account with these default details:

  Detail     Value
  ---------- --------------------------
  Email      `superadmin@gmail.com`
  Password   `Test@123`

These are the default values used in `DatabaseSeeder.php`. If you want to
change them, add your own values in the `.env` file before running the seeder:

``` env
SUPERADMIN_NAME=Super Admin
SUPERADMIN_EMAIL=superadmin@gmail.com
SUPERADMIN_PASSWORD=Test@123
```

## How to Test the Project

Follow these steps to understand how the project works.

### Step 1: Login as SuperAdmin

Open the application and log in using the default SuperAdmin details.

### Step 2: Create a company and invite an Admin

Open the **Invite Admin** page.

Enter: - Company name - Admin email

Submit the form. The application will show an invitation link.

Copy this link. You do not need to configure an email server for this
assignment.

### Step 3: Accept the invitation

Open the invitation link in another browser or an incognito window.

Enter your name and password. After accepting the invitation, you will
be logged in as the Admin of that company.

### Step 4: Invite team members

As an Admin, open **Invite User**.

You can invite another Admin or a Member to your company.

You can also open **Create Short URL** and create a short link.

The Admin can see all short URLs created by users in their company.

### Step 5: Login as Member

Open the Member's invitation link in another private window and set up
the account.

Now login as the Member and create a short URL.

The Member will see only the short URLs created by their own account.

### Step 6: Test the short link

Copy any generated short URL and open it in a browser where you are not
logged in.

It should redirect you to the original website.

For example:

`http://127.0.0.1:8000/s/AbCd1234`

### Step 7: Check SuperAdmin access

Login again as SuperAdmin.

Open **All Short URLs**. Here you can see short URLs from every company.

SuperAdmin can only view these URLs and cannot create new ones.

## Running Tests

To run the project's tests, use:

``` bash
php artisan test
```

The tests use an in-memory SQLite database. They do not change your
MySQL database.

The tests check:

-   Admin and Member can create short URLs.
-   SuperAdmin cannot create short URLs.
-   SuperAdmin can see URLs from all companies.
-   Admin can see URLs from their own company only.
-   Member can see only their own URLs.
-   Short links redirect to the original URL.
-   Invalid short links show a 404 error.
-   The link visit counter increases.
-   Invitation permissions work according to user roles.
-   An invitation cannot be used again after it has been accepted.
-   Login and logout work correctly.
-   Public registration is not available.

## Important Project Files

Here are some files you may need when checking the code:

  -------------------------------------------------------------------------------------
  File or Folder                                    What it does
  ------------------------------------------------- -----------------------------------
  `app/Enums/Role.php`                              Contains user roles

  `app/Models/`                                     Contains User, Company, Invitation
                                                    and ShortUrl models

  `app/Http/Controllers/DashboardController.php`    Handles the dashboard

  `app/Http/Controllers/InvitationController.php`   Handles invitations

  `app/Http/Controllers/ShortUrlController.php`     Handles short URL creation and
                                                    listing

  `app/Http/Controllers/RedirectController.php`     Redirects short links

  `app/Http/Controllers/Auth/`                      Handles login and other
                                                    authentication features

  `database/migrations/`                            Contains database table migrations

  `database/seeders/DatabaseSeeder.php`             Creates the default SuperAdmin

  `routes/web.php`                                  Contains application routes

  `routes/auth.php`                                 Contains authentication routes

  `resources/views/`                                Contains Blade pages

  `tests/Feature/`                                  Contains feature tests
  -------------------------------------------------------------------------------------

## Common Problems

If you face any issue while running the project, check these solutions:

  -----------------------------------------------------------------------
  Problem                             What to do
  ----------------------------------- -----------------------------------
  `could not find driver`             Enable the `pdo_mysql` extension.
                                      For tests, also enable
                                      `pdo_sqlite`.

  `Unknown database`                  Make sure the `url_shortener_interview`
                                      database has been created.

  `Access denied for user`            Check your database username and
                                      password in `.env`.

  `Vite manifest not found`           Run `npm run build`.

  `No application encryption key`     Run `php artisan key:generate`.

  Changes in `.env` are not working   Run `php artisan config:clear`.
  -----------------------------------------------------------------------

If you want to reset the database and run all migrations and seeders
again, use:

``` bash
php artisan migrate:fresh --seed
```

**Note:** This command deletes existing tables and their data before
creating them again. Use it only when you are okay with resetting the
database.

## AI Usage

I used Claude (Anthropic) as a helper while working on this project. It helped me plan the work, understand the requirements, prepare some initial code drafts, and organize the setup steps.

I used Laravel Breeze for the login, logout, and password reset features.

My work on the project included:
- Setting up the Laravel project and database.
- Reviewing the code and understanding how the main features work.
- Checking the invitation process for SuperAdmin, Admin, and Member.
- Checking short URL creation, URL listing, and public redirects.
- Running the application and tests, and checking issues found during setup.

AI was used as a support tool. I reviewed the code and tested the application as part of the development process.

