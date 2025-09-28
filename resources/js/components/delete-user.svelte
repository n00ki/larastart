<script lang='ts'>
  import { Form } from '@inertiajs/svelte';

  import { destroy } from '@/actions/App/Http/Controllers/Settings/ProfileController';
  import HeadingSmall from '@/components/heading-small.svelte';
  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import * as Dialog from '@/components/ui/dialog';
  import { Input } from '@/components/ui/input';

  import { Label } from '@/components/ui/label';

  let open = $state(false);
  let passwordInput = $state<HTMLInputElement | null>(null);

  function closeModal() {
    open = false;
  }
</script>

<div class='space-y-6'>
  <HeadingSmall
    title='Delete account'
    description='Delete your account and all of its resources'
  />
  <div
    class='space-y-4 rounded-lg border border-destructive/20 bg-destructive/10 p-4'
  >
    <div class='relative space-y-0.5 text-destructive'>
      <p class='font-medium'>Warning</p>
      <p class='text-sm'>Please proceed with caution, this cannot be undone.</p>
    </div>

    <Dialog.Root bind:open onOpenChange={(isOpen) => !isOpen && closeModal()}>
      <Dialog.Trigger>
        <Button variant='destructive'>Delete account</Button>
      </Dialog.Trigger>
      <Dialog.Content>
        <Dialog.Title
        >Are you sure you want to delete your account?</Dialog.Title
        >
        <Dialog.Description>
          Once your account is deleted, all of its resources and data will also
          be permanently deleted. Please enter your password to confirm you
          would like to permanently delete your account.
        </Dialog.Description>
        <Form
          method='delete'
          action={destroy()}
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
          class='space-y-6'
        >
          {#snippet children({ errors, processing, resetAndClearErrors })}
            <div class='grid gap-2'>
              <Label for='password' class='sr-only'>Password</Label>

              <Input
                id='password'
                type='password'
                name='password'
                bind:ref={passwordInput}
                placeholder='Password'
                autocomplete='current-password'
                onkeydown={(e) => {
                  if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    e.currentTarget.form?.requestSubmit();
                  }
                }}
              />

              <InputError message={errors.password} />
            </div>

            <Dialog.Footer class='gap-2'>
              <Dialog.Close>
                <Button
                  variant='secondary'
                  onclick={() => resetAndClearErrors()}>Cancel</Button
                >
              </Dialog.Close>

              <Button type='submit' variant='destructive' disabled={processing}
              >Delete account</Button
              >
            </Dialog.Footer>
          {/snippet}
        </Form>
      </Dialog.Content>
    </Dialog.Root>
  </div>
</div>
