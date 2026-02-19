<script lang="ts">
  import { Form } from '@inertiajs/svelte';

  import AuthLayout from '@/layouts/auth-layout.svelte';

  import AppHead from '@/components/app-head.svelte';
  import TextLink from '@/components/text-link.svelte';
  import { Button } from '@/components/ui/button';
  import { Spinner } from '@/components/ui/spinner';

  import { logout } from '@/routes';
  import { send } from '@/routes/verification';

  interface Props {
    status?: string;
  }

  const { status = '' }: Props = $props();
</script>

<AppHead title="Email Verification" />

<AuthLayout
  title="Verify email"
  description="Please verify your email address by clicking on the link we just emailed to you."
>
  {#if status === 'verification-link-sent'}
    <div class="mb-4 text-center text-sm font-medium text-green-600">
      A new verification link has been sent to the email address you provided
      during registration.
    </div>
  {/if}

  <Form {...send.form()} class="space-y-6 text-center">
    {#snippet children({ processing })}
      <Button type="submit" disabled={processing} variant="secondary">
        {#if processing}
          <Spinner />
        {/if}
        Resend verification email
      </Button>

      <TextLink href={logout()} as="button" class="mx-auto block text-sm">
        Log out
      </TextLink>
    {/snippet}
  </Form>
</AuthLayout>
