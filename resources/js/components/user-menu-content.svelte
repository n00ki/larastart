<script lang="ts">
  import type { User } from "@/types";

  import { Link, router } from "@inertiajs/svelte";
  import { LogOut, Settings } from "@lucide/svelte";

  import Icon from "@/components/icon.svelte";
  import * as DropdownMenu from "@/components/ui/dropdown-menu";
  import UserInfo from "@/components/user-info.svelte";

  import { logout } from "@/routes";
  import { edit } from "@/routes/profile";

  interface Props {
    user: User;
  }

  const { user }: Props = $props();

  function handleLogout() {
    // mobile navigation cleanup
    document.body.style.removeProperty("pointer-events");
    router.flushAll();
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
      <Link class="block w-full" href={edit()} as="button" prefetch {...props}>
        <Icon name={Settings} />
        Settings
      </Link>
    {/snippet}
  </DropdownMenu.Item>
</DropdownMenu.Group>
<DropdownMenu.Separator />
<DropdownMenu.Item class="w-full">
  {#snippet child({ props })}
    <Link class="block w-full" method="post" href={logout()} as="button" onclick={handleLogout} {...props}>
      <Icon name={LogOut} />
      Log out
    </Link>
  {/snippet}
</DropdownMenu.Item>
