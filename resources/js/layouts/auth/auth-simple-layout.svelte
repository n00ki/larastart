<script lang="ts">
  import type { Snippet } from "svelte";

  import { Link } from "@inertiajs/svelte";

  import AppLogoIcon from "@/components/app-logo-icon.svelte";

  import { home } from "@/routes";

  interface Props {
    title: string;
    description: string;
    children?: Snippet;
  }

  const { title, description, children }: Props = $props();
</script>

<div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
  <div class="w-full max-w-sm">
    <div class="flex flex-col gap-8">
      <div class="flex flex-col items-center gap-4">
        <Link href={home()} class="flex flex-col items-center gap-2 font-medium">
          <div class="mb-1 flex items-center justify-center">
            <AppLogoIcon class="size-9 fill-current text-foreground" />
          </div>
          <span class="sr-only">{title}</span>
        </Link>
        <div class="space-y-2 text-center">
          <h1 class="text-xl font-medium">{title}</h1>
          <p class="text-center text-sm text-muted-foreground">{description}</p>
        </div>
      </div>
      {@render children?.()}
    </div>
  </div>
</div>
