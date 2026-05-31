<script lang="ts">
  import { page } from '@inertiajs/svelte';
  import { ChevronsUpDown } from '@lucide/svelte';

  import Icon from '@/components/icon.svelte';
  import * as DropdownMenu from '@/components/ui/dropdown-menu';
  import * as Sidebar from '@/components/ui/sidebar';
  import UserInfo from '@/components/user-info.svelte';
  import UserMenuContent from '@/components/user-menu-content.svelte';

  const user = $derived(page.props.auth.user);
  const sidebar = Sidebar.useSidebar();
</script>

<Sidebar.Menu>
  <Sidebar.MenuItem>
    <DropdownMenu.Root>
      <DropdownMenu.Trigger>
        {#snippet child({ props })}
          <Sidebar.MenuButton
            size="lg"
            class="data-open:bg-sidebar-accent data-open:text-sidebar-accent-foreground"
            data-active={undefined}
            data-test="sidebar-menu-button"
            {...props}
          >
            <UserInfo {user} showEmail />
            <Icon name={ChevronsUpDown} class="ml-auto" />
          </Sidebar.MenuButton>
        {/snippet}
      </DropdownMenu.Trigger>
      <DropdownMenu.Content
        class="w-(--bits-dropdown-menu-anchor-width) min-w-56 rounded-lg"
        side={sidebar.isMobile ? 'bottom' : 'right'}
        align="end"
        sideOffset={4}
      >
        <UserMenuContent {user} />
      </DropdownMenu.Content>
    </DropdownMenu.Root>
  </Sidebar.MenuItem>
</Sidebar.Menu>
