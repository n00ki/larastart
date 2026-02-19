<script lang="ts">
  import type { NavItem } from '@/types';

  import { Link, page } from '@inertiajs/svelte';

  import { toUrl } from '@/lib/utils';

  import Icon from '@/components/icon.svelte';
  import * as Sidebar from '@/components/ui/sidebar';

  interface Props {
    items?: NavItem[];
  }

  const { items = [] }: Props = $props();
</script>

<Sidebar.Group class="px-2 py-0">
  <Sidebar.GroupLabel>Platform</Sidebar.GroupLabel>
  <Sidebar.Menu>
    {#each items as item (item.title)}
      <Sidebar.MenuItem>
        <Sidebar.MenuButton
          isActive={$page.url.startsWith(toUrl(item.href))}
          tooltipContent={item.title}
        >
          {#snippet child({ props })}
            <Link href={item.href} prefetch {...props}>
              {#if item.icon}
                <Icon name={item.icon} />
              {/if}
              <span>{item.title}</span>
            </Link>
          {/snippet}
        </Sidebar.MenuButton>
      </Sidebar.MenuItem>
    {/each}
  </Sidebar.Menu>
</Sidebar.Group>
