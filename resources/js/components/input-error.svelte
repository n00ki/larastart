<script lang="ts">
  import type { HTMLAttributes } from 'svelte/elements';

  import { cn } from '@/lib/utils';

  interface Props extends HTMLAttributes<HTMLParagraphElement> {
    message?: string;
    class?: string;
  }

  const { message, class: className = '', ...props }: Props = $props();
</script>

{#if message}
  <p {...props} class={cn('text-sm text-destructive', className)}>
    {message}
  </p>
{/if}
