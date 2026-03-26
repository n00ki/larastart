<script lang="ts">
  import type { Snippet } from 'svelte';

  import { page } from '@inertiajs/svelte';

  import { useTheme } from '@/hooks/use-theme.svelte';
  import { createFlashToastHandler } from '@/lib/utils';

  import { Toaster } from '@/components/ui/sonner';

  interface Props {
    children?: Snippet;
  }

  const { children }: Props = $props();
  const theme = $derived(useTheme().current);
  const handleFlashToast = createFlashToastHandler();

  $effect(() => {
    handleFlashToast(page.flash);
  });
</script>

<Toaster position="bottom-right" richColors {theme} />

{@render children?.()}
