<script module lang="ts">
  import AppLayout from '@/layouts/app-layout.svelte';
  import SettingsLayout from '@/layouts/settings/layout.svelte';

  import { edit } from '@/routes/security';

  export const layout = [
    [
      AppLayout,
      {
        breadcrumbs: [
          {
            title: 'Security settings',
            href: edit(),
          },
        ],
      },
    ],
    SettingsLayout,
  ];
</script>

<script lang="ts">
  import { Form } from '@inertiajs/svelte';
  import { ShieldCheck } from '@lucide/svelte';
  import { onDestroy } from 'svelte';
  import { fade } from 'svelte/transition';

  import { twoFactorAuthState } from '@/lib/state/two-factor-auth.svelte';

  import AppHead from '@/components/app-head.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';
  import InputError from '@/components/input-error.svelte';
  import PasswordInput from '@/components/password-input.svelte';
  import TwoFactorRecoveryCodes from '@/components/two-factor-recovery-codes.svelte';
  import TwoFactorSetupModal from '@/components/two-factor-setup-modal.svelte';
  import { Button } from '@/components/ui/button';
  import { Label } from '@/components/ui/label';

  import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
  import { disable, enable } from '@/routes/two-factor';

  interface Props {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
  }

  const {
    canManageTwoFactor = false,
    requiresConfirmation = false,
    twoFactorEnabled = false,
  }: Props = $props();

  const twoFactorAuth = twoFactorAuthState();
  let showSetupModal = $state(false);

  onDestroy(() => {
    twoFactorAuth.clearTwoFactorAuthData();
  });
</script>

<AppHead title="Security settings" />

<h1 class="sr-only">Security Settings</h1>

<div class="space-y-6">
  <HeadingSmall
    title="Update Password"
    description="Ensure your account is using a long, random password to stay secure"
  />

  <Form
    {...SecurityController.update.form()}
    options={{ preserveScroll: true }}
    resetOnSuccess
    resetOnError={['password', 'password_confirmation', 'current_password']}
    class="space-y-6"
  >
    {#snippet children({ errors, processing, recentlySuccessful })}
      <div class="grid gap-2">
        <Label for="current_password">Current password</Label>
        <PasswordInput
          id="current_password"
          name="current_password"
          class="mt-1 block w-full"
          autocomplete="current-password"
          placeholder="Current password"
        />
        <InputError message={errors.current_password} />
      </div>

      <div class="grid gap-2">
        <Label for="password">New password</Label>
        <PasswordInput
          id="password"
          name="password"
          class="mt-1 block w-full"
          autocomplete="new-password"
          placeholder="New password"
        />
        <InputError message={errors.password} />
      </div>

      <div class="grid gap-2">
        <Label for="password_confirmation">Confirm password</Label>
        <PasswordInput
          id="password_confirmation"
          name="password_confirmation"
          class="mt-1 block w-full"
          autocomplete="new-password"
          placeholder="Confirm password"
        />
        <InputError message={errors.password_confirmation} />
      </div>

      <div class="flex items-center gap-4">
        <Button
          type="submit"
          disabled={processing}
          data-test="update-password-button"
        >
          Save password
        </Button>

        {#if recentlySuccessful}
          <p
            class="text-sm text-muted-foreground"
            transition:fade={{ duration: 150 }}
          >
            Saved.
          </p>
        {/if}
      </div>
    {/snippet}
  </Form>
</div>

{#if canManageTwoFactor}
  <div class="space-y-6">
    <HeadingSmall
      title="Two-Factor Authentication"
      description="Manage your two-factor authentication settings"
    />

    {#if !twoFactorEnabled}
      <div class="flex flex-col items-start justify-start space-y-4">
        <p class="text-sm text-muted-foreground">
          When you enable two-factor authentication, you will be prompted for a
          secure pin during login. This pin can be retrieved from a
          TOTP-supported application on your phone.
        </p>

        <div>
          {#if twoFactorAuth.hasSetupData()}
            <Button onclick={() => (showSetupModal = true)}>
              <ShieldCheck class="size-4" />Continue Setup
            </Button>
          {:else}
            <Form {...enable.form()} onSuccess={() => (showSetupModal = true)}>
              {#snippet children({ processing })}
                <Button type="submit" disabled={processing}>Enable 2FA</Button>
              {/snippet}
            </Form>
          {/if}
        </div>
      </div>
    {:else}
      <div class="flex flex-col items-start justify-start space-y-4">
        <p class="text-sm text-muted-foreground">
          You will be prompted for a secure, random pin during login, which you
          can retrieve from the TOTP-supported application on your phone.
        </p>

        <div class="relative inline">
          <Form {...disable.form()}>
            {#snippet children({ processing })}
              <Button variant="destructive" type="submit" disabled={processing}>
                Disable 2FA
              </Button>
            {/snippet}
          </Form>
        </div>

        <TwoFactorRecoveryCodes />
      </div>
    {/if}

    <TwoFactorSetupModal
      bind:isOpen={showSetupModal}
      {requiresConfirmation}
      {twoFactorEnabled}
    />
  </div>
{/if}
