<script lang="ts">
  import type { Snippet } from 'svelte';

  import { router } from '@inertiajs/svelte';
  import { onMount } from 'svelte';

  import { useTheme } from '@/hooks/use-theme.svelte';
  import { displayFlashMessage } from '@/lib/utils';

  import { Toaster } from '@/components/ui/sonner';

  interface Props {
    children?: Snippet;
  }

  const { children }: Props = $props();
  const theme = $derived(useTheme().current);

  // Listen for Inertia flash events (v2.3+)
  // @see https://inertiajs.com/docs/v2/data-props/flash-data#global-flash-event
  onMount(() => {
    return router.on('flash', (event) => {
      const { type, message } = event.detail.flash;
      if (type && message) {
        displayFlashMessage(type, message);
      }
    });
  });
</script>

<Toaster position="bottom-right" richColors {theme} />

{@render children?.()}
