<script lang="ts">
  import type { BreadcrumbItem } from "@/types";

  import { Link } from "@inertiajs/svelte";

  import * as Breadcrumb from "@/components/ui/breadcrumb";

  interface Props {
    breadcrumbs: BreadcrumbItem[];
  }

  const { breadcrumbs }: Props = $props();
</script>

<Breadcrumb.Root>
  <Breadcrumb.List>
    {#each breadcrumbs as item, index (item.href || index)}
      {@const isLast = index === breadcrumbs.length - 1}
      <Breadcrumb.Item>
        {#if isLast}
          <Breadcrumb.Page>{item.title}</Breadcrumb.Page>
        {:else if item.href}
          <Breadcrumb.Link>
            {#snippet child()}
              <Link href={item.href}>{item.title}</Link>
            {/snippet}
          </Breadcrumb.Link>
        {:else}
          <Breadcrumb.Page>{item.title}</Breadcrumb.Page>
        {/if}
      </Breadcrumb.Item>
      {#if !isLast}
        <Breadcrumb.Separator />
      {/if}
    {/each}
  </Breadcrumb.List>
</Breadcrumb.Root>
