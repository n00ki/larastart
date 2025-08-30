# Project Overview

## General Principles

- **Consistency First**: Follow established patterns and conventions
- **Security by Default**: Apply secure coding practices throughout
- **Performance-Conscious**: Write efficient, optimized code based on latest best practices
- **Documentation**: Code should always be well documented, with clear and concise comments
- **Test-Driven**: All features must include comprehensive tests
- **Type Safety**: Leverage PHP 8.4 and TypeScript type systems

## Project Structure & Architecture

### Laravel Backend Structure

```
app/
├── Actions/                   # Business logic (Action pattern)
│   ├── Auth/                  # Authentication actions
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
```

### Frontend Structure

```
resources/js/
├── actions/                   # Wayfinder actions (automatically generated)
├── components/                # Svelte components
│   ├── ui/                    # shadcn-svelte UI components
├── hooks/                     # Svelte hooks and utilities
├── layouts/                   # Layout components (auth, app, settings, etc.)
├── lib/                       # Utility functions
├── pages/                     # Page components
├── routes/                    # Wayfinder routes (automatically generated)
├── types/                     # TypeScript type definitions
├── wayfinder/                 # Wayfinder (automatically generated)
├── app.ts                     # Main entry point
└── ssr.ts                     # Server-side rendering entry point
```

## Architecture Decisions

### Backend Architecture

- **No Abstract/Base Controllers**: Composition over inheritance
- **Action Pattern**: Single responsibility for business logic
- **Request Validation**: Centralized form validation with custom messages
- **Model Design**: Use `$guarded = []` instead of `$fillable`
- **Policy-Based Authorization**: Use policies for all authorization logic

### Frontend Architecture

- **Component-First**: Break UI into reusable, focused components
- **State Management**: Scale from local → complex (state machines) → global (shared stores)
- **Type Safety**: End-to-end type safety from PHP to TypeScript via Wayfinder
- **Performance**: Leverage Svelte 5 runes and Inertia.js 2.0 optimizations

## Technology Stack Integration

### Laravel 12 + PHP 8.4

- Use modern PHP features (readonly classes, property hooks)
- Enable strict types: `declare(strict_types=1);`
- Leverage Laravel 12's new features (Model::casts(), improved error handling)

### Svelte 5 + TypeScript

- Use Svelte 5 runes for all reactive state
- Implement proper TypeScript interfaces
- Follow functional and declarative programming patterns

### Inertia.js 2.0 + Laravel Wayfinder

- Type-safe routing with auto-generated TypeScript definitions
- Partial reloads and performance optimizations
- Seamless server-client data flow

### TailwindCSS v4 + shadcn-svelte

- Utility-first styling with latest TailwindCSS features
- Consistent component library with shadcn-svelte
- Responsive, accessible design patterns

## Quality Standards

### Code Quality Tools

- **Pint**: PHP code formatting and style
- **PHPStan**: Static analysis and type checking
- **Rector**: Code quality and modernization
- **ESLint**: JavaScript/TypeScript/Svelte formatting and linting

### Testing Requirements

- **Comprehensive test types**: Unit, Feature, Browser
- **Test-driven development**: Write tests before implementation
- **No test removal**: Don't remove tests without approval

### Performance Standards

- **Database**: Prevent N+1 queries, proper indexing, eager loading
- **Frontend**: Code splitting, lazy loading, derived state optimization
- **Caching**: Implement caching for expensive operations
- **Bundle Size**: Monitor and optimize bundle sizes
