# Svelte 5 Guidelines

## Key Principles

- Write concise, accurate, technical code based on modern Svelte 5 (Runes) and TypeScript best practices
- Prioritize performance optimization for optimal user experience
- Use descriptive variable names and follow modern Svelte 5 conventions
- Use functional and declarative programming patterns; avoid unnecessary classes except for state machines
- Prefer iteration and modularization over code duplication
- Keep components focused and reusable

## File Structure & Naming Conventions

### Naming Standards
- Use **kebab-case** for all component files: `user-profile.svelte`
- Use **PascalCase** for component names in imports and usage
- Use **camelCase** for variables, functions, and props
- Use **.svelte.ts** files for global state and complex logic

## State Management & Svelte 5 Runes

### Core Runes Usage

**Use Svelte 5 runes consistently:**

- **`$state`**: Declare reactive state
  ```typescript
  let count = $state(0);
  let items = $state<string[]>([]);
  ```

- **`$derived`**: Compute derived values
  ```typescript
  let doubled = $derived(count * 2);
  let filteredItems = $derived(items.filter(item => item.includes(searchQuery)));
  ```

- **`$effect`**: Manage side effects and lifecycle
  ```typescript
  $effect(() => {
    console.log(`Count is now ${count}`);
  });
  ```

- **`$props`**: Declare component props
  ```typescript
  let { optionalProp = 42, requiredProp } = $props();
  ```

- **`$bindable`**: Create two-way bindable props
  ```typescript
  let { bindableProp = $bindable() } = $props();
  ```

- **`$inspect`**: Debug reactive state (development only)
  ```typescript
  $inspect(count);
  ```

### Comprehensive State Example
```svelte
<script lang="ts">
  import { untrack } from "svelte";

  // Reactive State
  let searchQuery = $state("");
  let isLoading = $state(false);
  let selectedTodos = $state<number[]>([]);

  // Derived Values
  let filteredTodos = $derived(
    todos.filter((todo) =>
      todo.title.toLowerCase().includes(searchQuery.toLowerCase())
    )
  );

  let selectedCount = $derived(selectedTodos.length);

  // Effects for Side Effects
  $effect(() => {
    // Auto-save search query to localStorage
    if (searchQuery) {
      localStorage.setItem("todoSearch", searchQuery);
    }
  });

  // Props with Destructuring
  interface Props {
    todos: Todo[];
    onUpdate?: (todo: Todo) => void;
    className?: string;
  }

  let { todos, onUpdate = () => {}, className = "" }: Props = $props();

  // Bindable Props for Two-way Data Flow
  let { selectedItems = $bindable([]), searchValue = $bindable("") } = $props();

  // Advanced effect dependency tracking
  let a = $state(1);
  let b = $state(2);

  // Effect with explicit dependency control
  $effect(() => {
    // This effect only runs when 'a' changes, even though 'b' is referenced
    const valueA = a;
    console.log("A changed:", valueA);

    // Use untrack to prevent 'b' from being a dependency
    untrack(() => {
      console.log("B value (not tracked):", b);
    });
  });

  // Batched state updates for performance
  const updateMultipleValues = () => {
    // All updates happen in a single reactive cycle
    a = 10;
    b = 20;
  };
</script>
```

## State Management Scaling Strategy

### 1. Local Component State (Default)
For state confined to a single component, use `$state` directly within the `<script>` block:

```svelte
<script lang="ts">
  let count = $state(0);
  let doubled = $derived(count * 2);

  $effect(() => {
    console.log(`The count is now ${count}`);
  });
</script>
```

### 2. Complex Component State (State Machines)
For components with multiple, interdependent states, use a **class-based state machine**:

```svelte
<script lang="ts">
  import { Button } from "@/components/ui/button";

  // Types
  type Status = "idle" | "uploading" | "success" | "error";

  // State Machine
  class Uploader {
    status = $state<Status>("idle");
    data = $state<string | null>(null);
    error = $state<Error | null>(null);

    async upload() {
      this.status = "uploading";
      this.error = null;
      try {
        const response = await fetch("/api/upload");
        if (!response.ok) throw new Error("Network response was not ok");
        this.data = await response.text();
        this.status = "success";
      } catch (e) {
        this.error = e as Error;
        this.status = "error";
      }
    }
  }

  const uploader = new Uploader();
</script>

{#if uploader.status === "uploading"}
  <p>Uploading...</p>
{:else if uploader.status === "success"}
  <p>{uploader.data}</p>
{:else if uploader.status === "error"}
  <p class="text-destructive">{uploader.error.message}</p>
{/if}

<Button onclick={() => uploader.upload()}>Fetch Data</Button>
```

