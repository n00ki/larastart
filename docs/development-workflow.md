# Development Workflow

## Development Tools & Configuration

This project leverages several development tools to ensure code quality and consistency:

- **Pint** - PHP code formatting and linting
- **PHPStan** - Type safety checking (Level 6)
- **Rector** - Code quality and modernization
- **ESLint** - JavaScript/TypeScript/Svelte formatting and linting
- **Pest** - PHP testing framework
- **TypeScript** - Type checking and enhanced development experience
- **lint-staged** - linting and formatting code before commit

## Essential Commands

### Development Server

```bash
# Start all development services
composer dev

# Frontend only
bun run dev
```

### Code Quality

```bash
# Check formatting and linting (backend + frontend)
composer lint

# Fix formatting and linting issues
composer lint:fix

# Run complete test suite
composer test

# Run specific test types
composer test:unit          # Unit tests only
composer test:feature       # Feature tests only
composer test:browser       # Browser tests only
composer test:types         # PHPStan static analysis
composer test:type-coverage # type coverage verification
composer test:all           # All tests with coverage

# Generate IDE helpers
composer annotate
```

### Frontend Specific

```bash
# Install dependencies
bun install

# Build for production
bun run build

# Type checking
bun run type-check

# ESLint formatting and linting
bun run lint                # Check ESLint rules
bun run lint:fix            # Fix ESLint issues
```

### Laravel Wayfinder

```bash
# Generate TypeScript route definitions
php artisan wayfinder:generate
```

## Task Completion Checklist

Before marking any task complete:

- [ ] **Update any documentation** that may be affected by the changes
- [ ] **Run `composer lint:fix`** and fix all issues
- [ ] **Run `composer run test`** and ensure all tests pass

## Code Quality Standards

### Configuration Adherence

- **Actively adhere to all project configuration files** (`.editorconfig`, `.pint.json`, `eslint.config.js`, `phpstan.neon`, `rector.php`, `tsconfig.json`, etc.)
- **Address all linting tool output** (errors and warnings) before proceeding
- **Follow strict TypeScript configuration** with proper type annotations

## Environment Setup

### Development Database

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database (if seeders exist)
php artisan db:seed
```

## Git Workflow

### Commit Messages

- Use conventional commits format
- Be descriptive and specific
- Reference issues when applicable

### Pull Request Requirements

- Pass all automated checks
- Include test coverage for new features
- Update documentation if needed
- Get approval from code owners
