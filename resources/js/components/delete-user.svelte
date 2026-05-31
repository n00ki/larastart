<script lang="ts">
  import { Form } from '@inertiajs/svelte';
  import { toast } from 'svelte-sonner';

  import { cn } from '@/lib/utils';

  import HeadingSmall from '@/components/heading-small.svelte';
  import InputError from '@/components/input-error.svelte';
  import PasswordInput from '@/components/password-input.svelte';
  import { Button, buttonVariants } from '@/components/ui/button';
  import * as Dialog from '@/components/ui/dialog';
  import { Label } from '@/components/ui/label';

  import { destroy } from '@/actions/App/Http/Controllers/User/AccountController';

  const destructiveButtonClass =
    'bg-destructive text-white hover:bg-destructive/90 dark:bg-destructive/60 dark:hover:bg-destructive/70';

  let open = $state(false);
  let passwordInput = $state<HTMLInputElement | null>(null);

  function closeModal() {
    open = false;
  }

  function preventKeyboardDelete(event: KeyboardEvent) {
    if (event.key !== 'Enter') {
      return;
    }

    event.preventDefault();
    toast.info(
      'Mysterious are the ways of the keyboard. Please click the button for confirmation.',
    );
  }
</script>

<div class="space-y-6">
  <HeadingSmall
    title="Delete account"
    description="Delete your account and all of its resources"
  />
  <div
    class="space-y-4 rounded-lg border border-destructive/20 bg-destructive/10 p-4"
  >
    <div class="relative space-y-0.5 text-destructive">
      <p class="font-medium">Warning</p>
      <p class="text-sm">Please proceed with caution, this cannot be undone.</p>
    </div>

    <Dialog.Root bind:open>
      <Dialog.Trigger
        class={cn(
          buttonVariants({ variant: 'destructive' }),
          destructiveButtonClass,
        )}
        data-test="delete-user-button"
      >
        Delete account
      </Dialog.Trigger>

      <Dialog.Content class="sm:max-w-lg">
        <Dialog.Title
          >Are you sure you want to delete your account?</Dialog.Title
        >
        <Dialog.Description>
          Once your account is deleted, all of its resources and data will also
          be permanently deleted. Please enter your password to confirm you
          would like to permanently delete your account.
        </Dialog.Description>
        <Form
          {...destroy.form()}
          options={{
            preserveScroll: true,
          }}
          disableWhileProcessing
          resetOnSuccess
          resetOnError
          onError={() => {
            setTimeout(() => passwordInput?.focus(), 50);
          }}
          onSuccess={closeModal}
          class="space-y-6"
        >
          {#snippet children({ errors, processing, resetAndClearErrors })}
            <div class="grid gap-2">
              <Label for="password" class="sr-only">Password</Label>

              <PasswordInput
                id="password"
                name="password"
                bind:ref={passwordInput}
                placeholder="Password"
                autocomplete="current-password"
                onkeydown={preventKeyboardDelete}
              />

              <InputError message={errors.password} />
            </div>

            <Dialog.Footer class="gap-2">
              <Dialog.Close
                type="button"
                class={buttonVariants({ variant: 'secondary' })}
                onclick={() => resetAndClearErrors()}>Cancel</Dialog.Close
              >

              <Button
                type="submit"
                variant="destructive"
                class={destructiveButtonClass}
                disabled={processing}
                data-test="confirm-delete-user-button"
              >
                Delete account
              </Button>
            </Dialog.Footer>
          {/snippet}
        </Form>
      </Dialog.Content>
    </Dialog.Root>
  </div>
</div>
