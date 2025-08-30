# LaraStart Documentation

This documentation provides comprehensive guidelines for the LaraStart project - a Laravel + Svelte starter kit with modern tooling and best practices.

## Documentation Structure

### 📁 General Guidelines

- [**Project Overview**](./project-overview.md) - Architecture, principles, and project structure
- [**Development Workflow**](./development-workflow.md) - Tools, commands, and development processes

### 📁 Backend Documentation

- [**PHP & Laravel Guidelines**](./backend/php-laravel-guidelines.md) - PHP 8.4 and Laravel 12 guidelines
- [**Architecture Guidelines**](./backend/architecture-guidelines.md) - Controllers, Actions, Models, and project patterns
- [**Database Guidelines**](./backend/database-guidelines.md) - Eloquent, migrations, and data handling
- [**Security Guidelines**](./backend/security-guidelines.md) - Authentication, authorization, and data protection
- [**Error Handling Guidelines**](./backend/error-handling-guidelines.md) - Exception handling and error patterns

### 📁 Frontend Documentation

- [**Svelte Guidelines**](./frontend/svelte-guidelines.md) - Svelte 5 runes, component patterns, and state management
- [**Inertia Guidelines**](./frontend/inertia-guidelines.md) - Navigation, forms, and type-safe routing with Wayfinder
- [**UI Guidelines**](./frontend/ui-guidelines.md) - TailwindCSS v4, shadcn-svelte, and design patterns

### 📁 Testing Documentation

- [**Testing Guidelines**](./testing/testing-guidelines.md) - Pest PHP, testing patterns, and coverage requirements

## Quick Reference

### Essential Commands

```bash
# Development
composer dev              # Start all development services
composer lint:fix         # Fix code formatting and linting
composer test             # Run complete test suite

# Frontend
bun run dev              # Frontend development server
bun run build            # Production build
```

### Key Principles

1. **Consistency First** - Follow established patterns
2. **Security by Default** - Secure coding practices
3. **Performance-Conscious** - Efficient, optimized code
4. **Type Safety** - Leverage PHP 8.4 and TypeScript
5. **Test-Driven** - Comprehensive test coverage
6. **Accessibility** - ARIA guidelines compliance

## Getting Help

- Review the specific documentation sections for detailed guidelines
- Check the project's README.md for setup instructions
- Refer to the official documentation for Laravel, Svelte, and other technologies used
