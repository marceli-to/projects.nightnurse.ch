# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 9 project management application with Vue.js frontend for Nightnurse. It includes:
- Multi-domain Laravel application with project management and quote system
- Vue.js 2.7 SPA frontend with TailwindCSS
- File upload and messaging system with markup/annotation capabilities
- Role-based access control (admin, client_admin, client)
- API integrations (Salesforce, Vertec, DeepL translation)

## Development Commands

### Frontend Build Commands
```bash
# Development build
npm run dev
npm run development

# Watch mode for development
npm run watch
npm run watch-poll

# Hot reload for development
npm run hot

# Production build
npm run prod
npm run production
```

### Backend Commands
```bash
# Run tests
vendor/bin/phpunit
php artisan test

# Start local server
php artisan serve

# Clear various caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Database operations
php artisan migrate
php artisan db:seed

# Generate application key
php artisan key:generate
```

### Composer Commands
```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Autoload optimization
composer dump-autoload
```

## Architecture

### Backend Structure
- **Models**: Eloquent models in `app/Models/` with Base model extending functionality
- **Controllers**: API controllers in `app/Http/Controllers/Api/` with versioned endpoints
- **Middleware**: Custom middleware including role checking, localization, project redirects
- **Services**: Business logic in `app/Services/` (Media, Quote, ProjectService, VertecApi)
- **Tasks**: Background processing in `app/Tasks/`

### Frontend Structure
- **Vue.js 2.7 SPA** in `resources/js/`
- **Components**: Organized in `resources/js/components/` with UI, form, and feature-specific components
- **Views**: Page components in `resources/js/views/`
- **Store**: Vuex store configuration in `resources/js/config/store.js`
- **Routing**: Vue Router in `resources/js/config/routes.js`

### Key Models
- **User**: Multi-role users with company associations
- **Company**: Client companies with projects
- **Project**: Core project entity with states, quotes, and access control
- **Message**: Project communication with file attachments
- **MessageFile**: File attachments with image processing and markup support
- **Markup**: Image annotation system for feedback
- **Feedback**: Project feedback system

### Authentication & Authorization
- Laravel Sanctum for API authentication
- Role-based middleware: `admin`, `client_admin`, `client`
- Project-based access control through `ProjectUser` pivot model

### Multi-Domain Setup
- Uses `gecche/laravel-multidomain` package
- Domains: `projects.nightnurse.ch` (main app), `my.nightnurse.ch` (quotes)
- Domain-specific routing and middleware

### File Processing
- Image processing with Intervention Image
- File upload handling with UUID-based storage
- Image markup/annotation system using Konva.js/Fabric.js on frontend
- PDF generation for quotes using DomPDF

### Frontend Architecture
- Vue 2.7 with Composition API support
- TailwindCSS for styling with custom components
- Vuex for state management
- Vue Router for SPA navigation
- Custom mixins for common functionality (DateTime, ErrorHandling, Filters)

### Build System
- Laravel Mix for asset compilation
- Webpack configuration with Vue.js support
- SCSS compilation for quote system
- Asset versioning for cache busting

## Testing

Run PHP tests with: `vendor/bin/phpunit`
Tests are located in `tests/Feature/` and `tests/Unit/`

## Database

Uses MySQL/MariaDB with Laravel migrations in `database/migrations/`
Eloquent ORM with relationships and soft deletes on key models

## Queue System

Background job processing for:
- Email sending (invitations, notifications)
- File processing (image previews, cleanup)
- External API synchronization

## API Structure

- Public API v1 routes under `/api/v1/` with API token authentication
- Protected API routes under `/api/` with Sanctum authentication
- RESTful design with resource controllers
- Role-based route protection

## Key Features

- Project management with workflow states
- Real-time messaging with file attachments
- Image markup and annotation system
- PDF quote generation
- Multi-language support (DE/EN)
- Role-based access control
- File upload with preview generation
- External API integrations