# LaraStart

**The ultimate _mise en place_ for your next Laravel + Svelte project 🚀**

![Version](https://img.shields.io/badge/version-1.0.0-blue) [![PHP Version](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)](https://php.net/) [![Svelte Version](https://img.shields.io/badge/Svelte-5-FF3E00?logo=svelte&logoColor=white)](https://svelte.dev/) [![CI](https://github.com/n00ki/larastart/actions/workflows/ci.yml/badge.svg)](https://github.com/n00ki/larastart/actions/workflows/ci.yml)

<div align="center">

<img src="https://res.cloudinary.com/nshemesh/image/upload/v1771517281/larastart/meta.png" alt="LaraStart" width="600">

<a href="https://larastart.laravel.cloud">🌐 View Demo</a> ·
<a href="#getting-started">⚡ Quick Start</a> ·
<a href="#documentation">📚 Documentation</a>

</div>

## Tech Stack

- [🐘 Laravel 12](https://laravel.com/) - Latest PHP framework with modern features
- [🔧 PHP 8.5](https://php.net/) - Latest PHP with property hooks and performance improvements
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

# Generate AI assistant guidelines (optional)
php artisan boost:update

# Build frontend (required once)
bun run build

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
composer fix

# Generate IDE helpers
composer annotate

# Run tests
composer test
```

## Project Structure

```
app/
├── Actions/                   # Business logic (Action pattern)
│   ├── Fortify/               # Fortify authentication actions
│   └── User/                  # User domain actions (profile, password, account)
├── Concerns/                  # Reusable traits (validation rules, etc.)
├── Http/
│   ├── Controllers/           # Thin controllers (delegate to Actions)
│   ├── Middleware/            # Custom middleware
│   ├── Requests/              # Form validation with custom messages
│   └── Responses/             # Custom response contracts (Fortify, etc.)
│       └── Auth/              # Login / logout / register response handling
├── Jobs/                      # Queue jobs
├── Models/                    # Eloquent models with typed properties
├── Policies/                  # Authorization logic
├── Providers/                 # Service providers


resources/
├── css/
│   └── app.css               # Global CSS
└── js/
    ├── actions/              # Wayfinder-generated actions
    ├── components/           # Svelte components
    │   └── ui/               # shadcn-svelte UI components
    ├── hooks/                # Svelte hooks (theme, utilities)
    ├── layouts/              # Page layouts (auth, app, settings)
    ├── lib/                  # Utilities and state machines
    │   └── state/            # Global state (class-based, *.svelte.ts)
    ├── pages/                # Inertia.js pages
    ├── routes/               # Wayfinder-generated routes
    ├── types/                # TypeScript type definitions
    ├── wayfinder/            # Wayfinder runtime setup
    ├── app.ts                # Main entry point
    └── ssr.ts                # SSR entry point

tests/
├── Browser/                  # Browser tests (end-to-end)
├── Feature/                  # Feature tests
└── Unit/                     # Unit tests
```

### Architecture Decisions

- **No Abstract Controllers**: Composition over inheritance
- **Action Pattern**: Single responsibility for business logic
- **Request Validation**: Centralized form validation with custom messages
- **Type Safety**: End-to-end type safety from PHP to TypeScript
- **Auth Response Contracts**: Fortify responses are customized via `app/Http/Responses/Auth/*Response.php`
- **Flash Toasts**: One-time toasts use `Inertia::flash()` + centralized frontend handling in `resources/js/layouts/base-layout.svelte`

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

### For Humans

- **[Architecture Decisions](/docs/architecture.md)** - Why we chose certain patterns

### For AI Assistants

This project uses **[Laravel Boost](https://boost.laravel.com)** for AI guidance:

- **Laravel/Inertia/Pest/Wayfinder:** Automatically detected and provided by Laravel Boost
- **Svelte 5:** Use the Svelte MCP server for up-to-date documentation, patterns and conventions

After updating packages, run `php artisan boost:update` to refresh AI guidelines.

## Contributing

Contributions are welcome! Feel free to submit a Pull Request.

## Acknowledgments

Special thanks to all incredible contributors to the open-source community!

---

Built with ❤️ for the Laravel and Svelte communities
