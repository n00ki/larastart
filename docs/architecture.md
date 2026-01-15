# Architecture

## Stack

**Backend:** Laravel 12, PHP 8.5, Inertia.js 2.0, Pest PHP

**Frontend:** Svelte 5, TypeScript, TailwindCSS v4, shadcn-svelte

**E2E Type Safety:** Laravel Wayfinder

## Backend Patterns

### Action Pattern

All business logic lives in Action classes. Controllers stay thin.

```php
// app/Actions/User/UpdateProfileAction.php
final readonly class UpdateProfileAction
{
    public function execute(User $user, array $data): User
    {
        return tap($user)->update($data);
    }
}

// app/Http/Controllers/ProfileController.php
public function update(UpdateProfileRequest $request, UpdateProfileAction $action): RedirectResponse
{
    $action->execute($request->user(), $request->validated());
    return back();
}
```

### Model Conventions

```php
final class User extends Authenticatable
{
    protected $guarded = [];  // Not $fillable

    protected function casts(): array  // Method, not property
    {
        return ['email_verified_at' => 'datetime'];
    }
}
```

### Database Queries

Always use `Model::query()`, never `DB::`:

```php
// Correct
User::query()->where('active', true)->get();

// Wrong
DB::table('users')->where('active', true)->get();
```

## Frontend Patterns

### Type-Safe Routing with Wayfinder

```typescript
import { show } from '@/actions/App/Http/Controllers/UserController';
import { dashboard } from '@/routes';

router.visit(show(userId));
```

### State Management Scaling

| Complexity | Pattern                   | Location                  |
| ---------- | ------------------------- | ------------------------- |
| Local      | `$state` in component     | Component file            |
| Complex    | Class-based state machine | Same file or co-located   |
| Global     | Class-based state machine | `@/lib/state/*.svelte.ts` |

```typescript
// @/lib/state/counter.svelte.ts
class Counter {
  count = $state(0);
  double = $derived(this.count * 2);

  increment = () => this.count++;

  get count() {
    return this.#count;
  }
  set count(value) {
    this.#count = value;
  }
}

export const counter = new Counter();
```

```typescript
// @/lib/state/auth.svelte.ts
class AuthState {
  user = $state<User | null>(null);
  isAuthenticated = $derived(this.user !== null);

  login = (user: User) => {
    this.user = user;
  };

  logout = () => {
    this.user = null;
  };
}

export const auth = new AuthState();
```

### shadcn-svelte Components

```typescript
// Simple components - named imports
import { Button } from '@/components/ui/button';
// Compound components - namespace imports
import * as Dialog from '@/components/ui/dialog';
```

Add new components: `bunx shadcn-svelte add <component>`

## Testing

Tests are domain-focused, not implementation-focused:

```php
// Good
it('allows user to update their profile');

// Bad
it('calls UpdateProfileAction');
```

## Directory Structure

```
app/
├── Actions/           # Business logic
├── Http/
│   ├── Controllers/   # Thin, delegate to Actions
│   └── Requests/      # Form validation
└── Models/            # Eloquent models

resources/js/
├── components/        # Svelte components
│   └── ui/            # shadcn-svelte
├── layouts/           # Page layouts
├── lib/state/         # Global state machines
├── pages/             # Inertia pages
├── actions/           # Wayfinder (auto-generated)
└── routes/            # Wayfinder (auto-generated)
```
