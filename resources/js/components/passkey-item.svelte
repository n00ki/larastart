<script lang="ts">
  import type { Passkey } from '@/types/auth';

  import { Form } from '@inertiajs/svelte';
  import { KeyRound, Trash2 } from '@lucide/svelte';

  import { Button } from '@/components/ui/button';
  import * as Dialog from '@/components/ui/dialog';

  import { destroy } from '@/routes/passkey';

  interface Props {
    passkey: Passkey;
  }

  const { passkey }: Props = $props();
  let isDeleteDialogOpen = $state(false);
</script>

<div
  class="flex items-center justify-between gap-4 border-b p-4 last:border-b-0"
>
  <div class="flex min-w-0 items-center gap-4">
    <div
      class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-muted"
    >
      <KeyRound class="size-5 text-muted-foreground" />
    </div>
    <div class="min-w-0 space-y-1">
      <div class="flex items-center gap-2.5">
        <p class="truncate font-medium tracking-tight">{passkey.name}</p>
        {#if passkey.authenticator}
          <span
            class="inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium tracking-wide text-muted-foreground uppercase ring-1 ring-border ring-inset"
          >
            {passkey.authenticator}
          </span>
        {/if}
      </div>
      <p class="text-sm text-muted-foreground">
        Added {passkey.created_at_diff}
        {#if passkey.last_used_at_diff}
          <span class="mx-1 text-muted-foreground/50">/</span>
          Last used {passkey.last_used_at_diff}
        {/if}
      </p>
    </div>
  </div>

  <Button
    type="button"
    variant="ghost"
    size="sm"
    class="text-destructive hover:bg-destructive/10 hover:text-destructive"
    onclick={() => (isDeleteDialogOpen = true)}
  >
    <Trash2 class="size-4" />
    <span class="sr-only">Remove</span>
  </Button>

  <Dialog.Root bind:open={isDeleteDialogOpen}>
    <Dialog.Content>
      <Dialog.Title>Remove passkey</Dialog.Title>
      <Dialog.Description>
        Are you sure you want to remove the "{passkey.name}" passkey? You will
        no longer be able to use it to sign in.
      </Dialog.Description>

      <Form
        {...destroy.form.delete(passkey.id)}
        options={{ preserveScroll: true }}
        onSuccess={() => (isDeleteDialogOpen = false)}
      >
        {#snippet children({ processing })}
          <Dialog.Footer class="gap-2">
            <Button
              type="button"
              variant="secondary"
              onclick={() => (isDeleteDialogOpen = false)}
            >
              Cancel
            </Button>

            <Button type="submit" variant="destructive" disabled={processing}>
              Remove passkey
            </Button>
          </Dialog.Footer>
        {/snippet}
      </Form>
    </Dialog.Content>
  </Dialog.Root>
</div>
