<script lang="ts">
  import type { NavItem } from '@/types';
  import type { HTMLAttributes } from 'svelte/elements';

  import { toUrl } from '@/lib/utils';

  import Icon from '@/components/icon.svelte';
  import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
  } from '@/components/ui/sidebar';

  interface Props extends HTMLAttributes<HTMLElement> {
    items: NavItem[];
    class?: string;
  }

  const { items, class: className, ...props }: Props = $props();
</script>

<SidebarGroup
  {...props}
  class={`
  group-data-[collapsible=icon]:p-0
  ${className || ''}
`}
>
  <SidebarGroupContent>
    <SidebarMenu>
      {#each items as item (item.title)}
        <SidebarMenuItem>
          <SidebarMenuButton
            class="text-muted-foreground hover:text-foreground"
          >
            {#snippet child({ props })}
              <a
                href={toUrl(item.href)}
                target="_blank"
                rel="noopener noreferrer"
                {...props}
              >
                {#if item.icon}
                  <Icon name={item.icon} />
                {/if}
                <span>{item.title}</span>
              </a>
            {/snippet}
          </SidebarMenuButton>
        </SidebarMenuItem>
      {/each}
    </SidebarMenu>
  </SidebarGroupContent>
</SidebarGroup>
