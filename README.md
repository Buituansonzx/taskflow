# TaskFlow

## 📝 Overview
TaskFlow is a robust, highly scalable project and task management API platform designed to streamline team collaboration. It empowers teams to efficiently organize workspaces, manage cross-functional projects, assign tasks, and track real-time activities while ensuring secure, role-based access control.

## 🚀 Core Features
*   **Authentication & Security**: Secure OAuth2 authentication via Laravel Passport, enhanced with ID hashing (HashIDs) to mask database primary keys.
*   **Role-Based Access Control (RBAC)**: Fine-grained authorization using Spatie Laravel-Permission to manage user roles and permissions across workspaces.
*   **Workspace Management**: Create isolated workspaces, manage member invitations, and control team access dynamically.
*   **Project & Task Management**: Full CRUD operations for projects, tasks, tags, and assignees with detailed status tracking.
*   **Real-time Activity & Comments**: Track granular task activities and facilitate team communication via task comments.
*   **Asynchronous Notifications**: Event-driven background jobs (Queues) for sending welcome emails, email verification, password updates, and workspace invitations.

## 🛠️ Technical Highlights
This project is engineered for enterprise-level scalability and maintainability, adhering to strict coding standards and modern architectural patterns:
*   **Porto Software Architecture Pattern (SAP)**: The codebase is structured using the Apiato framework, enforcing a highly modular, domain-driven design via `app/Containers` (e.g., `AppSection`, `ClientSection`).
*   **Action-Task Pattern**: Business logic is decoupled into single-responsibility Tasks and Actions, ensuring maximum reusability and testability.
*   **Form Requests**: Strict incoming data validation and sanitization using dedicated Laravel Form Request classes.
*   **API Resources (Transformers)**: Clean, consistent, and versioned JSON responses using fractal transformers and API resources.
*   **Event-Driven Architecture**: Extensive use of Laravel Events and Listeners (`ShouldQueue`) pushed to Redis/Database queues for non-blocking execution (e.g., `InviteToWorkspaceEventListener`, `RemoveMemberEventListener`).
*   **Performance Optimization**: Utilizes Eloquent Eager Loading (`with()`) to prevent N+1 query problems and implements database indexing for faster query execution on large datasets.

## 📦 Tech Stack
*   **Language**: PHP 8.2+
*   **Framework**: Laravel 11.x (Apiato 13.1 API Starter Kit)
*   **Database**: PostgreSQL / MySQL (Configured via Eloquent ORM)
*   **Cache & Queues**: Redis / Database / Memcached
*   **Authentication**: Laravel Passport (OAuth2)
*   **Tooling**: Docker, Nginx, Mailpit (for local email testing)

## 💻 Local Installation & Setup
Follow these steps to get the project up and running on your local machine using Docker:

1. **Clone the repository and navigate into the project:**
   ```bash
   git clone <repository-url>
   cd taskflow
   ```

2. **Configure Environment Variables:**
   ```bash
   cp .env.example .env
   # Update DB and Redis credentials in .env if not using the default Docker setup
   ```

3. **Start Docker Containers:**
   ```bash
   docker compose up -d --build
   ```

4. **Install Dependencies and Setup Application:**
   ```bash
   docker compose exec php sh -c "composer install && php artisan key:generate"
   ```

5. **Run Migrations & Seeders:**
   ```bash
   docker compose exec php php artisan migrate --seed
   ```
