# Architecture

## Stack

**Backend:** Laravel 12, PHP 8.5, Inertia.js 2.0, Pest PHP

**Frontend:** Svelte 5, TypeScript, TailwindCSS v4, shadcn-svelte

**E2E Type Safety:** Laravel Wayfinder

## Backend Patterns

### Action Pattern

All business logic lives in Action classes. Controllers stay thin.

```php
// app/Actions/User/UpdateUserProfile.php
final readonly class UpdateUserProfile
{
    /** @param array<string, mixed> $data */
    public function handle(User $user, array $data): void
    {
        $user->update($data);
    }
}

// app/Http/Controllers/User/ProfileController.php
public function update(UpdateProfileRequest $request, UpdateUserProfile $action): RedirectResponse
{
    $action->handle($request->user(), $request->validated());
    Inertia::flash([
        'type' => 'success',
        'message' => __('settings.profile_updated'),
    ]);

    return to_route('profile.edit');
}
```

### Auth + Flash Response Pattern

Fortify auth redirects are customized via response contracts. This keeps auth
success messaging consistent with app-level flash toasts.

```php
// app/Http/Responses/Auth/LoginResponse.php
final class LoginResponse implements LoginResponseContract
{
    public function toResponse(mixed $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        Inertia::flash([
            'type' => 'success',
            'message' => __('auth.logged_in'),
        ]);

        return redirect()->intended(Fortify::redirects('login'));
    }
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

Avoid using `DB::` facade query operations for domain logic. Use `Model::query()` for domain reads/writes, and `DB::transaction()` for DB-mutating actions:

```php
// Correct
User::query()->where('active', true)->get();

DB::transaction(function (): void {
    User::query()->where('active', true)->update(['active' => false]);
});

// Wrong
DB::table('users')->where('active', true)->get();
```

## Frontend Patterns

### Type-Safe Routing with Wayfinder

```typescript
import { edit } from '@/actions/App/Http/Controllers/User/ProfileController';
import { dashboard } from '@/routes';

router.visit(edit());
router.visit(dashboard());
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

## Reusable Traits (Concerns)

Shared logic lives in `app/Concerns/` as traits:

```php
// app/Concerns/PasswordValidationRules.php
trait PasswordValidationRules
{
    /** @return array<int, mixed> */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::defaults(), 'confirmed'];
    }
}

// app/Http/Requests/User/UpdatePasswordRequest.php
final class UpdatePasswordRequest extends FormRequest
{
    use PasswordValidationRules;

    public function rules(): array
    {
        return [
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ];
    }
}
```

## Directory Structure

```
app/
├── Actions/           # Business logic (Action pattern)
├── Concerns/          # Reusable traits
├── Http/
│   ├── Controllers/   # Thin, delegate to Actions
│   ├── Middleware/    # Inertia / theme middleware
│   ├── Requests/      # Form validation
│   └── Responses/
│       └── Auth/      # Fortify custom auth response contracts
├── Jobs/              # Queue jobs
├── Models/            # Eloquent models
├── Policies/          # Authorization
└── Providers/         # Service providers

resources/js/
├── actions/           # Wayfinder action functions (generated)
├── app.ts             # Client entrypoint
├── components/        # Svelte components
│   └── ui/            # shadcn-svelte
├── hooks/             # Svelte hooks (theme, utilities)
├── layouts/           # Page layouts
├── lib/
│   └── state/         # Global state machines (*.svelte.ts)
├── pages/             # Inertia pages
├── routes/            # Wayfinder route functions (generated)
├── ssr.ts             # SSR entrypoint
├── types/             # TypeScript type definitions
└── wayfinder/         # Wayfinder runtime setup

tests/
├── Browser/           # Browser tests
├── Feature/           # Feature tests
└── Unit/              # Unit tests
```
