# Testing Standards

## Test Framework & Organization

### Test Frameworks

- **Use Pest PHP** for all tests

### Tests Organization

```
tests/
├── Browser/             # Browser tests (end-to-end)
├── Feature/             # Feature tests (domain/behavior-focused)
└── Unit/
    ├── Actions/         # Business logic (mirrors Actions structure)
    ├── Models/          # Model behavior
    ├── Requests/        # Request validation (mirrors Requests structure)
    ├── Policies/        # Authorization logic
    ├── Jobs/            # Queue jobs
    └── Services/        # Service classes
```

### Test Naming Conventions

#### Unit Tests

**Pattern**: `{ClassName}Test.php`

- Must mirror the class being tested exactly
- One test file per class with 1:1 mapping
- Located in the same subdirectory structure as the source

**Examples:**

```
app/Actions/User/CreateUser.php
→ tests/Unit/Actions/User/CreateUserTest.php

app/Actions/User/UpdateUserProfile.php
→ tests/Unit/Actions/User/UpdateUserProfileTest.php

app/Http/Requests/Auth/RegisterRequest.php
→ tests/Unit/Requests/Auth/RegisterRequestTest.php
```

#### Feature Tests

**Pattern**: `{Domain/Feature}Test.php`

- Domain or behavior-focused, NOT controller-focused
- Describes what users can do, not implementation details
- Can cover multiple controllers if they serve the same domain

**Examples:**
✅ **Good (Domain-focused):**

- `AuthenticationTest.php` - Covers login/logout functionality
- `RegistrationTest.php` - Covers user registration
- `AccountDeletionTest.php` - Covers account deletion
- `ProfileUpdateTest.php` - Covers profile updates
- `PasswordResetTest.php` - Covers password reset flow

❌ **Avoid (Implementation-focused):**

- `LoginControllerTest.php` - Testing implementation, not behavior
- `UserControllerTest.php` - Too broad and implementation-focused
- `SettingsTest.php` - Too generic

**Why domain-focused?**

- Tests remain stable when refactoring controllers
- Focuses on user stories and business value
- Easier to understand what functionality is covered
- Multiple controllers serving same domain can share one test file

## Testing with Pest PHP

### Test Requirements

- **All code must be tested**
- Generate `{Model}Factory` with each model
- **Don't remove tests** without approval
- Use descriptive test names and arrange-act-assert pattern

### Basic Test Structure

```php
<?php

use App\Models\User;
use App\Models\Todo;

it('creates a todo for authenticated user', function () {
    // Arrange
    $user = User::factory()->create();
    $data = [
        'title' => 'Test Todo',
        'description' => 'Test Description',
        'priority' => 'medium'
    ];

    // Act
    $response = $this->actingAs($user)
        ->post(route('todos.store'), $data);

    // Assert
    $response->assertRedirect(route('todos.index'));
    expect($user->fresh()->todos)->toHaveCount(1);
    expect($user->todos->first())
        ->title->toBe('Test Todo')
        ->priority->toBe('medium');
});
```

### Feature Testing Patterns

```php
<?php

use App\Models\User;
use App\Models\Todo;

describe('Todo Management', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('can list todos', function () {
        $todos = Todo::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->get(route('todos.index'));

        $response->assertOk();
        $response->assertInertia(fn($page) => $page
            ->component('todos/index')
            ->has('todos', 3)
            ->where('todos.0.title', $todos[0]->title)
        );
    });

    it('can create a todo', function () {
        $data = [
            'title' => 'New Todo',
            'description' => 'Todo description',
            'priority' => 'high'
        ];

        $response = $this->post(route('todos.store'), $data);

        $response->assertRedirect(route('todos.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('todos', [
            'user_id' => $this->user->id,
            'title' => 'New Todo',
            'priority' => 'high'
        ]);
    });

    it('validates required fields', function () {
        $response = $this->post(route('todos.store'), []);

        $response->assertSessionHasErrors(['title']);
    });

    it('prevents access to other users todos', function () {
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->get(route('todos.show', $todo));

        $response->assertForbidden();
    });
});
```

### Unit Testing Patterns

```php
<?php

use App\Models\User;
use App\Models\Todo;
use App\Actions\CreateTodoAction;

describe('CreateTodoAction', function () {
    it('creates a todo for a user', function () {
        $user = User::factory()->create();
        $action = new CreateTodoAction();
        $data = [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'priority' => 'medium'
        ];

        $todo = $action->handle($user, $data);

        expect($todo)->toBeInstanceOf(Todo::class);
        expect($todo->user_id)->toBe($user->id);
        expect($todo->title)->toBe('Test Todo');
        expect($todo->priority)->toBe('medium');
    });

    it('handles optional fields', function () {
        $user = User::factory()->create();
        $action = new CreateTodoAction();
        $data = ['title' => 'Test Todo'];

        $todo = $action->handle($user, $data);

        expect($todo->description)->toBeNull();
        expect($todo->priority)->toBe('medium'); // default value
    });
});
```

### Model Testing

