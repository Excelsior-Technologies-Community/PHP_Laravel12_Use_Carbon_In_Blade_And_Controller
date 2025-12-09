# PHP_Laravel12_Use_Carbon_In_Blade_And_Controller

A complete step-by-step Laravel 12 project demonstrating how to use **Carbon (PHP DateTime Library)** in different parts of a Laravel application.

---

##  Features

 Current date, time, and day formatting  
 Date manipulation (Yesterday, Tomorrow, Next Week, Last Month)  
 Timezone handling (New York, London)  
 Age calculation from birth date  
 Future/Past date checking  
 Subscription expiry tracking  
 Human-readable time (`diffForHumans`)  
 Blade-level Carbon usage  
 Model Accessors & Scopes using Carbon  
 Seeder with sample Carbon-powered data

---

## Technologies Used

- **Laravel 12**
- **Carbon (nesbot/carbon)**
- **MySQL / SQLite**
- **Bootstrap 5**

---
## Screenshots

<img width="602" height="261" alt="image" src="https://github.com/user-attachments/assets/5f149c37-a0cd-4421-bd38-b0d1b85cfa57" />
<img width="1634" height="441" alt="image" src="https://github.com/user-attachments/assets/b751b0b3-74f1-4c57-abd9-1b08c27535a1" />
<img width="1722" height="674" alt="image" src="https://github.com/user-attachments/assets/f8a05680-b4d9-414f-b7ee-83b6ca851e1e" />

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/carbon-project.git
cd carbon-project

2. Install Dependencies
composer install
npm install

3. Environment Setup
cp .env.example .env
php artisan key:generate


Update .env:

DB_DATABASE=carbon_project
DB_USERNAME=root
DB_PASSWORD=

4. Run Migrations
php artisan migrate

5. Run Seeder
php artisan db:seed --class=UserProfileSeeder

6. Start the Server
php artisan serve

# Usage

Visit these URLs in your browser:

Page	URL
Carbon Demo Dashboard	http://localhost:8000/profiles
Create Profile	http://localhost:8000/profiles/create
Carbon Calculation Page	Click View Calculations button

Project Structure
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

Carbon Examples Used
use Carbon\Carbon;

Carbon::now();
Carbon::yesterday();
Carbon::tomorrow();
Carbon::parse('2024-01-01');
Carbon::now()->addDays(30);
Carbon::now('America/New_York');
Carbon::now()->diffForHumans();
