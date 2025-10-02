<script lang="ts">
  import type { NavItem } from '@/types';

  import { Link } from '@inertiajs/svelte';
  import { BookOpen, Folder, LayoutGrid } from '@lucide/svelte';

  import AppLogo from '@/components/app-logo.svelte';
  import NavFooter from '@/components/nav-footer.svelte';
  import NavMain from '@/components/nav-main.svelte';
  import NavUser from '@/components/nav-user.svelte';
  import * as Sidebar from '@/components/ui/sidebar';

  import { dashboard } from '@/routes';

  const mainNavItems: NavItem[] = [
    {
      title: 'Dashboard',
      href: dashboard().url,
      icon: LayoutGrid,
    },
  ];

  const footerNavItems: NavItem[] = [
    {
      title: 'Repository',
      href: 'https://github.com/laravel/react-starter-kit',
      icon: Folder,
    },
    {
      title: 'Documentation',
      href: 'https://laravel.com/docs/starter-kits#react',
      icon: BookOpen,
    },
  ];
</script>

<Sidebar.Root collapsible="icon" variant="inset">
  <Sidebar.Header>
    <Sidebar.Menu>
      <Sidebar.MenuItem>
        <Sidebar.MenuButton size="lg">
          {#snippet child({ props })}
            <Link href="/dashboard" prefetch {...props}>
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
    <NavFooter items={footerNavItems} class="mt-auto" />
    <NavUser />
  </Sidebar.Footer>
</Sidebar.Root>
