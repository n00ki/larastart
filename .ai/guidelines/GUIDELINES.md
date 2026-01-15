# LaraStart - Project Rules

> Laravel Boost provides general Laravel/Inertia/Pest patterns.
> Svelte MCP provides Svelte 5 documentation and conventions.

## Architecture

### Action Pattern (REQUIRED)

All business logic MUST live in Action classes. Controllers stay thin.

- Actions live in `app/Actions/` organized by domain
- Actions are `final readonly` classes with an `execute()` method
- Controllers inject Actions and call them with validated data
- See `docs/ARCHITECTURE.md` for detailed examples

### Database Rules

- **NEVER** use `DB::` facade - always use `Model::query()`
- Use `$guarded = []` instead of `$fillable`
- Use `Model::casts()` method (Laravel 12 style)
- Models should be `final class`

```php
// WRONG
DB::table('users')->where('id', 1)->first();
User::where('id', 1)->first();

// CORRECT
User::query()->where('id', 1)->first();
```

## Frontend

### Code Style

- Use `//` comments to describe flows clearly and concisely
- No JSDoc blocks unless it's an essential shared helper/util

### Svelte Components

- Files: kebab-case (`user-card.svelte`)
- Imports: PascalCase (`import UserCard from './user-card.svelte'`)
- UI components live in `@/components/ui/` (shadcn-svelte)

### shadcn-svelte Imports

```typescript
// Simple components - named imports
import { Button } from '@/components/ui/button';
// Compound components - namespace imports
import * as Card from '@/components/ui/card';
import * as Dialog from '@/components/ui/dialog';
```

### State Management Scaling

| Complexity | Pattern                   | Location                  |
| ---------- | ------------------------- | ------------------------- |
| Local      | `$state` in component     | Component file            |
| Complex    | Class-based state machine | Same file or co-located   |
| Global     | Class-based state machine | `@/lib/state/*.svelte.ts` |

## Testing

Name tests by WHAT they do for users, not implementation details.

```php
// CORRECT - domain-focused
it('allows user to update their profile')
it('prevents unauthorized access to admin dashboard')

// WRONG - implementation-focused
it('calls UpdateProfileAction')
it('uses AdminPolicy')
```

## Essential Commands

```bash
composer dev              # Start all services
composer lint:fix         # Format + lint
composer test             # Run tests
```

## Task Completion Checklist

Before marking any task complete:

- [ ] Run `composer lint:fix`
- [ ] Run `composer test`
