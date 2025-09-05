<script lang="ts">
  import type { Snippet } from "svelte";

  import { SidebarInset } from "@/components/ui/sidebar";

  import { cn } from "@/lib/utils";

  interface Props {
    variant?: "header" | "sidebar";
    class?: string;
    children: Snippet;
  }

  const { variant = undefined, class: className = "", children }: Props = $props();
</script>

{#if variant === "sidebar"}
  <SidebarInset class={className}>
    <div class="mx-auto w-full max-w-7xl">
      {@render children?.()}
    </div>
  </SidebarInset>
{:else}
  <main class={cn(`mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl`, className)}>
    {@render children?.()}
  </main>
{/if}
