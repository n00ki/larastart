# PHP & Laravel Standards

## Language Features

### PHP 8.4 Features
- Use PHP 8.4 features (readonly classes, property hooks, etc.)
- Enable strict types: `declare(strict_types=1);`
- Use typed properties and return types consistently
- Leverage union types, nullable types, and mixed types appropriately

### Code Quality Standards
- Follow `pint.json` coding standards
- Enforce strict types and array shapes via PHPStan level 6
- Use `composer lint:fix` after code changes
- Run `composer run test` before task completion

## Database Interactions

### Eloquent Best Practices
- **Never use `DB::`** - always use `Model::query()`
- Prefer Eloquent relationships over manual joins
- Implement proper database transactions for multi-step operations
- Use the new `Model::casts()` method for dynamic casting in Laravel 12

```php
// ✅ Correct
User::query()->where('active', true)->get();

// ❌ Incorrect
DB::table('users')->where('active', true)->get();
```

### Performance Considerations
- Use eager loading to prevent N+1 queries
- Implement proper database indexing
- Use caching for expensive operations
- Optimize Inertia.js page props (avoid over-fetching)

## Laravel 12 Specific Features

### Model Improvements
- Use the new `Model::casts()` method for dynamic casting
- Leverage Laravel 12's performance improvements
- Utilize graceful encryption key rotation

### Error Handling
- Use Laravel's improved error handling features
- Implement global exception handling in `app/Exceptions/Handler.php`
- Log errors appropriately based on severity

## Security Standards

### Authentication & Authorization
- Use Laravel's built-in authentication
- Implement proper CSRF protection
- Use policies for authorization logic
- Validate all user inputs

### Data Protection
- Never expose sensitive data in Inertia.js props
- Use proper SQL injection prevention
- Implement rate limiting on API endpoints
- Use HTTPS in production
- Utilize Inertia 2.0's history encryption for sensitive data

## Code Style Guidelines

### File Organization
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateTodoAction;
use App\Http\Requests\CreateTodoRequest;
use Illuminate\Http\RedirectResponse;

class TodoController extends Controller
{
    public function store(CreateTodoRequest $request, CreateTodoAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully');
    }
}
```

### Type Declarations
- Always use strict typing with `declare(strict_types=1);`
- Type all method parameters and return types
- Use proper type hints for dependency injection
- Leverage PHP 8.4's improved type system

### Error Handling Patterns
```php
<?php

// Custom exceptions for business logic
class TodoNotFoundException extends Exception
{
    public function __construct(int $todoId)
    {
        parent::__construct("Todo with ID {$todoId} not found");
    }
}

// Proper exception handling in actions
class DeleteTodoAction
{
    public function handle(User $user, int $todoId): void
    {
        $todo = $user->todos()->find($todoId);
        
        if (!$todo) {
            throw new TodoNotFoundException($todoId);
        }
        
        $todo->delete();
    }
}
```

## Laravel Best Practices

### Service Provider Organization
- Keep service providers focused and single-purpose
- Register services properly with dependency injection
- Use proper binding for interfaces

### Configuration Management
- Use environment variables for configuration
- Validate configuration values
- Cache configuration in production

### Queue and Job Management
- Use typed job classes with proper failure handling
- Implement idempotent operations
- Use job batching for related operations
- Handle job failures gracefully