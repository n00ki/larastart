<script lang="ts">
  import type { Passkey } from '@/types/auth';

  import { KeyRound } from '@lucide/svelte';

  import HeadingSmall from '@/components/heading-small.svelte';
  import PasskeyItem from '@/components/passkey-item.svelte';
  import PasskeyRegister from '@/components/passkey-register.svelte';

  interface Props {
    canManagePasskeys?: boolean;
    passkeys: Passkey[];
  }

  const { canManagePasskeys = false, passkeys }: Props = $props();
</script>

{#if canManagePasskeys}
  <div class="space-y-6">
    <HeadingSmall
      title="Passkeys"
      description="Manage your passkeys for passwordless sign-in"
    />

    <div class="overflow-hidden rounded-lg border">
      {#if passkeys.length > 0}
        {#each passkeys as passkey (passkey.id)}
          <PasskeyItem {passkey} />
        {/each}
      {:else}
        <div class="p-8 text-center">
          <div
            class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-muted"
          >
            <KeyRound class="size-7 text-muted-foreground" />
          </div>
          <p class="font-medium">No passkeys yet</p>
          <p class="mt-1 text-sm text-muted-foreground">
            Add a passkey to sign in without a password
          </p>
        </div>
      {/if}
    </div>

    <PasskeyRegister />
  </div>
{/if}
