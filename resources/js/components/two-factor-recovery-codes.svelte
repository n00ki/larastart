<script lang="ts">
  import { Form } from '@inertiajs/svelte';
  import { Eye, EyeOff, LockKeyhole, RefreshCw } from '@lucide/svelte';
  import { onMount, tick } from 'svelte';

  import { twoFactorAuthState } from '@/lib/state/two-factor-auth.svelte';

  import AlertError from '@/components/alert-error.svelte';
  import { Button } from '@/components/ui/button';
  import * as Card from '@/components/ui/card';

  import { regenerateRecoveryCodes } from '@/routes/two-factor';

  const twoFactorAuth = twoFactorAuthState();
  let isRecoveryCodesVisible = $state(false);
  let recoveryCodeSectionRef = $state<HTMLDivElement | undefined>();

  async function toggleRecoveryCodesVisibility() {
    if (
      !isRecoveryCodesVisible &&
      !twoFactorAuth.state.recoveryCodesList.length
    ) {
      await twoFactorAuth.fetchRecoveryCodes();
    }

    isRecoveryCodesVisible = !isRecoveryCodesVisible;

    if (isRecoveryCodesVisible) {
      await tick();
      recoveryCodeSectionRef?.scrollIntoView({ behavior: 'smooth' });
    }
  }

  onMount(async () => {
    if (!twoFactorAuth.state.recoveryCodesList.length) {
      await twoFactorAuth.fetchRecoveryCodes();
    }
  });
</script>

<Card.Root class="w-full">
  <Card.Header>
    <Card.Title class="flex gap-3">
      <LockKeyhole class="size-4" />2FA Recovery Codes
    </Card.Title>
    <Card.Description>
      Recovery codes let you regain access if you lose your 2FA device. Store
      them in a secure password manager.
    </Card.Description>
  </Card.Header>
  <Card.Content>
    <div
      class="flex flex-col gap-3 select-none sm:flex-row sm:items-center sm:justify-between"
    >
      <Button onclick={toggleRecoveryCodesVisibility} class="w-fit">
        {#if isRecoveryCodesVisible}
          <EyeOff class="size-4" />
        {:else}
          <Eye class="size-4" />
        {/if}
        {isRecoveryCodesVisible ? 'Hide' : 'View'} Recovery Codes
      </Button>

      {#if isRecoveryCodesVisible && twoFactorAuth.state.recoveryCodesList.length}
        <Form
          {...regenerateRecoveryCodes.form()}
          options={{ preserveScroll: true }}
          onSuccess={() => twoFactorAuth.fetchRecoveryCodes()}
        >
          {#snippet children({ processing })}
            <Button variant="secondary" type="submit" disabled={processing}>
              <RefreshCw class="size-4" /> Regenerate Codes
            </Button>
          {/snippet}
        </Form>
      {/if}
    </div>

    <div
      class="relative overflow-hidden transition-all duration-300 {isRecoveryCodesVisible
        ? 'h-auto opacity-100'
        : 'h-0 opacity-0'}"
    >
      {#if twoFactorAuth.state.errors.length}
        <div class="mt-6">
          <AlertError errors={twoFactorAuth.state.errors} />
        </div>
      {:else}
        <div class="mt-3 space-y-3">
          <div
            bind:this={recoveryCodeSectionRef}
            class="grid gap-1 rounded-lg bg-muted p-4 font-mono text-sm"
          >
            {#if !twoFactorAuth.state.recoveryCodesList.length}
              <div class="space-y-2">
                {#each { length: 8 } as _, n (n)}
                  <div
                    class="h-4 animate-pulse rounded bg-muted-foreground/20"
                  ></div>
                {/each}
              </div>
            {:else}
              {#each twoFactorAuth.state.recoveryCodesList as code, index (index)}
                <div>{code}</div>
              {/each}
            {/if}
          </div>
          <p class="text-xs text-muted-foreground select-none">
            Each recovery code can be used once to access your account and will
            be removed after use. If you need more, click
            <span class="font-bold">Regenerate Codes</span> above.
          </p>
        </div>
      {/if}
    </div>
  </Card.Content>
</Card.Root>