```php
<?php

use App\Models\User;
use App\Models\Todo;
use Carbon\Carbon;

describe('Todo Model', function () {
    it('belongs to a user', function () {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        expect($todo->user)->toBeInstanceOf(User::class);
        expect($todo->user->id)->toBe($user->id);
    });

    it('casts due_date to datetime', function () {
        $todo = Todo::factory()->create(['due_date' => '2023-12-25 10:00:00']);

        expect($todo->due_date)->toBeInstanceOf(Carbon::class);
    });

    it('can check if todo is overdue', function () {
        $overdueTodo = Todo::factory()->create([
            'due_date' => Carbon::yesterday(),
            'completed' => false
        ]);

        $futureTodo = Todo::factory()->create([
            'due_date' => Carbon::tomorrow(),
            'completed' => false
        ]);

        $completedTodo = Todo::factory()->create([
            'due_date' => Carbon::yesterday(),
            'completed' => true
        ]);

        expect($overdueTodo->isOverdue())->toBeTrue();
        expect($futureTodo->isOverdue())->toBeFalse();
        expect($completedTodo->isOverdue())->toBeFalse();
    });
});
```

### Request Validation Testing

```php
<?php

use App\Http\Requests\CreateTodoRequest;
use Illuminate\Support\Facades\Validator;

describe('CreateTodoRequest', function () {
    it('validates required title', function () {
        $request = new CreateTodoRequest();
        $validator = Validator::make([], $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('title'))->toBeTrue();
    });

    it('validates title length', function () {
        $request = new CreateTodoRequest();
        $data = ['title' => str_repeat('a', 256)]; // Too long
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('title'))->toBeTrue();
    });

    it('validates priority enum', function () {
        $request = new CreateTodoRequest();

        // Valid priority
        $validData = ['title' => 'Test', 'priority' => 'high'];
        $validValidator = Validator::make($validData, $request->rules());
        expect($validValidator->passes())->toBeTrue();

        // Invalid priority
        $invalidData = ['title' => 'Test', 'priority' => 'invalid'];
        $invalidValidator = Validator::make($invalidData, $request->rules());
        expect($invalidValidator->fails())->toBeTrue();
    });
});
```

## Database Testing

### Factory Usage

```php
<?php

// tests/Unit/Models/UserTest.php
use App\Models\User;
use App\Models\Todo;

it('can have many todos', function () {
    $user = User::factory()
        ->has(Todo::factory()->count(5)->completed())
        ->has(Todo::factory()->count(3)->pending())
        ->create();

    expect($user->todos)->toHaveCount(8);
    expect($user->todos()->completed()->get())->toHaveCount(5);
    expect($user->todos()->pending()->get())->toHaveCount(3);
});

it('can create user with specific traits', function () {
    $user = User::factory()
        ->verified()
        ->withTodos(10)
        ->create();

    expect($user->email_verified_at)->not->toBeNull();
    expect($user->todos)->toHaveCount(10);
});
```

### Database Transaction Testing

```php
<?php

use Illuminate\Support\Facades\DB;

it('rolls back transaction on failure', function () {
    $user = User::factory()->create();
    $initialCount = Todo::count();

    expect(fn() => DB::transaction(function () use ($user) {
        Todo::factory()->create(['user_id' => $user->id]);
        throw new Exception('Simulated failure');
    }))->toThrow(Exception::class);

    expect(Todo::count())->toBe($initialCount);
});
```

## Testing Utilities & Helpers

### Custom Assertions

```php
<?php

// tests/Pest.php
use App\Models\Todo;
use App\Models\User;

expect()->extend('toBeOverdue', function () {
    return $this->toBeInstanceOf(Todo::class)
        ->and($this->value->isOverdue())->toBeTrue();
});

expect()->extend('toHaveTodos', function (int $count) {
    return $this->toBeInstanceOf(User::class)
        ->and($this->value->todos->count())->toBe($count);
});

// Usage in tests
it('marks todo as overdue', function () {
    $todo = Todo::factory()->overdue()->create();

    expect($todo)->toBeOverdue();
});
```

### Test Data Builders

```php
<?php

// tests/Support/Builders/TodoBuilder.php
namespace Tests\Support\Builders;

use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;

class TodoBuilder
{
    private array $attributes = [];

    public function forUser(User $user): self
    {
        $this->attributes['user_id'] = $user->id;
        return $this;
    }

    public function withTitle(string $title): self
    {
        $this->attributes['title'] = $title;
        return $this;
    }

    public function overdue(): self
    {
        $this->attributes['due_date'] = Carbon::yesterday();
        $this->attributes['completed'] = false;
        return $this;
    }

    public function highPriority(): self
    {
        $this->attributes['priority'] = 'high';
        return $this;
    }

    public function create(): Todo
    {
        return Todo::factory()->create($this->attributes);
    }
}

// Usage in tests
it('handles overdue high priority todos', function () {
    $user = User::factory()->create();
    $todo = (new TodoBuilder())
        ->forUser($user)
        ->withTitle('Urgent Task')
        ->highPriority()
        ->overdue()
        ->create();

    expect($todo->isOverdue())->toBeTrue();
    expect($todo->priority)->toBe('high');
});
```

## Continuous Testing

### Running Tests

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

# Run tests with coverage
vendor/bin/pest --coverage

# Run tests in parallel
vendor/bin/pest --parallel

# Watch mode for development
vendor/bin/pest --watch
```

### Test Configuration

```php
<?php

// tests/Pest.php
uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Unit');

// Custom test helpers
function createUserWithTodos(int $todoCount = 5): User
{
    return User::factory()
        ->has(Todo::factory()->count($todoCount))
        ->create();
}

function assertTodoBelongsToUser(Todo $todo, User $user): void
{
    expect($todo->user_id)->toBe($user->id);
}
```
