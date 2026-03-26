<script lang="ts">
  import type { AppVariant } from '@/types';
  import type { Snippet } from 'svelte';

  import { page } from '@inertiajs/svelte';

  import { SidebarProvider } from '@/components/ui/sidebar';

  interface Props {
    children: Snippet;
    variant?: AppVariant;
  }

  const { children, variant = 'header' }: Props = $props();

  let isOpen = $derived(Boolean(page.props.sidebarOpen));
</script>

{#if variant === 'header'}
  <div class="flex min-h-screen w-full flex-col">
    {@render children()}
  </div>
{:else}
  <SidebarProvider bind:open={isOpen}>
    {@render children()}
  </SidebarProvider>
{/if}
