<script lang="ts">
  import { Link } from '@inertiajs/svelte';

  import { mainNavItems, secondaryNavItems } from '@/lib/navigation';

  import AppLogo from '@/components/app-logo.svelte';
  import NavFooter from '@/components/nav-footer.svelte';
  import NavMain from '@/components/nav-main.svelte';
  import NavUser from '@/components/nav-user.svelte';
  import * as Sidebar from '@/components/ui/sidebar';

  import { dashboard } from '@/routes';
</script>

<Sidebar.Root collapsible="icon" variant="inset">
  <Sidebar.Header>
    <Sidebar.Menu>
      <Sidebar.MenuItem>
        <Sidebar.MenuButton size="lg">
          {#snippet child({ props })}
            <Link href={dashboard()} prefetch {...props}>
              <AppLogo />
            </Link>
          {/snippet}
        </Sidebar.MenuButton>
      </Sidebar.MenuItem>
    </Sidebar.Menu>
  </Sidebar.Header>

  <Sidebar.Content>
    <NavMain items={mainNavItems} />
  </Sidebar.Content>

  <Sidebar.Footer>
    <NavFooter items={secondaryNavItems} class="mt-auto" />
    <NavUser />
  </Sidebar.Footer>

  <Sidebar.Rail />
</Sidebar.Root>
