 =
Small-Scale Library Management System (LMS) - IAP Project

Library Management System Overview
This application is a dynamic, full-stack web system designed to manage all essential library activities. It is built on the modern TALL Stack (Tailwind CSS for look, Livewire/Alpine.js for interactive features, and Laravel for the powerful PHP backend). It also includes a robust RESTful API layer for integrating with external tools or mobile apps.

Core Functionality
The system is organized around three main areas:

Book Inventory: This is the public catalog, where any authenticated member can search and view details about all books in the library. Only administrators can add, edit, or delete book records.

Loan Desk: This module tracks every book issued and returned. It is primarily used by administrators to issue books to members and process returns, calculating simple fines for overdue items. Members can only view their personal loan history here.

Member Registry: This handles user management. Administrators use this to register new members, assign roles (Admin or Member), and manage user access details.

Security and Roles (RBAC)
The system uses Role-Based Access Control to ensure secure operations:

Administrators: Have full control over the entire system. They can manage inventory, issue/return books, and manage the member list.

Members: Have limited access. They can look up books and see their own outstanding and returned loans, but they cannot access any administrative forms or modify any data.

Guests: Must log in to access any part of the application.

IAP Project
Installed laravel
Installed live wire
Installed node version manager (nvm)
Installed nodejs(builds final css and js assets)
Installed npm (local project dependencies)
Installed api
Created lms database
Installed alpine js

STEPS
In the terminal:
cd library-lms
composer install
cp .env.example .env
php artisan:key generate

In your .env file:
remove comments in the db part and input appropriate details

In terminal:
php artisan migrate
npm install
php artisan tinker

Paste this in tinker:

\App\Models\User::create([
    'name' => 'System Admin', 
    'email' => 'admin@lms.com', 
    'password' => \Illuminate\Support\Facades\Hash::make('password'),
    'role' => \App\Models\User::ROLE_ADMIN // Set the admin role
]);

Then exit tinker
php artisan serve

Credentials are:
admin@lms.com
password
(Password is literally password)




