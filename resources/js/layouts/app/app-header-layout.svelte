<script lang="ts">
  import type { BreadcrumbItem } from '@/types';
  import type { Snippet } from 'svelte';

  import AppContent from '@/components/app-content.svelte';
  import AppHeader from '@/components/app-header.svelte';
  import AppShell from '@/components/app-shell.svelte';

  interface Props {
    breadcrumbs?: BreadcrumbItem[];
    children?: Snippet;
  }

  const { breadcrumbs = [], children }: Props = $props();
</script>

<AppShell variant="header">
  <AppHeader {breadcrumbs} />
  <AppContent variant="header">
    {@render children?.()}
  </AppContent>
</AppShell>
