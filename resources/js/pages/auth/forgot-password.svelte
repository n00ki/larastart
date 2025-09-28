<script lang='ts'>
  import { Form } from '@inertiajs/svelte';
  import { LoaderCircle } from '@lucide/svelte';

  import { store } from '@/actions/App/Http/Controllers/Auth/PasswordResetLinkController';

  import AppHead from '@/components/app-head.svelte';
  import Icon from '@/components/icon.svelte';
  import InputError from '@/components/input-error.svelte';
  import TextLink from '@/components/text-link.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';

  import AuthLayout from '@/layouts/auth-layout.svelte';
  import { login } from '@/routes';
</script>

<AppHead title='Forgot password' />

<AuthLayout
  title='Forgot password'
  description='Enter your email to receive a password reset link'
>
  <div class='space-y-6'>
    <Form method='post' action={store()}>
      {#snippet children({ errors, processing })}
        <div class='grid gap-2'>
          <Label for='email'>Email address</Label>
          <Input
            id='email'
            type='email'
            name='email'
            autocomplete='off'
            autofocus
            placeholder='email@example.com'
          />
          <InputError message={errors.email} />
        </div>

        <div class='my-6 flex items-center justify-start'>
          <Button type='submit' class='w-full' disabled={processing}>
            {#if processing}
              <Icon name={LoaderCircle} class='animate-spin' />
            {/if}
            Email password reset link
          </Button>
        </div>
      {/snippet}
    </Form>

    <div class='space-x-1 text-center text-sm text-muted-foreground'>
      <span>Or, return to</span>
      <TextLink href={login()} tabindex={3}>log in</TextLink>
    </div>
  </div>
</AuthLayout>
