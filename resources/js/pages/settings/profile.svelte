<script lang="ts">
  import type { BreadcrumbItem } from '@/types';

  import { Form, page } from '@inertiajs/svelte';
  import { fade } from 'svelte/transition';

  import AppLayout from '@/layouts/app-layout.svelte';
  import SettingsLayout from '@/layouts/settings/layout.svelte';

  import AppHead from '@/components/app-head.svelte';
  import DeleteUser from '@/components/delete-user.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';
  import InputError from '@/components/input-error.svelte';
  import TextLink from '@/components/text-link.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';

  import ProfileController from '@/actions/App/Http/Controllers/User/ProfileController';
  import { edit } from '@/routes/profile';
  import { send } from '@/routes/verification';

  interface Props {
    mustVerifyEmail: boolean;
    status?: string;
  }

  const { mustVerifyEmail, status = '' }: Props = $props();

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Profile settings',
      href: edit().url,
    },
  ];

  const user = $derived($page.props.auth.user);
</script>

<AppHead title="Profile settings" />

<AppLayout {breadcrumbs}>
  <h1 class="sr-only">Profile Settings</h1>

  <SettingsLayout>
    <div class="flex flex-col space-y-6">
      <HeadingSmall
        title="Profile Information"
        description="Update your name and email address"
      />

      <Form
        {...ProfileController.update.form()}
        options={{ preserveScroll: true }}
        class="space-y-6"
      >
        {#snippet children({ errors, processing, recentlySuccessful })}
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input
              id="name"
              name="name"
              class="mt-1 block w-full"
              value={user.name}
              required
              autocomplete="name"
              placeholder="Full name"
            />
            <InputError class="mt-2" message={errors.name} />
          </div>

          <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input
              id="email"
              name="email"
              type="email"
              class="mt-1 block w-full"
              value={user.email}
              required
              autocomplete="username"
              placeholder="Email address"
            />
            <InputError class="mt-2" message={errors.email} />
          </div>

          {#if mustVerifyEmail && !user.email_verified_at}
            <div>
              <p class="-mt-4 text-sm text-muted-foreground">
                Your email address is unverified.
                <TextLink href={send()} method="post" as="button">
                  Click here to resend the verification email.
                </TextLink>
              </p>

              {#if status === 'verification-link-sent'}
                <div class="mt-2 text-sm font-medium text-green-600">
                  A new verification link has been sent to your email address.
                </div>
              {/if}
            </div>
          {/if}

          <div class="flex items-center gap-4">
            <Button
              type="submit"
              disabled={processing}
              data-test="update-profile-button"
            >
              Save
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

    <DeleteUser />
  </SettingsLayout>
</AppLayout>
