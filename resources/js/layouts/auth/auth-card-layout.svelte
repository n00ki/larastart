<script lang="ts">
  import type { Snippet } from "svelte";

  import { Link } from "@inertiajs/svelte";

  import AppLogoIcon from "@/components/app-logo-icon.svelte";
  import * as Card from "@/components/ui/card";

  import { home } from "@/routes";

  interface Props {
    title: string;
    description: string;
    children?: Snippet;
  }

  const { title, description, children }: Props = $props();
</script>

<div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
  <div class="flex w-full max-w-md flex-col gap-6">
    <Link href={home()} class="flex items-center gap-2 self-center font-medium">
      <div class="flex items-center justify-center">
        <AppLogoIcon class="size-9 fill-current text-foreground" />
      </div>
    </Link>

    <div class="flex flex-col gap-6">
      <Card.Root class="rounded-xl">
        <Card.Header class="px-10 pt-8 pb-0 text-center">
          <Card.Title class="text-xl">{title}</Card.Title>
          <Card.Description>
            {description}
          </Card.Description>
        </Card.Header>
        <Card.Content class="px-10 py-8">
          {@render children?.()}
        </Card.Content>
      </Card.Root>
    </div>
  </div>
</div>
