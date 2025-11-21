# Inertia.js 2.0

## Integration Overview

- **Navigation**: Use `router.visit()` for navigation, and the `router` object for all programmatic navigation
- **Links**: Use Inertia's `Link` component for links, with the wayfinder-generated URL parameters
- **Forms**: Use Inertia's `Form` component with Wayfinder controller methods
- **Laravel Wayfinder Integration**: Use Laravel Wayfinder for type-safe, auto-generated route helpers
- **Route Generation**: Wayfinder automatically generates TypeScript functions for all Laravel routes and controller methods
- **Type Safety**: Leverage Wayfinder's TypeScript definitions for compile-time route validation
- **Performance**: Use partial reloads, async requests, deferred props, and prefetching
- **PageProps Access**: Use the Inertia `page` store to access server-side props instead of component props

## Navigation Patterns

### Link Navigation

```svelte
<script lang="ts">
  import { Link } from '@inertiajs/svelte';

  import { show } from '@/actions/App/Http/Controllers/TodoController';

  interface Props {
    todo: Todo;
  }

  let { todo }: Props = $props();
</script>

<!-- Basic Link with Wayfinder integration -->
<Link href={show(todo.id)} class="text-primary hover:underline">
  {todo.title}
</Link>

<!-- Link with partial reload -->
<Link href={show(todo.id)} only={['todo', 'comments']} preserveScroll>
  View Todo
</Link>

<!-- Link with different HTTP methods -->
<Link href={show(todo.id)} method="delete" data={{ _token: 'csrf-token' }}>
  Delete Todo
</Link>

<!-- Link with custom headers -->
<Link href={show(todo.id)} headers={{ 'X-Custom': 'header-value' }}>
  Custom Request
</Link>

<!-- Link with prefetching -->
<Link href={show(todo.id)} prefetch="hover" cacheFor="30s">
  Quick Access Todo
</Link>
```

### Programmatic Navigation

```svelte
<script lang="ts">
  import { router } from '@inertiajs/svelte';

  import { index, show } from '@/actions/App/Http/Controllers/TodoController';
  import { dashboard } from '@/routes';

  // Using Wayfinder for type-safe navigation with query parameters
  const navigateToTodos = () => {
    router.get(
      index({ query: { status: 'completed' } }),
      {},
      {
        preserveState: true,
        only: ['todos'], // Only fetch the 'todos' prop
      },
    );
  };

  // Navigate to dashboard using Wayfinder
  const goToDashboard = () => {
    router.visit(dashboard());
  };

  // Prefetch a specific todo page
  const prefetchTodo = (id: number) => {
    router.prefetch(show(id));
  };

  // Using query parameters with Wayfinder
  const getTodoUrl = (id: number) => {
    return show(id, {
      query: {
        include: 'comments',
        per_page: 10,
      },
    }); // "/todos/1?include=comments&per_page=10"
  };

  // Advanced router options
  const navigateWithOptions = () => {
    router.visit(show(1), {
      method: 'get',
      preserveState: true,
      preserveScroll: true,
      only: ['todo'],
      showProgress: false,
      async: true,
      onBefore: (visit) => {
        console.log('Navigation starting:', visit);
        return true; // Return false to cancel
      },
      onStart: (visit) => {
        console.log('Request started:', visit);
      },
      onProgress: (progress) => {
        console.log('Upload progress:', progress);
      },
      onSuccess: (page) => {
        console.log('Navigation successful:', page);
      },
      onError: (errors) => {
        console.error('Navigation failed:', errors);
      },
      onFinish: (visit) => {
        console.log('Navigation finished:', visit);
      },
    });
  };

  // Router event listeners
  $effect(() => {
    const unsubscribeBefore = router.on('before', (event) => {
      console.log('Before visit:', event.detail);
    });

    const unsubscribeStart = router.on('start', (event) => {
      console.log('Visit started:', event.detail);
    });

    const unsubscribePrefetching = router.on('prefetching', (event) => {
      console.log('Prefetching:', event.detail.page.url);
    });

    const unsubscribePrefetched = router.on('prefetched', (event) => {
      console.log('Prefetched:', event.detail.page.url);
    });

    // Cleanup listeners
    return () => {
      unsubscribeBefore();
      unsubscribeStart();
      unsubscribePrefetching();
      unsubscribePrefetched();
    };
  });
</script>
```

## Type Definitions & PageProps

