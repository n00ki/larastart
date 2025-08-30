<script lang="ts">
  import type { User } from "@/types";

  import * as Avatar from "@/components/ui/avatar";

  import { getInitials } from "@/hooks/use-initials";

  interface Props {
    user: User;
    showEmail?: boolean;
  }

  const { user, showEmail = false }: Props = $props();
</script>

<Avatar.Root class="h-8 w-8 rounded-lg">
  <Avatar.Image src={user.avatar} alt={user.name} />
  <Avatar.Fallback class="rounded-lg bg-muted text-foreground">
    {getInitials(user.name)}
  </Avatar.Fallback>
</Avatar.Root>
<div class="grid flex-1 text-left text-sm leading-tight">
  <span class="truncate font-semibold">{user.name}</span>
  {#if showEmail}
    <span class="truncate text-xs text-muted-foreground">{user.email}</span>
  {/if}
</div>
