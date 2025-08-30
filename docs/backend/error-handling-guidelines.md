# Error Handling

## Backend Error Handling

### Laravel Exception Handling
- Use custom exceptions for business logic errors
- Implement global exception handling in `app/Exceptions/Handler.php`
- Log errors appropriately based on severity
- Use Laravel 12 improved error handling features

### Custom Business Exceptions
```php
<?php

namespace App\Exceptions;

use Exception;

class TodoNotFoundException extends Exception
{
    public function __construct(int $todoId)
    {
        parent::__construct("Todo with ID {$todoId} not found");
    }

    public function render($request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error_code' => 'TODO_NOT_FOUND'
            ], 404);
        }

        return inertia('errors/404', [
            'message' => 'The todo you are looking for does not exist.'
        ])->toResponse($request)->setStatusCode(404);
    }
}

class InsufficientPermissionsException extends Exception
{
    public function __construct(string $action = '')
    {
        $message = $action
            ? "Insufficient permissions to {$action}"
            : "Insufficient permissions";
        parent::__construct($message);
    }

    public function render($request)
    {
        return inertia('errors/403', [
            'message' => $this->getMessage()
        ])->toResponse($request)->setStatusCode(403);
    }
}
```

### Global Exception Handler
```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        TodoNotFoundException::class,
        InsufficientPermissionsException::class,
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Add context to error reporting
            logger()->error('Application error', [
                'exception' => $e,
                'user_id' => auth()->id(),
                'url' => request()->url(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }

    public function render($request, Throwable $e)
    {
        // Handle validation exceptions for Inertia
        if ($e instanceof ValidationException && $request->header('X-Inertia')) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Handle 404 errors
        if ($e instanceof NotFoundHttpException) {
            return inertia('errors/404', [
                'message' => 'The page you are looking for does not exist.'
            ])->toResponse($request)->setStatusCode(404);
        }

        // Handle 500 errors in production
        if (app()->environment('production') && $this->shouldReport($e)) {
            return inertia('errors/500', [
                'message' => 'An unexpected error occurred. Please try again.'
            ])->toResponse($request)->setStatusCode(500);
        }

        return parent::render($request, $e);
    }
}
```

### Action Error Handling
```php
<?php

namespace App\Actions;

use App\Models\Todo;
use App\Models\User;
use App\Exceptions\TodoNotFoundException;
use App\Exceptions\InsufficientPermissionsException;
use Illuminate\Support\Facades\DB;

class UpdateTodoAction
{
    public function handle(User $user, int $todoId, array $data): Todo
    {
        $todo = $user->todos()->find($todoId);

        if (!$todo) {
            throw new TodoNotFoundException($todoId);
        }

        if (!$user->can('update', $todo)) {
            throw new InsufficientPermissionsException('update this todo');
        }

        return DB::transaction(function () use ($todo, $data) {
            $todo->update($data);

            // Log the action
            logger()->info('Todo updated', [
                'todo_id' => $todo->id,
                'user_id' => $todo->user_id,
                'changes' => $todo->getChanges()
            ]);

            return $todo->fresh();
        });
    }
}
```

## Frontend Error Handling

### Form Errors
Display validation errors from Laravel using the `errors` object provided by Inertia.js's `Form` component:

```svelte
<script lang="ts">
  import { Form } from "@inertiajs/svelte";
  import { Input } from "@/components/ui/input";
  import { Label } from "@/components/ui/label";
  import { Button } from "@/components/ui/button";
  import InputError from "@/components/input-error.svelte";
  import { store } from "@/actions/App/Http/Controllers/TodoController";
</script>

<Form
  method="post"
  action={store().url}
  onError={(errors) => {
    // Handle form-specific errors
    console.log('Form errors:', errors);
  }}
>
  {#snippet children({ errors, processing })}
    <div class="space-y-4">
      <div>
        <Label for="title">Title</Label>
        <Input
          type="text"
          id="title"
          name="title"
          aria-invalid={errors.title ? "true" : undefined}
          aria-describedby={errors.title ? "title-error" : undefined}
        />
        <InputError id="title-error" message={errors.title} />
      </div>

      <div>
        <Label for="description">Description</Label>
        <Input
          type="text"
          id="description"
          name="description"
          aria-invalid={errors.description ? "true" : undefined}
          aria-describedby={errors.description ? "description-error" : undefined}
        />
        <InputError id="description-error" message={errors.description} />
      </div>

      <Button type="submit" disabled={processing}>
        {processing ? "Creating..." : "Create Todo"}
      </Button>
    </div>
  {/snippet}
</Form>
```

### InputError Component
```svelte
<!-- @/components/input-error.svelte -->
<script lang="ts">
  interface Props {
    message?: string;
    id?: string;
  }

  const { message, id }: Props = $props();
</script>

{#if message}
  <p {id} class="mt-1 text-sm text-destructive" aria-live="polite">
    {message}
  </p>
{/if}
```