### PageProps Interface

The base PageProps type provides standard props available to all Inertia pages:

```typescript
// resources/js/types/index.d.ts
export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
  name: string;
  auth: Auth;
  flash: {
    message: string;
  };
  [key: string]: unknown;
};

export interface Auth {
  user: User;
}

// define other types and interfaces here...
```

### Accessing PageProps via Page Store

Access server-side data through the Inertia page store instead of component props:

```typescript
import { page } from '@inertiajs/svelte';

// Access page store with reactive derivation
const user = $derived($page.props.auth.user);
```

### Component Props Pattern

Define component-specific props separately from PageProps:

```svelte
<script lang="ts">
  import { page } from '@inertiajs/svelte';

  // Component props are typed separately
  interface Props {
    status?: string;
  }

  const { status }: Props = $props();

  // Server data accessed via page store with reactive derivation
  const user = $derived($page.props.auth.user);
</script>
```

### When to Import Types

**❌ Don't import type when accessing page props:**

```svelte
<script lang="ts">
  // ❌ Unnecessary import
  import type { User } from '@/types';

  import { page } from '@inertiajs/svelte';

  // ❌ Unnecessary type assertion
  const user = $page.props.auth.user as User;
</script>
```

**✅ Do import type for component props:**

```svelte
<script lang="ts">
  // ✅ Import needed for prop typing
  import type { User } from '@/types';

  interface Props {
    user: User; // Type import required here
    showEmail?: boolean;
  }

  const { user, showEmail = false }: Props = $props();
</script>
```

### Core Shared Types

```typescript
// resources/js/types/index.d.ts
export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: any;
  isActive?: boolean;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}
```

### Global Type Declarations

```typescript
// resources/js/types/global.d.ts
declare global {
  interface Window {
    axios: AxiosInstance;
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}
```

## Wayfinder Setup & Usage

### Route Generation

```bash
# Generate TypeScript route definitions
php artisan wayfinder:generate

# Generate with selective options
php artisan wayfinder:generate --skip-actions
php artisan wayfinder:generate --skip-routes
```

### Basic Controller Method Usage

```typescript
// Import Wayfinder-generated controller methods
import {
  index,
  show,
  store,
  update,
} from '@/actions/App/Http/Controllers/TodoController';

// Get URL and method
store(); // { url: "/todos", method: "post" }
show(1); // { url: "/todos/1", method: "get" }

// Get URL only
store.url(); // "/todos"
show.url(1); // "/todos/1"

// Different HTTP methods
show.get(1); // { url: "/todos/1", method: "get" }
show.head(1); // { url: "/todos/1", method: "head" }
```

### Route Parameter Handling

```typescript
// Single parameter
show(1);
show({ id: 1 });

// Multiple parameters
update([1, 2]); // For routes like /todos/{todo}/comments/{comment}
update({ todo: 1, comment: 2 });

// Named parameter bindings
show({ slug: 'my-todo' }); // For routes like /todos/{todo:slug}
```

### Query Parameters

```typescript
// Add query parameters
show(1, { query: { include: 'comments' } }); // "/todos/1?include=comments"

// Merge with existing URL parameters
show(1, { mergeQuery: { page: 2 } }); // Merges with current URL query

// Remove parameters by setting to null
show(1, { mergeQuery: { filter: null } }); // Removes 'filter' from URL
```

## Form Handling

### Form Component with Wayfinder Integration

```svelte
<script lang="ts">
  import type { Todo } from '@/types';

  import { Form } from '@inertiajs/svelte';

  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';

  // Import Wayfinder-generated controller methods
  import { store, update } from '@/actions/App/Http/Controllers/TodoController';

  interface Props {
    todo?: Todo;
  }

  let { todo }: Props = $props();
  let isEditing = !!todo;
</script>

<!-- Direct Wayfinder integration with Form component -->
<Form
  action={isEditing ? update(todo.id) : store()}
  resetOnSuccess={['title', 'description']}
  preserveScroll
  onSuccess={() => {
    console.log(isEditing ? 'Todo updated' : 'Todo created');
  }}
>
  {#snippet children({ errors, processing, resetAndClearErrors })}
    <div class="space-y-4">
      <div>
        <Label for="title">Title</Label>
        <Input
          id="title"
          name="title"
          type="text"
          value={todo?.title || ''}
          placeholder="My new todo"
          required
        />
        <InputError message={errors.title} />
      </div>

      <div>
        <Label for="description">Description</Label>
        <Input
          id="description"
          name="description"
          type="text"
          value={todo?.description || ''}
          placeholder="Description"
        />
        <InputError message={errors.description} />
      </div>

      <div class="flex gap-2">
        <Button type="submit" disabled={processing}>
          {processing
            ? isEditing
              ? 'Updating...'
              : 'Creating...'
            : isEditing
              ? 'Update Todo'
              : 'Create Todo'}
        </Button>

        <Button type="button" variant="secondary" onclick={resetAndClearErrors}>
          Reset
        </Button>
      </div>
    </div>
  {/snippet}
</Form>
```

