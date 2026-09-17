<script lang="ts">
  import type { LucideIcon } from '@lucide/svelte';

  interface Props {
    name: LucideIcon;
    size?: number;
    class?: string;
    [key: string]: any;
  }

  const { name: Icon, size = 16, class: className, ...props }: Props = $props();
</script>

<Icon {size} class={className} {...props} />
