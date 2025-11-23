📚 Small-Scale Library Management System (LMS) - IAP Project
📄 Overview and Project Goals
This project implements a web-based, small-scale Library Management System (LMS) developed as an IAP Project. The central problem addressed is the difficulty in accessing and managing physical library resources in a scalable digital environment.

🎯 Key Objectives (Rubric Alignment)
Enhanced Resource Access (Read Operations): Provide a fast, searchable, and filtered digital catalog accessible to all users (Category 4).

Streamlined Management (CRUD): Offer a secure administrative interface (Librarian role) for accurate inventory management (Category 4, 7).

Robust Data Integrity: Enforce database constraints and relationships (e.g., Book to User via foreign keys) to maintain data quality (Category 3).

Modern UX: Deliver a responsive and intuitive user experience using dynamic frontend tools (Category 6).

💻 Tech Stack & Dependencies
The system is built on the Laravel framework, leveraging a modern dynamic stack for development efficiency.

Core Technologies
The Backend Framework is Laravel (PHP), which provides the Model-View-Controller (MVC) structure, Eloquent ORM, Routing, and foundational business logic.

For Frontend Dynamics, the project utilizes Livewire and Alpine.js.
Livewire is a full-stack framework that handles dynamic server-side rendering and state management, allowing us to build complex interfaces with minimal JavaScript. 
Alpine.js is used alongside Livewire to provide minimal, declarative JavaScript behavior for specific user experience enhancements. 
The styling is managed by Tailwind CSS, a utility-first framework that ensures a rapid and responsive UI development (Category 6).

Infrastructure and Tooling
The data is persisted in a MySQL/MariaDB instance, which hosts the dedicated LMS Database.

For the development pipeline, Node Version Manager (nvm) is used to ensure consistency in the Node.js environment. 
Node.js and npm manage and compile frontend assets (like CSS and JavaScript) into final production bundles. 
The Laravel Breeze scaffolding provides the secure foundation for Authentication & Authorization (Category 7). 
The internal structure of Laravel Routes and Controllers serves as the application's internal API Layer, driving the data flow between the front and back ends.
