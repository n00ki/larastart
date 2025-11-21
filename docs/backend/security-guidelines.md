# Security Standards

## Authentication & Authorization

### Laravel Authentication

- Use Laravel's built-in authentication system
- Implement proper CSRF protection (enabled by default)
- Use policies for all authorization logic
- Validate all user inputs through FormRequests

### Policy-Based Authorization

```php
<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list their todos
    }

    public function view(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create todos
    }

    public function update(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function delete(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }
}
```

### Controller Authorization

```php
<?php

class TodoController extends Controller
{
    public function show(Todo $todo): Response
    {
        $this->authorize('view', $todo);

        return inertia('todos/show', [
            'todo' => $todo->load('user'),
        ]);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): RedirectResponse
    {
        $this->authorize('update', $todo);

        $todo->update($request->validated());

        return redirect()->route('todos.show', $todo);
    }
}
```

## Data Protection

### Input Validation

Always validate user input through FormRequests:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your todo',
            'title.max' => 'Title cannot exceed 255 characters',
            'due_date.after' => 'Due date must be in the future',
        ];
    }
}
```

### SQL Injection Prevention

- Use Eloquent ORM exclusively (never raw queries without parameterization)
- Always use parameterized queries for any raw SQL

```php
// ✅ Secure - Using Eloquent
User::query()->where('email', $email)->first();

// ✅ Secure - Parameterized raw query (when necessary)
DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// ❌ Vulnerable - String concatenation
DB::select("SELECT * FROM users WHERE email = '{$email}'");
```

### Data Exposure Prevention

#### Inertia.js Props Security

Never expose sensitive data in Inertia props:

```php
<?php

// ✅ Safe - Only expose necessary data
return inertia('users/profile', [
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'created_at' => $user->created_at,
    ],
]);

// ❌ Dangerous - Exposes sensitive information
return inertia('users/profile', [
    'user' => $user, // This might expose password hashes, tokens, etc.
]);
```

#### Model Hidden Attributes

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at', // If sensitive
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }
}
```

## Rate Limiting

### API Rate Limiting

```php
<?php

// In RouteServiceProvider or routes file
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::post('/todos', [TodoController::class, 'store']);
    Route::put('/todos/{todo}', [TodoController::class, 'update']);
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);
});

// Custom rate limiting for sensitive operations
Route::middleware(['auth', 'throttle:5,1'])->group(function () {
    Route::post('/password/reset', [PasswordResetController::class, 'store']);
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'store']);
});
```

### Custom Rate Limiting

```php
<?php

// In AppServiceProvider
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    RateLimiter::for('sensitive-operations', function (Request $request) {
        return $request->user()
            ? Limit::perMinute(5)->by($request->user()->id)
            : Limit::perMinute(2)->by($request->ip());
    });
}
```

## HTTPS & Transport Security

### Production Requirements

- **Always use HTTPS** in production
- Configure proper SSL/TLS certificates
- Use Laravel's force HTTPS helpers

```php
<?php

// In AppServiceProvider for production
public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

### Security Headers

```php
<?php

// Custom middleware for security headers
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
```

## Session & Cookie Security

### Session Configuration

```php
<?php

// config/session.php
return [
    'lifetime' => 120, // Session timeout in minutes
    'expire_on_close' => false,
    'encrypt' => true,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100], // Garbage collection
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only in production
    'http_only' => true, // Prevent XSS
    'same_site' => 'lax',
    'partitioned' => false,
];
```

## Encryption & Key Management

### Laravel 12 Key Rotation

Leverage Laravel 12's graceful encryption key rotation:

```php
<?php

// Generate new key while maintaining access to old encrypted data
php artisan key:generate --show

// Update .env with new key
// Laravel 12 automatically handles decryption of old data
```

### Sensitive Data Encryption

```php
<?php

use Illuminate\Support\Facades\Crypt;

// Encrypt sensitive data before storage
$encryptedData = Crypt::encrypt($sensitiveData);

// Store in database
$user->update(['encrypted_notes' => $encryptedData]);

// Decrypt when needed
$decryptedData = Crypt::decrypt($user->encrypted_notes);
```

## Inertia.js Security

### Sensitive Data in Props

Avoid passing sensitive data in Inertia props. Prefer deferred/lazy props and server-side checks; enforce HTTPS.

### CSRF Protection

CSRF protection is enabled by default via Laravel middleware and Inertia adapters; no client configuration is required.

## Error Handling & Logging

### Secure Error Handling

```php
<?php

// In Handler.php - Don't expose sensitive information
public function render($request, Throwable $exception)
{
    // Log full error details
    logger()->error('Application error', [
        'exception' => $exception,
        'user_id' => auth()->id(),
        'url' => $request->url(),
        'ip' => $request->ip(),
    ]);

    // Return generic error to client in production
    if (app()->environment('production') && !$this->shouldReport($exception)) {
        return inertia('errors/500', [
            'message' => 'An unexpected error occurred. Please try again.',
        ])->toResponse($request)->setStatusCode(500);
    }

    return parent::render($request, $exception);
}
```

### Audit Logging

```php
<?php

// Custom middleware for sensitive operations
namespace App\Http\Middleware;

class AuditLog
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log sensitive operations
        if ($this->isSensitiveOperation($request)) {
            logger()->info('Sensitive operation performed', [
                'user_id' => auth()->id(),
                'action' => $request->route()->getActionName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    private function isSensitiveOperation(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }
}
```
