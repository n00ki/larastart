<script module lang="ts">
  import AuthLayout from '@/layouts/auth-layout.svelte';

  export const layout = [
    AuthLayout,
    {
      title: 'Confirm your password',
      description:
        'This is a secure area of the application. Please confirm your password before continuing.',
    },
  ];
</script>

<script lang="ts">
  import { Form } from '@inertiajs/svelte';

  import AppHead from '@/components/app-head.svelte';
  import InputError from '@/components/input-error.svelte';
  import PasskeyVerify from '@/components/passkey-verify.svelte';
  import PasswordInput from '@/components/password-input.svelte';
  import { Button } from '@/components/ui/button';
  import { Label } from '@/components/ui/label';
  import { Spinner } from '@/components/ui/spinner';

  import { store } from '@/routes/password/confirm';
</script>

<AppHead title="Confirm your password" />

<Form {...store.form()} resetOnSuccess={['password']}>
  {#snippet children({ errors, processing })}
    <div class="space-y-6">
      <div class="grid gap-2">
        <Label for="password">Password</Label>
        <PasswordInput
          id="password"
          name="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
          placeholder="********"
          autofocus
        />

        <InputError message={errors.password} />
      </div>

      <div class="flex items-center">
        <Button
          type="submit"
          class="w-full"
          disabled={processing}
          data-test="confirm-password-button"
        >
          {#if processing}
            <Spinner />
          {/if}
          Confirm
        </Button>
      </div>

      <div
        class="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border"
      >
        <span class="relative z-10 bg-background px-2 text-muted-foreground">
          Or
        </span>
      </div>

      <PasskeyVerify mode="confirm" />
    </div>
  {/snippet}
</Form>
