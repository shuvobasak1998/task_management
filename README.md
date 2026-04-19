# TaskFlow Studio

TaskFlow Studio is a polished Laravel-based task management system for small teams. It focuses on secure access, clear task ownership, fast progress updates, live countdown timing, and a dashboard that makes work status easy to understand at a glance.

This project was built with a recruiter-facing mindset:
- clean architecture
- clear commit history
- focused feature scope
- practical security
- strong behavior-driven tests

## Features

- Authentication with register, login, logout, and protected dashboard access
- Shared team workspace with task creator and assignee ownership
- Full task CRUD
- Task status tracking: `pending`, `in_progress`, `completed`
- Progress bar with one-click updates
- Automatic progress/status synchronization
- Live countdown clock per task
- Overdue and due-soon visibility
- Search and filtering by status, priority, assignee, and overdue state
- Dashboard insights for total, active, completed, due-soon, and overdue work
- Authorization rules for update/delete/progress actions
- Feature tests covering core workflows and edge cases

## Tech Stack

- PHP 8.3
- Laravel 13
- Blade
- Tailwind CSS 4
- Vite
- MySQL
- PHPUnit

## Core Product Decisions

- Authentication is included because this is a team-facing system and should not be openly editable by anyone.
- Team management is intentionally kept lightweight: one shared workspace instead of multi-tenant organization management.
- The timer is estimate-based, not a manual timesheet system.
- Progress and status are intentionally linked:
  - setting progress to `100` completes the task
  - marking a task completed sets progress to `100`
- The project favors a polished and believable scope over adding enterprise-level complexity.

## Authorization Rules

- Any authenticated user can view the dashboard and create tasks
- Task creator or assignee can update a task
- Only the task creator can delete a task
- Unauthorized task updates, deletions, and progress actions are blocked server-side

## Setup

### 1. Clone the project

```bash
git clone <your-repository-url>
cd task_management
```

### 2. Install backend dependencies

```bash
composer install
```

### 3. Create environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure MySQL

Update `.env` with your local MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database manually in MySQL before running migrations.

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. Install frontend dependencies

This repo already uses `.npmrc` with `ignore-scripts=true`, so a normal install is fine:

```bash
npm install
```

### 7. Run the app

In one terminal:

```bash
php artisan serve
```

In another terminal:

```bash
npm run dev
```

Then open:

```text
http://127.0.0.1:8000
```

## Running Tests

```bash
php artisan test
```

The test suite covers:
- authentication access
- task creation, update, and deletion
- task policy rules
- one-click progress actions
- progress/status synchronization
- overdue and timer state behavior
- search and filtering
- validation and authorization edge cases

## Project Structure

High-level organization:

- `app/Http/Controllers`
  - auth flow and task actions
- `app/Http/Requests`
  - request validation for task create/update
- `app/Models`
  - `User` and `Task` domain models
- `app/Policies`
  - task authorization rules
- `app/Enums`
  - status and priority enums
- `resources/views`
  - Blade layouts, auth screens, dashboard, task views
- `tests/Feature`
  - end-to-end behavior tests

## Testing Approach

The testing strategy is intentionally behavior-focused rather than only model-focused.

Key priorities:
- protect business rules
- verify authorization boundaries
- confirm the dashboard behaves correctly under normal use
- validate the most important recruiter-visible features

The app uses feature tests heavily because the most valuable risks here are cross-layer:
- route protection
- form validation
- policy enforcement
- lifecycle synchronization
- dashboard rendering logic

## Assumptions

- This is a small internal team tool, not a public SaaS product
- One shared workspace is enough for the scope of this assignment
- Email verification, notifications, attachments, comments, and advanced RBAC are intentionally out of scope
- The timer reflects estimated delivery time, not employee timesheet tracking

## Future Improvements

- Activity timeline per task
- Task comments
- Board/list view toggle
- Seeder with richer demo workspace data
- Avatar support and profile settings
- Notifications for overdue or reassigned work
- Role-based admin controls

## Commit History Strategy

The implementation was intentionally split into small milestone commits:

1. project foundation and authentication
2. task domain and authorization rules
3. dashboard CRUD flows
4. progress actions
5. live timer and overdue logic
6. search, filters, and insights
7. UI polish
8. testing pass
9. documentation

This structure is meant to make the repository easier to review and to show a disciplined build process.
