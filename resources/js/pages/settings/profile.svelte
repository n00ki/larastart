<script lang='ts'>
  import type { BreadcrumbItem } from '@/types';

  import { Form, Link, page } from '@inertiajs/svelte';
  import { fade } from 'svelte/transition';

  import { update } from '@/actions/App/Http/Controllers/Settings/ProfileController';
  import AppHead from '@/components/app-head.svelte';

  import DeleteUser from '@/components/delete-user.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';
  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import AppLayout from '@/layouts/app-layout.svelte';

  import SettingsLayout from '@/layouts/settings/layout.svelte';
  import { edit } from '@/routes/profile';

  interface Props {
    mustVerifyEmail: boolean;
    status?: string;
  }

  const { mustVerifyEmail, status }: Props = $props();

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Profile settings',
      href: edit().url,
    },
  ];

  const user = $derived($page.props.auth.user);
</script>

<AppHead title='Profile settings' />

<AppLayout {breadcrumbs}>
  <SettingsLayout>
    <div class='flex flex-col space-y-6'>
      <HeadingSmall
        title='Profile Information'
        description='Update your name and email address'
      />

      <Form
        method='patch'
        action={update()}
        transform={(data) => ({
          ...data,
          name: data.name || user.name,
          email: data.email || user.email,
        })}
        options={{
          preserveScroll: true,
        }}
        class='space-y-6'
      >
        {#snippet children({ errors, processing, recentlySuccessful })}
          <div class='grid gap-2'>
            <Label for='name'>Name</Label>
            <Input
              id='name'
              name='name'
              class='mt-1 block w-full'
              value={user.name}
              required
              autocomplete='name'
              placeholder='Full name'
            />
            <InputError class='mt-2' message={errors.name} />
          </div>

          <div class='grid gap-2'>
            <Label for='email'>Email address</Label>
            <Input
              id='email'
              name='email'
              type='email'
              class='mt-1 block w-full'
              value={user.email}
              required
              autocomplete='username'
              placeholder='Email address'
            />
            <InputError class='mt-2' message={errors.email} />
          </div>

          {#if mustVerifyEmail && !user.email_verified_at}
            <div>
              <p class='-mt-4 text-sm text-muted-foreground'>
                Your email address is unverified.
                <Link
                  href={route('verification.send')}
                  method='post'
                  as='button'
                  class='
                    text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300
                    ease-out
                    hover:!decoration-current
                    dark:decoration-neutral-500
                  '
                >
                  Click here to resend the verification email.
                </Link>
              </p>

              {#if status === 'verification-link-sent'}
                <div
                  class='mt-2 text-sm font-medium text-green-600 dark:text-green-400'
                >
                  A new verification link has been sent to your email address.
                </div>
              {/if}
            </div>
          {/if}

          <div class='flex items-center gap-4'>
            <Button type='submit' disabled={processing}>Save</Button>

            {#if recentlySuccessful}
              <p
                class='text-sm text-muted-foreground'
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
