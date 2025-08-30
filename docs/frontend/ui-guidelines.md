# UI & Styling Guidelines

## TailwindCSS v4 Standards

### Core Principles
- Use TailwindCSS v4 exclusively for utility-first styling approach
- Use `shadcn-svelte` components from `@/components/ui` as the primary building blocks for the UI
<!-- - Use the `@/components/icon.svelte` component for all icons -->
- Do not modify `@/components/ui` files directly, as they are managed by the `shadcn-svelte` CLI
- Use the global CSS variables defined in `resources/css/app.css`

### TailwindCSS v4 New Features
Leverage TailwindCSS v4's enhanced features:

- **Text shadows**: Use `text-shadow-*` utilities for enhanced typography
- **Masking**: Use `mask-*` utilities for advanced visual effects
- **Improved gradients**: Use `bg-linear-*`, `bg-radial-*`, and `bg-conic-*` utilities
- **3D transforms**: Use `rotate-x-*`, `rotate-y-*`, `scale-z-*`, and `translate-z-*` utilities
- **Container queries**: Use `@container` and `@*` variants for responsive design
- **Safe alignment**: Use `justify-center-safe` and similar utilities to prevent content overflow
- **Pointer variants**: Use `pointer-fine:` and `pointer-coarse:` for touch-optimized designs
- **Size utilities**: Use the new `size-*` utility instead of `w-*` and `h-*` combinations when appropriate

### Color System
Use the global CSS variables for all colors (avoid hardcoded color values):

```svelte
<!-- ✅ Correct - Using CSS variables -->
<div class="bg-primary text-primary-foreground">
  <p class="text-destructive">Error message</p>
  <button class="border-border bg-secondary">Button</button>
</div>

<!-- ❌ Incorrect - Hardcoded colors -->
<div class="bg-blue-500 text-white">
  <p class="text-red-500">Error message</p>
  <button class="border-gray-300 bg-gray-100">Button</button>
</div>
```

## shadcn-svelte Integration

### Component Usage
Use shadcn-svelte components as building blocks:

```svelte
<script lang="ts">
  import { Button } from "@/components/ui/button";
  import * as Card from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Label } from "@/components/ui/label";
  import { Badge } from "@/components/ui/badge";
</script>

<Card.Root class="w-full max-w-md mx-auto">
  <Card.Header>
    <Card.Title>Todo Item</Card.Title>
    <Card.Description>Manage your task</Card.Description>
  </Card.Header>

  <Card.Content class="space-y-4">
    <div class="space-y-2">
      <Label for="title">Title</Label>
      <Input id="title" placeholder="Enter task title" />
    </div>

    <div class="flex gap-2">
      <Badge variant="secondary">High Priority</Badge>
      <Badge variant="outline">Due Today</Badge>
    </div>
  </Card.Content>

  <Card.Footer class="flex gap-2">
    <Button size="sm" variant="outline">Cancel</Button>
    <Button size="sm">Save</Button>
  </Card.Footer>
</Card.Root>
```

### Component Variants
Understand and use component variants effectively:

```svelte
<script lang="ts">
  import { Button } from "@/components/ui/button";
  import * as Alert from "@/components/ui/alert";
  import { Badge } from "@/components/ui/badge";
</script>

<!-- Button variants -->
<div class="flex gap-2">
  <Button variant="default">Default</Button>
  <Button variant="destructive">Delete</Button>
  <Button variant="outline">Cancel</Button>
  <Button variant="secondary">Secondary</Button>
  <Button variant="ghost">Ghost</Button>
  <Button variant="link">Link</Button>
</div>

<!-- Button sizes -->
<div class="flex gap-2 items-center">
  <Button size="sm">Small</Button>
  <Button size="default">Default</Button>
  <Button size="lg">Large</Button>
  <Button size="icon">
    <Plus class="h-4 w-4" />
  </Button>
</div>

<!-- Alert variants -->
<Alert.Root>
  <AlertCircle class="h-4 w-4" />
  <Alert.Title>Note</Alert.Title>
  <Alert.Description>This is a default alert.</Alert.Description>
</Alert.Root>

<Alert.Root variant="destructive">
  <AlertTriangle class="h-4 w-4" />
  <Alert.Title>Error</Alert.Title>
  <Alert.Description>Something went wrong.</Alert.Description>
</Alert.Root>

<!-- Badge variants -->
<div class="flex gap-2">
  <Badge variant="default">Default</Badge>
  <Badge variant="secondary">Secondary</Badge>
  <Badge variant="destructive">Error</Badge>
  <Badge variant="outline">Outline</Badge>
</div>
```

## Icon System

### Icon Component Usage
All icons should use the `@/components/icon.svelte` custom component with icons from `@lucide/svelte`:

