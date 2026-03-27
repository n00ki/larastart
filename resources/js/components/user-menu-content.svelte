<script lang="ts">
  import type { User } from '@/types';

  import { Link, router } from '@inertiajs/svelte';
  import { LogOut, Settings } from '@lucide/svelte';

  import { cn } from '@/lib/utils';

  import Icon from '@/components/icon.svelte';
  import * as DropdownMenu from '@/components/ui/dropdown-menu';
  import UserInfo from '@/components/user-info.svelte';

  import { logout } from '@/routes';
  import { edit } from '@/routes/settings/profile';

  interface Props {
    user: User;
  }

  const { user }: Props = $props();

  type ClickHandler = (() => void) | undefined;

  function dropdownItemClass(className: unknown): string {
    return cn('block w-full', typeof className === 'string' ? className : '');
  }

  function handleLogout(onclick: unknown) {
    const callback =
      typeof onclick === 'function' ? (onclick as ClickHandler) : undefined;

    return () => {
      callback?.();

      document.body.style.removeProperty('pointer-events');
      router.flushAll();
    };
  }
</script>

<DropdownMenu.Label class="p-0 font-normal">
  <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
    <UserInfo {user} showEmail={true} />
  </div>
</DropdownMenu.Label>
<DropdownMenu.Separator />
<DropdownMenu.Group>
  <DropdownMenu.Item class="w-full">
    {#snippet child({ props })}
      <Link
        class={dropdownItemClass(props.class)}
        href={edit()}
        as="button"
        prefetch
        {...props}
      >
        <Icon name={Settings} />
        Settings
      </Link>
    {/snippet}
  </DropdownMenu.Item>
</DropdownMenu.Group>
<DropdownMenu.Separator />
<DropdownMenu.Item class="w-full">
  {#snippet child({ props })}
    <Link
      class={dropdownItemClass(props.class)}
      method="post"
      href={logout()}
      as="button"
      {...props}
      onclick={handleLogout(props.onclick)}
      data-test="logout-button"
    >
      <Icon name={LogOut} />
      Log out
    </Link>
  {/snippet}
</DropdownMenu.Item>
