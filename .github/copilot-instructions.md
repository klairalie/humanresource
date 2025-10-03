# Copilot Instructions for Humanresource (Laravel)

## Project Overview
This is a Laravel-based web application for human resource management. The codebase follows standard Laravel conventions but includes custom business logic and workflows specific to HR operations.

## Architecture & Key Components
- **app/**: Core application logic. Contains Controllers, Models, Mail, Notifications, Observers, Providers.
  - **Controllers**: Handle HTTP requests, business logic, and data flow.
  - **Models**: Eloquent ORM models for database tables (e.g., `Applicant`, `Employeeprofiles`, `Payroll`).
  - **Mail/Notifications**: Custom classes for sending emails and notifications.
- **resources/views/**: Blade templates for UI. Follows Laravel's view conventions.
- **routes/**: Defines application routes. `web.php` for web, `console.php` for CLI.
- **config/**: Application configuration (auth, mail, database, etc.).
- **database/**: Migrations, seeders, and factories for schema and test data.
- **public/**: Entry point (`index.php`), assets, and build output.
- **tests/**: Feature and Unit tests. Uses PHPUnit.

## Developer Workflows
- **Install dependencies**: `composer install` (PHP), `npm install` (JS/CSS)
- **Build assets**: `npm run build` (uses Vite)
- **Run server**: `php artisan serve`
- **Run tests**: `vendor\bin\phpunit` or `php artisan test`
- **Migrate DB**: `php artisan migrate`
- **Seed DB**: `php artisan db:seed`

## Project-Specific Patterns
- **Models**: Use Eloquent relationships. Example: `Applicant` links to `AssessmentResult`.
- **Controllers**: Business logic is often in controllers, not services.
- **Notifications/Mail**: Custom notification classes in `app/Notifications` and mailers in `app/Mail`.
- **Views**: Blade templates use Laravel's `@foreach`, `@if`, etc. Custom forms in `resources/views/Applicants/`.
- **Config**: Sensitive settings in `.env` (not committed).

## Integration Points
- **External packages**: See `composer.json` for PHP, `package.json` for JS.
- **Database**: Uses MySQL (default). Configured in `config/database.php` and `.env`.
- **Asset pipeline**: Vite for JS/CSS. Entry in `vite.config.js`.

## Conventions & Tips
- Follow PSR-4 autoloading for PHP classes.
- Use `php artisan` for most CLI tasks.
- Blade templates are in `resources/views/` and use `.blade.php` extension.
- Place new business logic in `app/` (not in `public/` or `resources/`).
- For new models, create migration, model, and optionally a factory and seeder.
- For new routes, add to `routes/web.php`.

## Example: Adding a New Feature
1. Create a model in `app/Models/` and migration in `database/migrations/`.
2. Add controller in `app/Http/Controllers/`.
3. Define route in `routes/web.php`.
4. Create Blade view in `resources/views/`.
5. Write tests in `tests/Feature/` or `tests/Unit/`.

## References
- [Laravel Documentation](https://laravel.com/docs)
- Key files: `app/Models/Applicant.php`, `resources/views/Applicants/Applicationform.blade.php`, `routes/web.php`, `vite.config.js`, `composer.json`, `package.json`

---
Update this file as new conventions or workflows emerge. For questions, check the README or Laravel docs.