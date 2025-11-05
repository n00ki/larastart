<script lang="ts">
  import type { NavItem } from '@/types';
  import type { Snippet } from 'svelte';

  import { Link, page } from '@inertiajs/svelte';

  import { cn } from '@/lib/utils';

  import Heading from '@/components/heading.svelte';
  import { Button } from '@/components/ui/button';
  import { Separator } from '@/components/ui/separator';

  import { appearance } from '@/routes';
  import { edit as editPassword } from '@/routes/password';
  import { edit as editProfile } from '@/routes/profile';

  const sidebarNavItems: NavItem[] = [
    {
      title: 'Profile',
      href: editProfile().url,
    },
    {
      title: 'Password',
      href: editPassword().url,
    },
    {
      title: 'Appearance',
      href: appearance().url,
    },
  ];

  interface Props {
    children?: Snippet;
  }

  const { children }: Props = $props();

  const currentPath = $derived(new URL($page.url).pathname);
</script>

<div class="px-4 py-6">
  <Heading
    title="Settings"
    description="Manage your profile and account settings"
  />

  <div
    class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12"
  >
    <aside class="w-full max-w-xl lg:w-48">
      <nav class="flex flex-col space-y-1 space-x-0">
        {#each sidebarNavItems as item (item.title)}
          <Link href={item.href}>
            <Button
              variant="ghost"
              class={cn('w-full justify-start', {
                'bg-muted': currentPath === item.href,
              })}
            >
              {item.title}
            </Button>
          </Link>
        {/each}
      </nav>
    </aside>

    <Separator class="my-6 md:hidden" />

    <div class="flex-1 md:max-w-2xl">
      <section class="max-w-xl space-y-12">
        {@render children?.()}
      </section>
    </div>
  </div>
</div>
