<script module lang="ts">
  import AppLayout from '@/layouts/app-layout.svelte';

  import { dashboard } from '@/routes';

  export const layout = [
    AppLayout,
    {
      breadcrumbs: [
        {
          title: 'Dashboard',
          href: dashboard(),
        },
      ],
    },
  ];
</script>

<script lang="ts">
  import AppHead from '@/components/app-head.svelte';
  import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
</script>

<AppHead
  title="Dashboard"
  description="Manage your LaraStart application from the dashboard. View analytics, settings, and more."
/>

<div class="space-y-4 p-4">
  <div class="grid auto-rows-min gap-4 md:grid-cols-3">
    <div
      class="
          relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70
          dark:border-sidebar-border
        "
    >
      <PlaceholderPattern
        class="absolute inset-0 size-full stroke-foreground/20"
      />
    </div>
    <div
      class="
          relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70
          dark:border-sidebar-border
        "
    >
      <PlaceholderPattern
        class="absolute inset-0 size-full stroke-foreground/20"
      />
    </div>
    <div
      class="
          relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70
          dark:border-sidebar-border
        "
    >
      <PlaceholderPattern
        class="absolute inset-0 size-full stroke-foreground/20"
      />
    </div>
  </div>
  <div
    class="
        relative h-[calc(100vh-23rem)] overflow-hidden rounded-xl border border-sidebar-border/70
        dark:border-sidebar-border
      "
  >
    <PlaceholderPattern
      class="absolute inset-0 size-full stroke-foreground/20"
    />
  </div>
</div>
