<script lang="ts">
  import { Form } from "@inertiajs/svelte";
  import { LoaderCircle } from "@lucide/svelte";

  import AppHead from "@/components/app-head.svelte";
  import Icon from "@/components/icon.svelte";
  import InputError from "@/components/input-error.svelte";
  import { Button } from "@/components/ui/button";
  import { Input } from "@/components/ui/input";
  import { Label } from "@/components/ui/label";

  import AuthLayout from "@/layouts/auth-layout.svelte";

  import { store } from "@/actions/App/Http/Controllers/Auth/NewPasswordController";

  interface Props {
    token: string;
    email: string;
  }

  const { token, email }: Props = $props();
</script>

<AppHead title="Reset password" />

<AuthLayout title="Reset password" description="Please enter your new password below">
  <Form method="post" action={store()}
        transform={(data) => {
          return {
            ...data,
            token,
            email,
          };
        }}
        resetOnSuccess={["password", "password_confirmation"]}>
    {#snippet children({
      errors,
      processing,
    })}
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="email">Email</Label>
          <Input
            id="email"
            type="email"
            name="email"
            autocomplete="email"
            value={email}
            class="mt-1 block w-full"
            readonly
          />
          <InputError message={errors.email} class="mt-2" />
        </div>

        <div class="grid gap-2">
          <Label for="password">Password</Label>
          <Input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
            class="mt-1 block w-full"
            autofocus
            placeholder="********"
          />
          <InputError message={errors.password} />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation">Confirm Password</Label>
          <Input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            class="mt-1 block w-full"
            placeholder="********"
          />
          <InputError message={errors.password_confirmation} />
        </div>

        <Button type="submit" class="mt-4 w-full" disabled={processing}>
          {#if processing}
            <Icon name={LoaderCircle} class="animate-spin" />
          {/if}
          Reset password
        </Button>
      </div>
    {/snippet}
  </Form>
</AuthLayout>
