# LaraStart - Project Rules

> Laravel Boost provides general Laravel/Inertia/Pest patterns.
> Svelte MCP provides Svelte 5 documentation and conventions.

## Architecture

### Action Pattern (REQUIRED)

All business logic MUST live in Action classes. Controllers stay thin.

- Actions live in `app/Actions/` organized by domain
- Actions are `final readonly` classes with a `handle()` method
- Controllers inject Actions and call them with validated data
- See `docs/architecture.md` for detailed examples

### Database Rules

- **AVOID** using the `DB::` facade for domain queries and writes - use `Model::query()` instead
- Use `DB::transaction()` in DB-mutating Action `handle()` methods
- Avoid `DB::table()` and raw query builder usage for domain logic
- Use `$guarded = []` instead of `$fillable`
- Use `Model::casts()` method (Laravel 12 style)
- Models should be `final class`

```php
// WRONG
DB::table('users')->where('id', 1)->first();
User::where('id', 1)->first();

// CORRECT
User::query()->where('id', 1)->first();

DB::transaction(function (): void {
    User::query()->where('id', 1)->update(['name' => 'Taylor']);
});
```

## Frontend

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

## Comments

Code should be self-documenting. Only use comments when essential.

### PHP

**PHPDoc blocks - Use for:**

- Array/collection type hints: `@param array<string, mixed>`, `@return array<int, User>`
- Exception annotations: `@throws ValidationException`

**PHPDoc blocks - Avoid:**

- Verbose descriptions that repeat method names
- Obvious summaries for self-explanatory methods

```php
// WRONG - verbose and obvious
/** Update the user's profile information. */
public function handle(User $user, array $data): void

// CORRECT - only type hint
/** @param array<string, mixed> $data */
public function handle(User $user, array $data): void
```

### Svelte / TypeScript

- Use `//` comments to describe flows in-line clearly and concisely if essential
- No JSDoc blocks unless it's an essential shared helper/util

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

## Authentication + Flash

- For Fortify auth success redirects (`login`, `logout`, `register`), use custom
  response contract implementations in `app/Http/Responses/Auth/`.
- Bind Fortify response contracts in `App\Providers\FortifyServiceProvider::register()`.
- Use `Inertia::flash()` for one-time success toasts.
- Frontend toast handling should stay centralized in
  `resources/js/layouts/base-layout.svelte` and route through utilities in
  `resources/js/lib/utils.ts`.

## Essential Commands

```bash
composer dev              # Start all services
composer fix              # Format + lint
composer test             # Run tests
```

## Task Completion Checklist

Before marking any task complete:

- [ ] Run `composer fix`
- [ ] Run `composer test`
