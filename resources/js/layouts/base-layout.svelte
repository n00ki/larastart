<script lang='ts'>
  import type { Snippet } from 'svelte';

  import { page } from '@inertiajs/svelte';

  import { Toaster } from '@/components/ui/sonner';
  import { useTheme } from '@/hooks/use-theme.svelte';

  import { displayFlashMessage } from '@/lib/utils';

  interface Props {
    children?: Snippet;
  }

  const { children }: Props = $props();
  const theme = $derived(useTheme().current);

  $effect(() => {
    if ($page.props.flash) {
      const { type, message } = $page.props.flash;
      setTimeout(() => {
        displayFlashMessage(type, message);
      }, 250);
    }
  });
</script>

<Toaster position='bottom-right' richColors {theme} />

{@render children?.()}
