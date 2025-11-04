<script lang="ts">
  import { Form } from '@inertiajs/svelte';

  import AuthLayout from '@/layouts/auth-layout.svelte';

  import AppHead from '@/components/app-head.svelte';
  import InputError from '@/components/input-error.svelte';
  import TextLink from '@/components/text-link.svelte';
  import { Button } from '@/components/ui/button';
  import { Checkbox } from '@/components/ui/checkbox';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Spinner } from '@/components/ui/spinner';

  import { store } from '@/actions/App/Http/Controllers/Auth/LoginController';
  import { register } from '@/routes';
  import { request } from '@/routes/password';

  interface Props {
    canResetPassword: boolean;
  }

  const { canResetPassword }: Props = $props();
</script>

<AppHead title="Login" />

<AuthLayout
  title="Log in to your account"
  description="Enter your email and password below to log in"
>
  <Form
    method="post"
    action={store()}
    resetOnSuccess={['password']}
    class="flex flex-col gap-6"
  >
    {#snippet children({ errors, processing })}
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="email">Email address</Label>
          <Input
            id="email"
            name="email"
            type="email"
            required
            autofocus
            tabindex={1}
            autocomplete="email"
            placeholder="email@example.com"
          />
          <InputError message={errors.email} />
        </div>

        <div class="grid gap-2">
          <div class="flex items-center justify-between">
            <Label for="password">Password</Label>
            {#if canResetPassword}
              <TextLink href={request()} class="text-sm" tabindex={5}
                >Forgot password?</TextLink
              >
            {/if}
          </div>
          <Input
            id="password"
            name="password"
            type="password"
            required
            tabindex={2}
            autocomplete="current-password"
            placeholder="********"
          />
          <InputError message={errors.password} />
        </div>

        <div class="flex items-center justify-between">
          <Label for="remember" class="flex items-center space-x-3">
            <Checkbox id="remember" name="remember" tabindex={3} />
            <span>Remember me</span>
          </Label>
        </div>

        <Button
          type="submit"
          class="mt-4 w-full"
          tabindex={4}
          disabled={processing}
        >
          {#if processing}
            <Spinner />
          {/if}
          Log in
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Don't have an account?
        <TextLink href={register()} tabindex={5}>Sign up</TextLink>
      </div>
    {/snippet}
  </Form>
</AuthLayout>
