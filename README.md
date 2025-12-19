# PHP_Laravel12_Use_Carbon_In_Blade_And_Controller

A complete Laravel 12 demonstration project that explains how to use **Carbon**, the powerful PHP DateTime library, inside **Controllers, Blade views, Models, Accessors, Scopes, and Seeders**.

This repository is designed to help beginners and students clearly understand real‑world date and time handling in Laravel applications.

---

## Project Overview

This project covers practical and commonly used Carbon operations such as:

* Formatting dates and times
* Manipulating dates (past and future)
* Calculating age from birth date
* Timezone conversion
* Human‑readable time differences
* Subscription expiry logic
* Using Carbon directly in Blade templates
* Using Carbon in Model accessors and query scopes

It is suitable for:

* Laravel beginners
* MCA / BCA academic projects
* Interview preparation
* Real‑world Laravel reference

---

## Features

* Current date, time, and day formatting
* Date manipulation (Yesterday, Tomorrow, Next Week, Last Month)
* Timezone handling (New York, London)
* Age calculation from birth date
* Future and past date checking
* Subscription expiry tracking
* Human‑readable time using `diffForHumans()`
* Carbon usage in Blade views
* Model Accessors and Scopes using Carbon
* Seeder with sample Carbon‑powered data

---

## Technologies Used

* Laravel 12
* Carbon (nesbot/carbon)
* MySQL or SQLite
* Bootstrap 5

---

## Project Screenshots

<img width="602" height="261" alt="image" src="https://github.com/user-attachments/assets/5f149c37-a0cd-4421-bd38-b0d1b85cfa57" />
<img width="1634" height="441" alt="image" src="https://github.com/user-attachments/assets/b751b0b3-74f1-4c57-abd9-1b08c27535a1" />
<img width="1722" height="674" alt="image" src="https://github.com/user-attachments/assets/f8a05680-b4d9-414f-b7ee-83b6ca851e1e" />

---

## Installation Guide

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-username/carbon-project.git
cd carbon-project
```

### Step 2: Install Dependencies

```bash
composer install
npm install
```

### Step 3: Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update your database credentials in the `.env` file:

```env
DB_DATABASE=carbon_project
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Run Seeder

```bash
php artisan db:seed --class=UserProfileSeeder
```

### Step 6: Start the Development Server

```bash
php artisan serve
```

Open your browser and visit:

```
http://localhost:8000
```

---

## Usage

Visit the following URLs:

| Page                  | URL                                                                            |
| --------------------- | ------------------------------------------------------------------------------ |
| Carbon Demo Dashboard | [http://localhost:8000/profiles](http://localhost:8000/profiles)               |
| Create Profile        | [http://localhost:8000/profiles/create](http://localhost:8000/profiles/create) |
| Carbon Calculations   | Click the "View Calculations" button                                           |

---

## Project Structure

```
app/
 └── Models/
      └── UserProfile.php

app/
 └── Http/Controllers/
      └── UserProfileController.php

resources/
 └── views/
      └── profiles/
           ├── index.blade.php
           ├── create.blade.php
           └── calculations.blade.php

database/
 └── seeders/
      └── UserProfileSeeder.php
```

---

## Carbon Examples Used

```php
use Carbon\Carbon;

Carbon::now();
Carbon::yesterday();
Carbon::tomorrow();
Carbon::parse('2024-01-01');
Carbon::now()->addDays(30);
Carbon::now('America/New_York');
Carbon::now()->diffForHumans();
```

---

## Final Notes

This repository is intentionally kept simple and readable.

It is ideal for:

* Learning Carbon step by step
* Understanding real‑world date logic
* Academic submissions
* Laravel interview preparation

You are free to extend this project with authentication, subscriptions, or reports.

If you find this project helpful, consider giving it a star on GitHub.
