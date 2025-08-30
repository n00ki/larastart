# Database Guidelines

## Eloquent Best Practices

### Query Building
- **Never use `DB::`** - always use `Model::query()`
- Prefer Eloquent relationships over manual joins
- Use eager loading to prevent N+1 queries
- Implement proper database transactions for multi-step operations

```php
// ✅ Correct - Using Eloquent
User::query()
    ->with(['todos' => fn($query) => $query->where('completed', false)])
    ->where('active', true)
    ->get();

// ❌ Incorrect - Using DB facade
DB::table('users')
    ->join('todos', 'users.id', '=', 'todos.user_id')
    ->where('users.active', true)
    ->get();
```

### Relationship Definitions
```php
<?php

// User model
class User extends Model
{
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function completedTodos(): HasMany
    {
        return $this->hasMany(Todo::class)->where('completed', true);
    }

    public function activeTodos(): HasMany
    {
        return $this->hasMany(Todo::class)->where('completed', false);
    }
}

// Todo model
class Todo extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## Model Configuration

### Casting with Laravel 12
Use the new `Model::casts()` method for dynamic casting:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed' => 'boolean',
            'priority' => Priority::class, // Enum casting
            'metadata' => 'array',
        ];
    }
}
```

### Fillable vs Guarded
- **Avoid `$fillable`** - use explicit assignment or `$guarded = []`
- Use `$guarded = []` for maximum flexibility with mass assignment protection handled in requests

```php
<?php

// ✅ Preferred approach
class Todo extends Model
{
    protected $guarded = [];

    // Handle assignment in Actions with explicit validation
}

// ❌ Avoid this approach
class Todo extends Model
{
    protected $fillable = ['title', 'description', 'due_date'];
}
```

## Migration Standards

### Migration Structure
- Omit `down()` method in new migrations
- Use descriptive column names
- Add proper indexes for performance
- Include foreign key constraints

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
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'completed']);
            $table->index('due_date');
            $table->index('priority');
        });
    }
};
```

### Index Strategy
```php
// Single column indexes
$table->index('email');
$table->index('created_at');

// Composite indexes (order matters)
$table->index(['user_id', 'status', 'created_at']);

// Unique constraints
$table->unique(['user_id', 'slug']);

// Foreign key constraints
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

## Query Optimization

### Eager Loading Patterns
```php
// Load related models to prevent N+1
$users = User::query()
    ->with(['todos', 'profile'])
    ->get();

// Conditional eager loading
$users = User::query()
    ->with([
        'todos' => fn($query) => $query->where('completed', false),
        'profile:id,user_id,avatar'
    ])
    ->get();

// Count relationships without loading
$users = User::query()
    ->withCount(['todos', 'completedTodos'])
    ->get();
```

### Query Scopes
```php
<?php

// Model scopes for reusable query logic
class Todo extends Model
{
    public function scopeCompleted(Builder $query): void
    {
        $query->where('completed', true);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('completed', false);
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->where('due_date', '<', now())
              ->where('completed', false);
    }

    public function scopeForUser(Builder $query, User $user): void
    {
        $query->where('user_id', $user->id);
    }
}

// Usage
$overdueTodos = Todo::query()
    ->forUser($user)
    ->overdue()
    ->get();
```

## Database Transactions

### Transaction Patterns
```php
<?php

use Illuminate\Support\Facades\DB;

// Simple transaction
DB::transaction(function () use ($user, $data) {
    $todo = $user->todos()->create($data);
    $todo->attachments()->create($data['attachments']);

    // Update user statistics
    $user->increment('total_todos');
});

// Manual transaction control
DB::beginTransaction();

try {
    $todo = $user->todos()->create($data);
    $this->notificationService->sendCreatedNotification($todo);

    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

## Model Factories

### Factory Definitions
```php
<?php

// TodoFactory
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'completed' => fake()->boolean(20), // 20% completed
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn() => ['completed' => true]);
    }

    public function overdue(): static
    {
        return $this->state(fn() => [
            'due_date' => fake()->dateTimeBetween('-1 week', '-1 day'),
            'completed' => false,
        ]);
    }
}
```

### Factory Usage in Tests
```php
<?php

// Create models with relationships
$user = User::factory()
    ->has(Todo::factory()->count(5)->completed())
    ->has(Todo::factory()->count(3)->overdue())
    ->create();

// Use factory states
$overdueTodos = Todo::factory()
    ->count(10)
    ->overdue()
    ->create();
```

## Performance Considerations

### Query Optimization
- Use `select()` to limit columns when possible
- Implement proper pagination for large datasets
- Use database indexes strategically

### Caching Strategies
```php
<?php

use Illuminate\Support\Facades\Cache;

// Cache expensive queries
$userStats = Cache::remember(
    "user_stats_{$user->id}",
    now()->addHour(),
    fn() => [
        'total_todos' => $user->todos()->count(),
        'completed_todos' => $user->todos()->completed()->count(),
        'overdue_todos' => $user->todos()->overdue()->count(),
    ]
);

// Cache with tags for easier invalidation
Cache::tags(['user', "user_{$user->id}"])
    ->remember('user_dashboard', 3600, fn() => $this->generateDashboardData($user));

// Invalidate related caches
Cache::tags("user_{$user->id}")->flush();
```