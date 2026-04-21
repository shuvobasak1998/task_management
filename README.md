# TaskFlow Studio

TaskFlow Studio is a polished Laravel-based task management system for small teams. It focuses on secure access, clear task ownership, fast progress updates, live countdown timing, and a clean split between operational task management and analytical reporting.

This project was built with a recruiter-facing mindset:
- clean architecture
- clear commit history
- focused feature scope
- practical security
- strong behavior-driven tests

## Features

- Authentication with register, login, logout, and protected application access
- Dedicated `Dashboard` page for analytics and summary signals
- Dedicated `Tasks` workspace page for task management and progress actions
- Full task CRUD
- Task assignment between team members
- Task status tracking: `pending`, `in_progress`, `completed`
- Progress bar with one-click updates
- Reopen flow for completed tasks by lowering progress below `100`
- Automatic progress/status synchronization
- Async JSON progress endpoint for inline updates
- Live countdown clock per task
- Overdue and due-soon visibility
- Search and filtering by status, priority, assignee, and overdue state
- Dashboard insights for total, assigned-to-me, created-by-me, active, completed, due-soon, and overdue work
- Monthly completion ratio and status distribution analytics
- Context-aware redirects back to dashboard or workspace after create/delete/progress actions
- Authorization rules for update/delete/progress actions
- Feature and unit tests covering core workflows and business rules

## Tech Stack

- PHP 8.3
- Laravel 13
- Blade
- Tailwind CSS 4
- Vite
- MySQL by default
- PHPUnit

## Core Product Decisions

- Authentication is included because this is a team-facing system and should not be openly editable by anyone.
- Team management is intentionally kept lightweight: one shared workspace instead of multi-tenant organization management.
- The product uses a deliberate two-page split:
  - `Dashboard` for analytics, trends, and delivery signals
  - `Tasks` for day-to-day workspace operations
- The timer is estimate-based, not a manual timesheet system.
- Progress and status are intentionally linked:
  - setting progress to `100` completes the task
  - marking a task completed sets progress to `100`
- The project favors a polished and believable scope over adding enterprise-level complexity.

## Authorization Rules

- Any authenticated user can view the dashboard, access the tasks workspace, and create tasks
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

### 4. Configure the database

This project uses MySQL as the default database.

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

If you want to use SQLite instead, you can switch `.env` to:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/task_management/database/database.sqlite
```

### 5. Run database migrations

```bash
php artisan migrate
```

### 5.1 Seed demo users and tasks

```bash
php artisan db:seed
```

This seeds:
- 10 users total
- 2 stable reviewer accounts
- 10 demo tasks across pending, in-progress, completed, due-soon, and overdue states

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

## Reviewer Login

After running `php artisan db:seed`, reviewers can sign in with either of these seeded accounts:

- `review.admin@example.com` / `password`
- `review.member@example.com` / `password`

Additional seeded users are generated automatically, and their password is also `password`.

## Running Tests

```bash
php artisan test
```

The test suite covers:
- authentication access
- dashboard analytics rendering
- tasks workspace rendering and filtering
- task creation, update, deletion, and redirects
- task policy rules
- one-click progress actions
- reopen and inline progress JSON responses
- progress/status synchronization
- overdue and timer state behavior
- due-soon and countdown business rules
- validation and authorization edge cases

## Application Flow

- `/dashboard`
  - Analytical overview page
  - Shows summary cards, assigned/created ownership signals, monthly completion ratio, status distribution, overdue tasks, and due-soon tasks
  - Includes a create-task modal
- `/tasks`
  - Main workspace page
  - Shows the searchable/filterable task table, timers, ownership, assignees, and action buttons
  - Includes the primary create-task modal and task progress actions

## Project Structure

High-level organization:

- `app/Http/Controllers`
  - auth flow and task actions
- `app/Services/Tasks`
  - page data building for dashboard/workspace rendering
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
- `tests/Unit`
  - focused business-rule tests

## Testing Approach

The testing strategy combines feature tests for user-facing flows with focused unit tests for task lifecycle rules.

Key priorities:
- protect business rules
- verify authorization boundaries
- confirm the dashboard and tasks workspace each behave according to their current responsibilities
- validate the most important recruiter-visible features

Feature tests cover the most valuable cross-layer risks:
- route protection
- form validation
- policy enforcement
- redirects after mutations
- dashboard and workspace rendering logic

Unit tests cover the model-level rules that should remain stable even if the UI changes:
- progress/status/completed-at synchronization
- overdue and due-soon calculations
- remaining-time calculation behavior

## Architecture Notes

- Authentication gates all task management screens.
- `TaskPolicy` controls who can update and delete tasks.
- Form requests validate create and update input before the controller mutates data.
- `TaskPageDataBuilder` centralizes the shared query and analytics preparation used by both page types.
- The `Task` model owns lifecycle rules such as status/progress synchronization and timer-related helpers.

## Assumptions

- This is a small internal team tool, not a public SaaS product
- One shared workspace is enough for the scope of this assignment
- The current split between analytics and workspace is the intended UX direction
- Email verification, notifications, attachments, comments, and advanced RBAC are intentionally out of scope
- The timer reflects estimated delivery time, not employee timesheet tracking

## Review Notes

- Authentication is intentionally enabled for the assessment review flow.
- Demo reviewer credentials are seeded in [database/seeders/UserSeeder.php](/home/shuvo_dev/projects/task_management/database/seeders/UserSeeder.php:18).
- The default password for seeded accounts is `password`.

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
