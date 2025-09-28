<script lang='ts'>
  import type { ComponentProps, Snippet } from 'svelte';

  import { Link } from '@inertiajs/svelte';

  import { cn } from '@/lib/utils';

  interface Props extends ComponentProps<Link> {
    class?: string;
    children: Snippet;
  }

  const { class: className = '', children, ...props }: Props = $props();
</script>

<Link
  class={cn(
    `
      text-foreground underline decoration-border underline-offset-4 transition-colors duration-300 ease-out
      hover:decoration-current!
    `,
    className,
  )}
  {...props}
>
  {@render children()}
</Link>
