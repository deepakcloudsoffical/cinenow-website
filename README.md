🎬 Cinenow Admin Panel
Production-Ready Laravel Content Management System

Cinenow Admin Panel is a scalable, secure, and modular backend system built using Laravel.
It is designed to power streaming platforms by managing movies, collections, banners, users, and platform analytics.

This system is optimized for performance, maintainability, and structured content workflows.

📖 Table of Contents

Project Overview

System Architecture

Features

Technical Specifications

Installation (Local)

Production Deployment

Environment Configuration

Database Structure

API Architecture

Security Implementation

Performance Optimization

Storage & Media Handling

Version Control Strategy

Backup Strategy

Future Roadmap

Author & License

📌 Project Overview

Cinenow provides a centralized administrative backend for:

🎥 Managing streaming content

📂 Structuring collections & genres

🖼 Homepage banner control

👥 User monitoring

📊 Dashboard analytics

📤 Data exports

The system is built following Laravel best practices and modular architecture.

🏗 System Architecture
Backend

Laravel 9/10

PHP 8.1+

MVC Pattern

Service Layer Structure

Event-driven components

Database

MySQL / MariaDB

Migration-based schema control

Seeder-based initial data

Frontend

Blade Templates

Bootstrap Admin UI

jQuery (if used)

Server

Apache / Nginx

VPS / Dedicated Server / cPanel Hosting

🚀 Core Features
🔐 Authentication System

Secure admin login

Password hashing (bcrypt)

Session management

CSRF protection

Middleware-based access control

🎥 Movie Management

Add/Edit/Delete movies

Assign categories & genres

Featured content toggle

Collection linking

Thumbnail & banner upload

📂 Category & Collection System

Dynamic collection creation

Content grouping

Sorting & ordering

Status-based visibility control

🖼 Banner & Slider System

Homepage slider management

Position-based banner placement

Scheduled promotion capability

👥 User Management

View user records

Monitor activity

Account status management

📊 Dashboard Analytics

Total movies

Total users

Content distribution overview

System summary

📤 Export Module

Banner export

Data export for reporting

Structured output formatting

🛠 Installation Guide (Development)
Step 1 – Clone Repository
git clone https://github.com/deepakcloudsoffical/cinenow-website.git
cd cinenow-website

Step 2 – Install Dependencies
composer install


If frontend assets exist:

npm install
npm run build

Step 3 – Configure Environment
cp .env.example .env


Update database credentials and app settings.

Step 4 – Generate App Key
php artisan key:generate

Step 5 – Database Setup
php artisan migrate


Optional:

php artisan db:seed

Step 6 – Storage Linking
php artisan storage:link

Step 7 – Run Server
php artisan serve

🌐 Production Deployment (Ubuntu VPS Example)
Install Requirements
sudo apt update
sudo apt install php php-fpm php-mysql php-cli php-mbstring php-xml php-curl unzip nginx mysql-server composer

Set Permissions
sudo chown -R www-data:www-data /var/www/cinenow
sudo chmod -R 755 /var/www/cinenow

Optimize Application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize


Set production mode:

APP_ENV=production
APP_DEBUG=false

⚙ Environment Configuration

Important .env variables:

APP_NAME=Cinenow
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinenow
DB_USERNAME=youruser
DB_PASSWORD=yourpassword

🗄 Database Structure Overview

Core Tables May Include:

users

movies

categories

collections

banners

exports

password_resets

Migration-based schema ensures version control consistency.

📡 API Architecture (If Enabled)

Web Routes:

routes/web.php


API Routes:

routes/api.php


Recommended API authentication:

Laravel Sanctum

🔒 Security Implementation

CSRF protection enabled

Input validation via Form Requests

Middleware-based route protection

Environment variable isolation

Hashed passwords

Secure file upload validation

⚡ Performance Optimization

Production optimization commands:

php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache


Optional:

Redis caching

Queue workers

CDN integration

Image compression

📁 Media & Storage Handling

Uploaded files stored in:

storage/app/public


Public access via:

public/storage


Linked using:

php artisan storage:link

🔄 Git Workflow Strategy

Recommended production workflow:

git checkout -b feature/update-module
git commit -m "Update module"
git push origin feature/update-module