### 2. Shared/Global State
For state shared across multiple components, create a class-based state machine in `.svelte.ts` files:

```typescript
// @/lib/state/counter.svelte.ts
class Counter {
  count = $state(0);
  incrementor = $state(1);

  increment() {
    this.count += this.incrementor;
  }

  resetCount() {
    this.count = 0;
  }

  resetIncrementor() {
    this.incrementor = 1;
  }
}

export const counter = new Counter();
```

Usage in components:
```svelte
<script lang="ts">
  import { counter } from "@/lib/state/counter.svelte.ts";
</script>

<button onclick={() => counter.increment()}>
  Count: {counter.count}
</button>
```

## Component Patterns

### Props Interface Definition
Always define TypeScript interfaces for component props:

```svelte
<script lang="ts">
  import type { Todo, User } from "@/types";

  interface Props {
    todo: Todo;
    user: User;
    onUpdate?: (todo: Todo) => void;
    onDelete?: (id: number) => void;
    className?: string;
    disabled?: boolean;
  }

  let {
    todo,
    user,
    onUpdate = () => {},
    onDelete = () => {},
    className = "",
    disabled = false
  }: Props = $props();
</script>
```

### Event Handling Patterns
```svelte
<script lang="ts">
  import type { Todo } from "@/types";

  interface Props {
    todo: Todo;
    onToggle?: (todo: Todo) => void;
  }

  let { todo, onToggle }: Props = $props();

  const handleToggle = () => {
    const updatedTodo = { ...todo, completed: !todo.completed };
    onToggle?.(updatedTodo);
  };
</script>

<button
  onclick={handleToggle}
  class="flex items-center space-x-2"
  aria-pressed={todo.completed}
>
  {todo.completed ? "✓" : "○"} {todo.title}
</button>
```

### Conditional Rendering
```svelte
<script lang="ts">
  let status = $state<"loading" | "success" | "error">("loading");
  let data = $state<string | null>(null);
  let error = $state<string | null>(null);
</script>

{#if status === "loading"}
  <div class="flex items-center space-x-2">
    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
    <span>Loading...</span>
  </div>
{:else if status === "error"}
  <div class="text-destructive">
    <p>Error: {error}</p>
    <button onclick={() => refetch()}>Try Again</button>
  </div>
{:else if status === "success" && data}
  <div class="text-green-600">
    <p>{data}</p>
  </div>
{:else}
  <p>No data available</p>
{/if}
```

## Performance Optimization

### Derived State Optimization
```svelte
<script lang="ts">
  let items = $state<Item[]>([]);
  let filter = $state("");

  // Expensive computation is memoized
  let filteredItems = $derived(
    items.filter(item => {
      // This only re-runs when items or filter changes
      return item.name.toLowerCase().includes(filter.toLowerCase());
    })
  );

  // For complex derivations, use $derived.by()
  let statistics = $derived.by(() => {
    const filtered = filteredItems;
    return {
      total: filtered.length,
      completed: filtered.filter(item => item.completed).length,
      pending: filtered.filter(item => !item.completed).length
    };
  });
</script>
```

### Effect Granularity
```svelte
<script lang="ts">
  let a = $state(1);
  let b = $state(2);

  // Use $effect.pre() for DOM reads before updates
  $effect.pre(() => {
    const element = document.getElementById('my-element');
    if (element) {
      // Capture current scroll position before DOM updates
      const scrollTop = element.scrollTop;
      // Store for later use
    }
  });

  // Use $effect.tracking() for complex dependency control
  $effect.tracking(() => {
    const valueA = a;
    console.log("A changed:", valueA);

    // Use untrack to prevent 'b' from being a dependency
    untrack(() => {
      console.log("B value (not tracked):", b);
    });
  });
</script>
```