<script lang="ts">
  import type { BreadcrumbItem, NavItem } from '@/types';

  import { Link, page } from '@inertiajs/svelte';
  import { BookOpen, Folder, LayoutGrid, Menu, Search } from '@lucide/svelte';

  import { getInitials } from '@/hooks/use-initials';
  import { cn } from '@/lib/utils';

  import AppLogoIcon from '@/components/app-logo-icon.svelte';
  import AppLogo from '@/components/app-logo.svelte';
  import Breadcrumbs from '@/components/breadcrumbs.svelte';
  import Icon from '@/components/icon.svelte';
  import * as Avatar from '@/components/ui/avatar';
  import { Button } from '@/components/ui/button';
  import * as DropdownMenu from '@/components/ui/dropdown-menu';
  import * as NavigationMenu from '@/components/ui/navigation-menu';
  import * as Sheet from '@/components/ui/sheet';
  import * as Tooltip from '@/components/ui/tooltip';
  import UserMenuContent from '@/components/user-menu-content.svelte';

  import { dashboard } from '@/routes';

  interface Props {
    breadcrumbs?: BreadcrumbItem[];
  }

  const { breadcrumbs = [] }: Props = $props();

  const auth = $derived($page.props.auth);

  const mainNavItems: NavItem[] = [
    {
      title: 'Dashboard',
      href: dashboard().url,
      icon: LayoutGrid,
    },
  ];

  const rightNavItems: NavItem[] = [
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

  const activeItemStyles = 'text-foreground bg-accent';
</script>

<div class="border-b border-sidebar-border/80">
  <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
    <!-- Mobile Menu -->
    <div class="lg:hidden">
      <Sheet.Root>
        <Sheet.Trigger>
          {#snippet child({ props })}
            <Button variant="ghost" size="icon" class="mr-2 size-9" {...props}>
              <Icon name={Menu} size={20} />
            </Button>
          {/snippet}
        </Sheet.Trigger>
        <Sheet.Content
          side="left"
          class="flex h-full w-64 flex-col items-stretch justify-between bg-sidebar"
        >
          <Sheet.Title class="sr-only">Navigation Menu</Sheet.Title>
          <Sheet.Header class="flex justify-start text-left">
            <AppLogoIcon class="size-6 fill-current text-foreground" />
          </Sheet.Header>
          <div class="flex h-full flex-1 flex-col space-y-4 p-4">
            <div class="flex h-full flex-col justify-between text-sm">
              <div class="flex flex-col space-y-4">
                {#each mainNavItems as item (item.title)}
                  <Link
                    href={item.href}
                    class="flex items-center space-x-2 font-medium"
                  >
                    {#if item.icon}
                      <Icon name={item.icon} />
                    {/if}
                    <span>{item.title}</span>
                  </Link>
                {/each}
              </div>

              <div class="flex flex-col space-y-4">
                {#each rightNavItems as item (item.title)}
                  <a
                    href={item.href}
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center space-x-2 font-medium"
                  >
                    {#if item.icon}
                      <Icon name={item.icon} />
                    {/if}
                    <span>{item.title}</span>
                  </a>
                {/each}
              </div>
            </div>
          </div>
        </Sheet.Content>
      </Sheet.Root>
    </div>

    <Link href="/dashboard" prefetch class="flex items-center space-x-2">
      <AppLogo />
    </Link>

    <!-- Desktop Navigation -->
    <div class="ml-6 hidden h-full items-center space-x-6 lg:flex">
      <NavigationMenu.Root class="flex h-full items-stretch">
        <NavigationMenu.List class="flex h-full items-stretch space-x-2">
          {#each mainNavItems as item, index (index)}
            <NavigationMenu.Item class="relative flex h-full items-center">
              <Link
                href={item.href}
                class={cn(
                  `
                    inline-flex items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium
                    text-accent-foreground ring-offset-background transition-colors
                    hover:bg-accent hover:text-accent-foreground
                    focus:bg-accent focus:text-accent-foreground focus:outline-none
                    disabled:pointer-events-none disabled:opacity-50
                  `,
                  'h-9 cursor-pointer px-3',
                  $page.url === item.href && activeItemStyles,
                )}
              >
                {#if item.icon}
                  <Icon name={item.icon} />
                {/if}
                {item.title}
              </Link>
              {#if $page.url === item.href}
                <div
                  class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-foreground"
                ></div>
              {/if}
            </NavigationMenu.Item>
          {/each}
        </NavigationMenu.List>
      </NavigationMenu.Root>
    </div>

    <div class="ml-auto flex items-center space-x-2">
      <div class="relative flex items-center space-x-1">
        <Button
          variant="ghost"
          size="icon"
          class="group h-9 w-9 cursor-pointer"
        >
          <Search class="!size-5 opacity-80 group-hover:opacity-100" />
        </Button>
        <div class="hidden lg:flex">
          {#each rightNavItems as item (item.title)}
            <Tooltip.Provider delayDuration={0}>
              <Tooltip.Root>
                <Tooltip.Trigger>
                  <a
                    href={item.href}
                    target="_blank"
                    rel="noopener noreferrer"
                    class="
                      group ml-1 inline-flex h-9 w-9 items-center justify-center rounded-md bg-transparent p-0 text-sm
                      font-medium text-accent-foreground ring-offset-background transition-colors
                      hover:bg-accent hover:text-accent-foreground
                      focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2
                      focus-visible:outline-none
                      disabled:pointer-events-none disabled:opacity-50
                    "
                  >
                    <span class="sr-only">{item.title}</span>
                    {#if item.icon}
                      <Icon name={item.icon} />
                    {/if}
                  </a>
                </Tooltip.Trigger>
                <Tooltip.Content>
                  <p>{item.title}</p>
                </Tooltip.Content>
              </Tooltip.Root>
            </Tooltip.Provider>
          {/each}
        </div>
      </div>
      <DropdownMenu.Root>
        <DropdownMenu.Trigger>
          {#snippet child({ props })}
            <Button variant="ghost" class="size-10 rounded-full p-1" {...props}>
              <Avatar.Root class="size-8 overflow-hidden rounded-full">
                <Avatar.Image src={auth.user.avatar} alt={auth.user.name} />
                <Avatar.Fallback class="rounded-lg bg-muted text-foreground">
                  {getInitials(auth.user.name)}
                </Avatar.Fallback>
              </Avatar.Root>
            </Button>
          {/snippet}
        </DropdownMenu.Trigger>
        <DropdownMenu.Content class="w-56" align="end">
          <UserMenuContent user={auth.user} />
        </DropdownMenu.Content>
      </DropdownMenu.Root>
    </div>
  </div>
</div>
{#if breadcrumbs.length > 1}
  <div class="flex w-full border-b border-sidebar-border/70">
    <div
      class="mx-auto flex h-12 w-full items-center justify-start px-4 text-muted-foreground md:max-w-7xl"
    >
      <Breadcrumbs {breadcrumbs} />
    </div>
  </div>
{/if}
