<script lang='ts'>
  import { Form } from '@inertiajs/svelte';
  import { LoaderCircle } from '@lucide/svelte';

  import { store } from '@/actions/App/Http/Controllers/Auth/ConfirmablePasswordController';

  import AppHead from '@/components/app-head.svelte';
  import Icon from '@/components/icon.svelte';
  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';

  import AuthLayout from '@/layouts/auth-layout.svelte';
</script>

<AppHead title='Confirm your password' />

<AuthLayout
  title='Confirm your password'
  description='This is a secure area of the application. Please confirm your password before continuing.'
>
  <Form method='post' action={store()} resetOnSuccess={['password']}>
    {#snippet children({ errors, processing })}
      <div class='space-y-6'>
        <div class='grid gap-2'>
          <Label for='password'>Password</Label>
          <Input
            id='password'
            type='password'
            name='password'
            class='mt-1 block w-full'
            required
            autocomplete='current-password'
            placeholder='********'
            autofocus
          />

          <InputError message={errors.password} />
        </div>

        <div class='flex items-center'>
          <Button type='submit' class='w-full' disabled={processing}>
            {#if processing}
              <Icon name={LoaderCircle} class='animate-spin' />
            {/if}
            Confirm
          </Button>
        </div>
      </div>
    {/snippet}
  </Form>
</AuthLayout>