## Performance Optimization

### Partial Reloads

```svelte
<script lang="ts">
  import { router } from '@inertiajs/svelte';

  // Only reload specific props
  const refreshTodos = () => {
    router.reload({
      only: ['todos'],
      preserveScroll: true,
    });
  };

  // Reload everything except certain props
  const refreshPage = () => {
    router.reload({
      except: ['user', 'navigation'],
    });
  };
</script>
```

### Async Requests

```svelte
<script lang="ts">
  import { router } from '@inertiajs/svelte';

  import { update } from '@/actions/App/Http/Controllers/TodoController';

  const toggleTodo = async (todo: Todo) => {
    // Non-blocking async request
    await router.patch(
      update(todo.id),
      { completed: !todo.completed },
      {
        async: true,
        preserveScroll: true,
        showProgress: false, // Disable loading indicator
        onSuccess: () => {
          console.log('Todo updated silently');
        },
      },
    );
  };

  // Async with progress indicator
  const updateWithProgress = async (todo: Todo) => {
    await router.patch(
      update(todo.id),
      { title: 'Updated title' },
      {
        async: true,
        showProgress: true, // Keep loading indicator
        preserveScroll: true,
      },
    );
  };

  // Multiple async requests with #await tracking
  let batchUpdatePromise = $state();

  const batchUpdate = (todos: Todo[]) => {
    const updates = todos.map((todo) =>
      router.patch(
        update(todo.id),
        { completed: true },
        { async: true, showProgress: false },
      ),
    );

    batchUpdatePromise = Promise.all(updates);
  };
</script>

<!-- Batch update with #await block -->
<button onclick={() => batchUpdate(todos)}>Update All Todos</button>

{#await batchUpdatePromise}
  <div class="progress">Updating {todos.length} todos...</div>
{:then results}
  <div class="success">✓ All {results.length} todos updated successfully</div>
{:catch error}
  <div class="error">Batch update failed: {error.message}</div>
{/await}
```

### Deferred Props (Server-Side)

```php
<?php

// In Laravel controller - defer non-critical data
return inertia('todos/index', [
    'todos' => $todos, // Critical - loaded immediately

    // Simple deferred props
    'statistics' => Inertia::defer(fn() => $this->getStatistics()),
    'recommendations' => Inertia::defer(fn() => $this->getRecommendations()),

    // Grouped deferred props (loaded together)
    'teams' => Inertia::defer(fn() => Team::all(), 'attributes'),
    'projects' => Inertia::defer(fn() => Project::all(), 'attributes'),
    'settings' => Inertia::defer(fn() => Setting::all(), 'attributes'),

    // Lazy props (only loaded when explicitly requested)
    'users' => Inertia::lazy(fn() => User::all()),
    'reports' => Inertia::lazy(fn() => $this->generateReports()),
]);

// Alternative: Using closures for lazy evaluation
return inertia('todos/index', [
    'todos' => $todos,
    'users' => fn() => User::all(), // Only loaded when requested
    'companies' => fn() => Company::all(),
]);
```

### Deferred Props (Client-Side)

```svelte
<script lang="ts">
  import { Deferred } from '@inertiajs/svelte';

  // Props passed from server
  let { todos, comments } = $props(); // Available immediately
  let { permissions } = $props(); // Deferred prop
</script>

<!-- Wait for single deferred prop -->
<Deferred data="permissions">
  {#snippet fallback()}
    <div class="animate-pulse">Loading permissions...</div>
  {/snippet}

  <!-- Permissions are now loaded -->
  {#each permissions as permission}
    <div>{permission.name}</div>
  {/each}
</Deferred>

<!-- Wait for multiple deferred props -->
<Deferred data={['teams', 'projects']}>
  {#snippet fallback()}
    <div class="space-y-4">
      <div class="h-4 animate-pulse rounded bg-muted"></div>
      <div class="h-4 animate-pulse rounded bg-muted"></div>
    </div>
  {/snippet}

  <!-- All deferred data is now available -->
  <div>Teams and projects loaded</div>
</Deferred>
```

