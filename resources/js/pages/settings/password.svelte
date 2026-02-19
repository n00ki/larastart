<script lang="ts">
  import type { BreadcrumbItem } from '@/types';

  import { Form } from '@inertiajs/svelte';
  import { fade } from 'svelte/transition';

  import AppLayout from '@/layouts/app-layout.svelte';
  import SettingsLayout from '@/layouts/settings/layout.svelte';

  import AppHead from '@/components/app-head.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';
  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';

  import PasswordController from '@/actions/App/Http/Controllers/User/PasswordController';
  import { edit } from '@/routes/password';

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Password settings',
      href: edit().url,
    },
  ];
</script>

<AppHead title="Password settings" />

<AppLayout {breadcrumbs}>
  <h1 class="sr-only">Password Settings</h1>

  <SettingsLayout>
    <div class="space-y-6">
      <HeadingSmall
        title="Update Password"
        description="Ensure your account is using a long, random password to stay secure"
      />

      <Form
        {...PasswordController.update.form()}
        options={{ preserveScroll: true }}
        resetOnSuccess
        resetOnError={['password', 'password_confirmation', 'current_password']}
        class="space-y-6"
      >
        {#snippet children({ errors, processing, recentlySuccessful })}
          <div class="grid gap-2">
            <Label for="current_password">Current password</Label>
            <Input
              id="current_password"
              name="current_password"
              type="password"
              class="mt-1 block w-full"
              autocomplete="current-password"
              placeholder="Current password"
            />
            <InputError message={errors.current_password} />
          </div>

          <div class="grid gap-2">
            <Label for="password">New password</Label>
            <Input
              id="password"
              name="password"
              type="password"
              class="mt-1 block w-full"
              autocomplete="new-password"
              placeholder="New password"
            />
            <InputError message={errors.password} />
          </div>

          <div class="grid gap-2">
            <Label for="password_confirmation">Confirm password</Label>
            <Input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
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
  </SettingsLayout>
</AppLayout>
