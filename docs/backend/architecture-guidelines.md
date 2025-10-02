# Architecture Patterns

## Controller Layer (`app/Http/Controllers`)

### Design Principles

- Keep controllers thin - delegate to Requests and Actions
- No abstract/base controllers (composition over inheritance)
- Use dependency injection for Services
- Return Inertia responses for Svelte components
- Organize by domain in subdirectories (e.g., `Auth/`, `Settings/`)

### Controller Naming Patterns

**Two approaches based on use case:**

#### 1. Resource Controllers (RESTful operations)

Use for standard CRUD operations on a resource:

- **Pattern**: `{Resource}Controller` (singular)
- **Methods**: Standard RESTful methods (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`)
- **Examples**: `TodoController`, `ProfileController`, `PasswordController`

#### 2. Single-Action Controllers (Focused operations)

Use for specific, focused operations that don't fit RESTful pattern:

- **Pattern**: `{Action}Controller` (verb-based)
- **Method**: Single `__invoke()` method OR focused methods (`create`, `store`)
- **Examples**: `LoginController`, `LogoutController`, `RegisterController`

### Controller Naming Examples

✅ **Resource Controllers (Settings/CRUD):**

```
Settings/
├── ProfileController       # edit(), update()
├── PasswordController      # edit(), update()
└── AccountController       # edit(), destroy()
```

✅ **Single-Action Controllers (Auth):**

```
Auth/
├── LoginController              # create(), store()
├── LogoutController             # __invoke()
├── RegisterController           # create(), store()
├── ForgotPasswordController     # create(), store()
├── ResetPasswordController      # create(), store()
└── ConfirmPasswordController    # show(), store()
```

❌ **Avoid:**

- `AuthenticatedSessionController` - Too verbose
- `PasswordResetLinkController` - Use `ForgotPasswordController`
- `NewPasswordController` - Use `ResetPasswordController`
- `UserManagementController` - Too broad, split into focused controllers

### Controller Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUserTodoAction;
use App\Http\Requests\CreateTodoRequest;
use Illuminate\Http\RedirectResponse;

class TodoController extends Controller
{
    public function store(CreateTodoRequest $request, CreateUserTodoAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully');
    }
}
```

### Single-Action Controller Example

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

## Request Validation (`app/Http/Requests`)

### FormRequest Standards

- Use FormRequest for all validations
- **Name based on user intent or action purpose** (e.g., `RegisterRequest`, `LoginRequest`, `DeleteAccountRequest`)
- For CRUD operations, you can use action verbs with entity: `CreateTodoRequest`, `UpdateTodoRequest`
- Include authorization logic when needed
- Define custom error messages
- Organize by domain in subdirectories (e.g., `Auth/`, `Settings/`)

### Request Naming Examples

✅ **Good:**

- `RegisterRequest` - User-intent focused (better than `CreateUserRequest`)
- `LoginRequest` - Clear purpose
- `DeleteAccountRequest` - Specific action (better than `DeleteProfileRequest`)
- `CreateTodoRequest` - Standard CRUD naming

❌ **Avoid:**

- `UserRequest` - Too generic
- `TodoForm` - Wrong suffix
- `CreateUserRequest` - Use `RegisterRequest` for registration flows

### Request Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your todo',
            'due_date.after' => 'Due date must be in the future',
        ];
    }
}
```

## Business Logic (`app/Actions`)

### Action Pattern

- Use the Action pattern for all business logic
- **Name with Verb + Entity + Action suffix** (`CreateUserAction`, `UpdateUserProfileAction`, `DeleteUserAccountAction`)
- Include the entity name for clarity (e.g., `User`, `Todo`, `Post`)
- Return domain objects, not HTTP responses
- Keep actions focused on a single responsibility
- Organize by domain in subdirectories (e.g., `Auth/`, `Settings/`)

### Action Naming Examples

✅ **Good:**

- `CreateUserAction` - Clear entity reference
- `UpdateUserProfileAction` - Specific about what's being updated
- `DeleteUserAccountAction` - Clear distinction from profile deletion
- `SendUserPasswordResetLinkAction` - Descriptive and specific
- `CreateUserTodoAction` - Includes both user and todo entities

❌ **Avoid:**

- `CreateAction` - Too generic
- `UpdateProfile` - Missing entity and Action suffix
- `DeleteUser` - Missing Action suffix
- `ResetPassword` - Ambiguous (user? admin? what entity?)
- `CreateTodo` - Missing Action suffix

### Action Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;

class CreateUserTodoAction
{
    public function handle(User $user, array $data): Todo
    {
        return $user->todos()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'completed' => false,
        ]);
    }
}
```

## Models (`app/Models`)

### Model Standards

- **Avoid `$fillable`** - use explicit assignment or `$guarded = []`
- Use typed properties and return types
- Implement proper relationships
- Add model factories for all models
- Use the new `Model::casts()` method for dynamic casting

### Model Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->completed) {
            return false;
        }

        return $this->due_date->isPast();
    }
}
```

## Jobs & Queues (`app/Jobs`)

### Job Standards

- Use typed job classes with proper failure handling
- Implement idempotent operations
- Use job batching for related operations
- Handle failures gracefully

### Job Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ProcessUserDataAction;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Batchable;

    public function __construct(
        private readonly User $user,
        private readonly array $data
    ) {}

    public function handle(ProcessUserDataAction $action): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $action->handle($this->user, $this->data);
    }

    public function failed(\Throwable $exception): void
    {
        // Handle job failure
        logger()->error('User data processing failed', [
            'user_id' => $this->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
```

## Policies (`app/Policies`)

### Policy Standards

- Use policies for all authorization logic
- Keep policies focused and testable
- Use proper type hints
- Follow resource-based naming

### Policy Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    public function view(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function update(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function delete(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create todos
    }
}
```

## Migration Standards (`database/migrations`)

### Migration Patterns

- Omit `down()` method in new migrations
- Use descriptive column names
- Add proper indexes for performance
- Include foreign key constraints

### Migration Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'completed']);
            $table->index('due_date');
        });
    }
};
```

## Service Layer (Optional)

### When to Use Services

- Complex business logic that spans multiple models
- External API integrations
- Heavy computational operations
- Third-party service integrations

### Service Pattern Example

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl
    ) {}

    public function sendWelcomeNotification(User $user): bool
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/notifications", [
                'email' => $user->email,
                'template' => 'welcome',
                'data' => [
                    'name' => $user->name,
                ],
            ]);

        return $response->successful();
    }
}
```