```svelte
<script lang="ts">
  import Icon from "@/components/icon.svelte";
  import { Menu, Plus, Trash2 } from "@lucide/svelte";
</script>

<!-- Using the custom Icon component -->
<Icon name={Menu} />
<Icon name={Plus} size={16} />
<Icon name={Trash2} class="text-destructive" />

<!-- Direct usage when needed -->
<Menu class="size-4" />
<Plus class="size-5 text-primary" />
<Trash2 class="size-4 text-destructive" />
```

### Icon with Buttons
```svelte
<script lang="ts">
  import { Button } from "@/components/ui/button";
  import { Plus, Download, Settings } from "@lucide/svelte";
</script>

<!-- Icon only buttons -->
<Button size="icon" variant="outline">
  <Icon name={Plus} />
</Button>

<!-- Buttons with text and icons -->
<Button>
  <Icon name={Plus} size={16} class="mr-2" />
  Add Todo
</Button>

<Button variant="secondary">
  <Icon name={Download} size={16} class="mr-2" />
  Export
</Button>

<Button variant="ghost" size="sm">
  <Icon name={Settings} size={16} class="mr-2" />
  Settings
</Button>
```

## Responsive Design Patterns

### Container Queries (TailwindCSS v4)
```svelte
<!-- Using container queries for adaptive components -->
<div class="@container">
  <div class="@md:flex @md:gap-4">
    <div class="@md:flex-1">
      <h2 class="@lg:text-2xl text-xl">Responsive Title</h2>
    </div>
    <div class="@md:flex-shrink-0">
      <Button size="sm" class="@lg:size-default">Action</Button>
    </div>
  </div>
</div>
```

## Layout Patterns

### Card Layouts
```svelte
<script lang="ts">
  import * as Card from "@/components/ui/card";
  import { Button } from "@/components/ui/button";
  import { Badge } from "@/components/ui/badge";
</script>

<!-- Standard card layout -->
<Card.Root>
  <Card.Header>
    <div class="flex items-start justify-between">
      <div>
        <Card.Title>Todo Title</Card.Title>
        <Card.Description>Created 2 hours ago</Card.Description>
      </div>
      <Badge variant="secondary">High</Badge>
    </div>
  </Card.Header>

  <Card.Content>
    <p class="text-muted-foreground">
      Todo description goes here...
    </p>
  </Card.Content>

  <Card.Footer class="flex justify-between">
    <span class="text-sm text-muted-foreground">Due: Tomorrow</span>
    <div class="flex gap-2">
      <Button variant="outline" size="sm">Edit</Button>
      <Button variant="destructive" size="sm">Delete</Button>
    </div>
  </Card.Footer>
</Card.Root>
```

### Form Layouts
```svelte
<script lang="ts">
  import * as Card from "@/components/ui/card";
  import { Label } from "@/components/ui/label";
  import { Input } from "@/components/ui/input";
  import { Button } from "@/components/ui/button";
  import * as Select from "@/components/ui/select";
</script>

<Card.Root class="max-w-md mx-auto">
  <Card.Header>
    <Card.Title>Create New Todo</Card.Title>
  </Card.Header>

  <Card.Content class="space-y-4">
    <!-- Form field pattern -->
    <div class="space-y-2">
      <Label for="title">Title</Label>
      <Input id="title" placeholder="Enter todo title" />
    </div>

    <div class="space-y-2">
      <Label for="priority">Priority</Label>
      <Select>
        <Select.Trigger>
          <SelectValue placeholder="Select priority" />
        </Select.Trigger>
        <Select.Content>
          <Select.Item value="low">Low</Select.Item>
          <Select.Item value="medium">Medium</Select.Item>
          <Select.Item value="high">High</Select.Item>
        </Select.Content>
      </Select>
    </div>

    <!-- Button group -->
    <div class="flex gap-2 pt-4">
      <Button variant="outline" class="flex-1">Cancel</Button>
      <Button class="flex-1">Create</Button>
    </div>
  </Card.Content>
</Card.Root>
```

## UI Principles

### Design Consistency
- Keep interfaces clean and uncluttered
- Implement clear loading, success, and error states for all user interactions
- Use clear and consistent messaging for errors
- Follow the shadcn-svelte design system principles
- Use proper semantic HTML elements where possible

### State Indication
```svelte
<script lang="ts">
  let loading = $state(false);
  let error = $state<string | null>(null);
  let success = $state(false);
</script>

<!-- Loading state -->
{#if loading}
  <Button disabled>
    <Icon name={Loader2} class="mr-2 animate-spin" />
    Loading...
  </Button>
{:else}
  <Button>Submit</Button>
{/if}

<!-- Error state -->
{#if error}
  <Alert.Root variant="destructive">
    <Icon name={AlertTriangle} />
    <Alert.Title>Error</Alert.Title>
    <Alert.Description>{error}</Alert.Description>
  </Alert.Root>
{/if}

<!-- Success state -->
{#if success}
  <Alert.Root>
    <Icon name={CheckCircle} />
    <Alert.Title>Success</Alert.Title>
    <Alert.Description>Operation completed successfully.</Alert.Description>
  </Alert.Root>
{/if}
```