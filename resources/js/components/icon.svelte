<script lang="ts">
  import type { Icon as IconType } from "@lucide/svelte";

  interface Props {
    name: typeof IconType;
    size?: number;
    class?: string;
    [key: string]: any;
  }

  const { name: Icon, size = 16, class: className, ...props }: Props = $props();
</script>

<Icon {size} class={className} {...props} />
