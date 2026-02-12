# 🎬 Cinenow Admin Panel

------------------------------------------------------------------------

## 📌 Overview

Cinenow Admin Panel is a scalable backend system built with Laravel.\
It manages movies, collections, banners, users, and dashboard analytics
for streaming platforms.

------------------------------------------------------------------------

## 🚀 Features

-   Secure Admin Authentication
-   Movie Management (Add/Edit/Delete)
-   Category & Collection Management
-   Banner & Slider System
-   User Monitoring
-   Dashboard Analytics
-   Data Export Module
-   Storage & Media Handling

------------------------------------------------------------------------

## 🏗 Tech Stack

-   Laravel 9/10
-   PHP 8.1+
-   MySQL / MariaDB
-   Blade Templates
-   Bootstrap
-   Apache / Nginx

------------------------------------------------------------------------

## ⚙ Installation

### 1️⃣ Clone Repository

``` bash
git clone https://github.com/deepakcloudsoffical/cinenow-website.git
cd cinenow-website
```

### 2️⃣ Install Dependencies

``` bash
composer install
```

If frontend assets exist:

``` bash
npm install
npm run build
```

### 3️⃣ Configure Environment

``` bash
cp .env.example .env
```

Update database credentials inside `.env`.

### 4️⃣ Generate Key

``` bash
php artisan key:generate
```

### 5️⃣ Run Migration

``` bash
php artisan migrate
```

### 6️⃣ Storage Link

``` bash
php artisan storage:link
```

### 7️⃣ Start Server

``` bash
php artisan serve
```

------------------------------------------------------------------------

## 🔒 Security

-   CSRF Protection
-   Middleware-based route protection
-   Password hashing (bcrypt)
-   Input validation
-   Environment configuration isolation

------------------------------------------------------------------------

## ⚡ Production Optimization

``` bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set:

    APP_ENV=production
    APP_DEBUG=false

------------------------------------------------------------------------

## 📁 Project Structure

    app/
    database/
    routes/
    resources/
    public/
    storage/

------------------------------------------------------------------------

## 🔄 Git Workflow

``` bash
git add .
git commit -m "Update feature"
git push origin main
```

------------------------------------------------------------------------

## 📈 Roadmap

-   Subscription system
-   Payment gateway integration
-   Advanced analytics
-   Multi-role admin support
-   REST API expansion

------------------------------------------------------------------------

## 👨‍💻 Author

Deepak\
GitHub: https://github.com/deepakcloudsoffical

------------------------------------------------------------------------

## 📄 License

Proprietary Software. All rights reserved.
