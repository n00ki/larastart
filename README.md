# LaraStart

**The ultimate _mise en place_ for your next Laravel + Svelte project 🚀**

<div align="center">
  <img src="https://res.cloudinary.com/nshemesh/image/upload/v1756713825/larastart/meta.png" alt="LaraStart" width="600">

<a href="https://larastart.laravel.cloud">🌐 View Demo</a> ·
<a href="#getting-started">⚡ Quick Start</a> ·
<a href="#documentation">📚 Documentation</a>

</div>

## Tech Stack

- [🐘 Laravel 12](https://laravel.com/) - Latest PHP framework with modern features
- [🔧 PHP 8.4](https://php.net/) - Latest PHP with property hooks and performance improvements
- [🛡️ Inertia.js 2.0](https://inertiajs.com/) - Modern monolith approach with SPA feel
- [🛣️ Laravel Wayfinder](https://github.com/laravel/wayfinder) - Type-safe routing for Laravel + TypeScript
- [🟠 Svelte 5](https://svelte.dev/) - Revolutionary frontend framework with runes
- [💨 TailwindCSS v4](https://tailwindcss.com/) - Utility-first CSS with latest features
- [🎨 shadcn-svelte](https://www.shadcn-svelte.com/) - Beautiful, accessible component library
- [📘 TypeScript](https://typescriptlang.org/) - Type safety and enhanced developer experience
- [🧪 Pest PHP](https://pestphp.com/) - Elegant PHP testing framework
- [🔍 PHPStan](https://phpstan.org/) - Static analysis for PHP (Level 6)
- [✨ Laravel Pint](https://laravel.com/docs/pint) - Opinionated PHP code style fixer
- [🔄 Rector](https://getrector.org/) - Automated code upgrades and refactoring
- [📏 ESLint](https://eslint.org/) - JavaScript/TypeScript linting with Antfu config

...and more!

## Getting Started

### Installation

```bash
# Clone the repository
git clone https://github.com/n00ki/larastart.git my-app
cd my-app

# Install PHP dependencies
composer install

# Install Node.js dependencies
bun install

# Setup environment variables
cp .env.example .env
# Edit .env with your configuration

# Generate application key
php artisan key:generate

# Setup database
touch database/database.sqlite
php artisan migrate

# Start development environment
composer dev
```

### Development

```bash
# Standard development (all services)
composer dev

# Development with SSR
composer dev:ssr

# Check format & linting
composer lint

# Format and Lint code
composer lint:fix

# Generate IDE helpers
composer annotate

# Run tests
composer test
```

## Project Structure

```
app/
├── Actions/                   # Business logic (Action pattern)
│   ├── Auth/                  # Authentication actions
│   └── User/                  # User domain actions (profile, password, account)
├── Enums/                     # Enum classes
├── Http/
│   ├── Controllers/           # Thin controllers (delegate to Actions)
│   ├── Requests/              # Form validation with custom messages
│   └── Middleware/            # Custom middleware
├── Jobs/                      # Queue jobs
├── Models/                    # Eloquent models with typed properties
├── Policies/                  # Authorization logic
├── Providers/                 # Service providers
└── Services/                  # Service classes

resources/
├── css/
│   └── app.css               # Global CSS
└── js/
    ├── actions/              # Wayfinder-generated actions
    ├── components/           # Svelte components
    │   └── ui/               # shadcn-svelte UI components
    ├── hooks/                # Svelte hooks and utilities
    ├── layouts/              # Page layouts (auth, app, settings)
    ├── lib/                  # Utility functions
    ├── pages/                # Inertia.js pages
    ├── routes/               # Wayfinder-generated routes
    ├── types/                # TypeScript type definitions
    ├── wayfinder/            # Wayfinder (automatically generated)
    ├── app.ts                # Main entry point
    └── ssr.ts                # Server-side rendering entry point

tests/
├── Browser/                  # Browser tests (end-to-end)
├── Feature/                  # Feature tests
└── Unit/
    ├── Actions/              # Business logic
    ├── Models/               # Model behavior
    ├── Requests/             # Request validation
    ├── Policies/             # Authorization logic
    ├── Jobs/                 # Queue jobs
    └── Services/             # Service classes
```

### Architecture Decisions

- **No Abstract Controllers**: Composition over inheritance
- **Action Pattern**: Single responsibility for business logic
- **Request Validation**: Centralized form validation with custom messages
- **Type Safety**: End-to-end type safety from PHP to TypeScript

## Testing

```bash
# Run all tests
composer test

# Run specific test suites
composer test:unit          # Unit tests only
composer test:feature       # Feature tests only
composer test:browser       # Browser tests only
composer test:types         # PHPStan static analysis
composer test:type-coverage # 100% type coverage verification
composer test:all           # All tests with coverage
```

## Documentation

Comprehensive development guidelines and best practices are available in the `/docs` folder:

- **[Documentation Index](/docs/README.md)** - Complete overview of all documentation
- **[Development Workflow](/docs/development-workflow.md)** - Commands, tools, and development processes
- **[PHP & Laravel Guidelines](/docs/backend/php-laravel-guidelines.md)**
- **[Architecture Guidelines](/docs/backend/architecture-guidelines.md)**
- **[Database Guidelines](/docs/backend/database-guidelines.md)**
- **[Security Guidelines](/docs/backend/security-guidelines.md)**
- **[Error Handling Guidelines](/docs/backend/error-handling-guidelines.md)**
- **[Svelte Guidelines](/docs/frontend/svelte-guidelines.md)**
- **[Inertia Guidelines](/docs/frontend/inertia-guidelines.md)**
- **[UI Guidelines](/docs/frontend/ui-guidelines.md)**
- **[Testing Guidelines](/docs/testing/testing-guidelines.md)** - Testing patterns and requirements

## Contributing

Contributions are welcome! Please review the documentation in `/docs` and feel free to submit a Pull Request.

## Acknowledgments

Special thanks to all incredible contributors to the open-source community!

---

Built with ❤️ for the Laravel and Svelte communities
