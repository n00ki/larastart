<script lang="ts">
  import type { BreadcrumbItem } from '@/types';

  import { Form } from '@inertiajs/svelte';
  import { ShieldBan, ShieldCheck } from '@lucide/svelte';
  import { onDestroy } from 'svelte';

  import { twoFactorAuthState } from '@/lib/state/two-factor-auth.svelte';

  import AppLayout from '@/layouts/app-layout.svelte';
  import SettingsLayout from '@/layouts/settings/layout.svelte';

  import AppHead from '@/components/app-head.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';
  import TwoFactorRecoveryCodes from '@/components/two-factor-recovery-codes.svelte';
  import TwoFactorSetupModal from '@/components/two-factor-setup-modal.svelte';
  import { Badge } from '@/components/ui/badge';
  import { Button } from '@/components/ui/button';

  import { disable, enable, show } from '@/routes/two-factor';

  interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
  }

  const { requiresConfirmation = false, twoFactorEnabled = false }: Props =
    $props();

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Two-Factor Authentication',
      href: show().url,
    },
  ];

  const twoFactorAuth = twoFactorAuthState();
  let showSetupModal = $state(false);

  onDestroy(() => {
    twoFactorAuth.clearTwoFactorAuthData();
  });
</script>

<AppHead title="Two-Factor Authentication" />

<AppLayout {breadcrumbs}>
  <h1 class="sr-only">Two-Factor Authentication Settings</h1>

  <SettingsLayout>
    <div class="space-y-6">
      <HeadingSmall
        title="Two-Factor Authentication"
        description="Manage your two-factor authentication settings"
      />

      {#if !twoFactorEnabled}
        <div class="flex flex-col items-start justify-start space-y-4">
          <Badge variant="destructive">Disabled</Badge>

          <p class="text-muted-foreground">
            When you enable two-factor authentication, you will be prompted for
            a secure pin during login. This pin can be retrieved from a
            TOTP-supported application on your phone.
          </p>

          <div>
            {#if twoFactorAuth.hasSetupData()}
              <Button onclick={() => (showSetupModal = true)}>
                <ShieldCheck class="size-4" />Continue Setup
              </Button>
            {:else}
              <Form
                {...enable.form()}
                onSuccess={() => (showSetupModal = true)}
              >
                {#snippet children({ processing })}
                  <Button type="submit" disabled={processing}>
                    <ShieldCheck class="size-4" />Enable 2FA
                  </Button>
                {/snippet}
              </Form>
            {/if}
          </div>
        </div>
      {:else}
        <div class="flex flex-col items-start justify-start space-y-4">
          <Badge variant="default">Enabled</Badge>

          <p class="text-muted-foreground">
            With two-factor authentication enabled, you will be prompted for a
            secure, random pin during login, which you can retrieve from the
            TOTP-supported application on your phone.
          </p>

          <TwoFactorRecoveryCodes />

          <div class="relative inline">
            <Form {...disable.form()}>
              {#snippet children({ processing })}
                <Button
                  variant="destructive"
                  type="submit"
                  disabled={processing}
                >
                  <ShieldBan class="size-4" />
                  Disable 2FA
                </Button>
              {/snippet}
            </Form>
          </div>
        </div>
      {/if}

      <TwoFactorSetupModal
        bind:isOpen={showSetupModal}
        {requiresConfirmation}
        {twoFactorEnabled}
      />
    </div>
  </SettingsLayout>
</AppLayout>