### Polling

```svelte
<script lang="ts">
  import { usePoll } from '@inertiajs/svelte';

  // Basic automatic polling every 2 seconds
  usePoll(2000);

  // Polling with manual control
  const { start, stop } = usePoll(
    2000,
    {},
    {
      autoStart: false, // Don't start immediately
      keepAlive: true, // Continue polling when tab is in background
    },
  );

  // Polling with callbacks
  usePoll(5000, {
    onStart() {
      console.log('Polling started');
    },
    onFinish() {
      console.log('Polling finished');
    },
    onError(errors) {
      console.log('Polling failed:', errors);
    },
  });
</script>

<button onclick={start}>Start Polling</button>
<button onclick={stop}>Stop Polling</button>
```

### Prefetching

```svelte
<script lang="ts">
  import { Link, usePrefetch } from '@inertiajs/svelte';

  import { show } from '@/actions/App/Http/Controllers/TodoController';
</script>

<!-- Basic hover prefetching -->
<Link href={show(1)} prefetch>View Todo</Link>

<!-- Prefetch on component mount -->
<Link href={show(2)} prefetch="mount">Important Todo</Link>

<!-- Prefetch on click/mousedown -->
<Link href={show(3)} prefetch="click">Action Todo</Link>

<!-- Multiple prefetch strategies -->
<Link href={show(4)} prefetch={['mount', 'hover']}>Critical Todo</Link>

<!-- Custom cache duration -->
<Link href={show(5)} prefetch cacheFor="1m">Cached Todo</Link>

<!-- Stale-while-revalidate caching -->
<Link href={show(6)} prefetch cacheFor={['30s', '1m']}>SWR Todo</Link>
```

#### Programmatic Prefetching with usePrefetch

```svelte
<script lang="ts">
  import { router, usePrefetch } from '@inertiajs/svelte';

  import { show } from '@/actions/App/Http/Controllers/TodoController';

  // Using the usePrefetch helper
  const { lastUpdatedAt, isPrefetching, isPrefetched, flush } = usePrefetch(
    show(1),
    { method: 'get', data: { include: 'comments' } },
    { cacheFor: '1m' },
  );

  // Direct router prefetching
  const prefetchTodo = (id: number) => {
    router.prefetch(
      show(id),
      { method: 'get', data: { include: 'comments' } },
      { cacheFor: '1m' },
    );
  };

  // Flush specific prefetch cache
  const clearCache = () => {
    flush(); // Clear specific prefetch
    router.flushAll(); // Clear all prefetch cache
  };
</script>

<!-- Display prefetch status -->
{#if isPrefetching}
  <span>Prefetching...</span>
{:else if isPrefetched}
  <span>Ready (cached {lastUpdatedAt})</span>
{/if}
```

### Lazy Loading with WhenVisible

```svelte
<script lang="ts">
  import { WhenVisible } from '@inertiajs/svelte';

  let { products } = $props(); // Deferred prop
</script>

<!-- Basic visibility-based loading -->
<WhenVisible data="products">
  {#snippet fallback()}
    <div class="h-64 animate-pulse rounded bg-muted"></div>
  {/snippet}

  {#each products as product}
    <ProductCard {product} />
  {/each}
</WhenVisible>

<!-- Load with buffer (500px before visible) -->
<WhenVisible data="products" buffer={500}>
  {#snippet fallback()}
    <div>Loading products...</div>
  {/snippet}

  <!-- Product content -->
</WhenVisible>

<!-- Always reload when visible (for infinite scroll) -->
<WhenVisible data="moreProducts" always>
  {#each moreProducts as product}
    <ProductCard {product} />
  {/each}
</WhenVisible>
```

## Advanced Patterns

### State Preservation

```svelte
<script lang="ts">
  import { router } from '@inertiajs/svelte';

  const navigateWithStatePreservation = () => {
    router.visit('/todos', {
      preserveState: true, // Keep current component state
      preserveScroll: true, // Keep scroll position
      only: ['todos'], // Only reload specific props
    });
  };
</script>
```
