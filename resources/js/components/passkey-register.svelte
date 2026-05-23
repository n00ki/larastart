<script lang="ts">
  import { router } from '@inertiajs/svelte';
  import { usePasskeyRegister } from '@laravel/passkeys/svelte';

  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Spinner } from '@/components/ui/spinner';

  import { registrationOptions, store } from '@/routes/passkey';

  let name = $state('');
  let showForm = $state(false);
  const passkeyName = $derived(name.trim());

  const passkeyRegister = usePasskeyRegister({
    routes: {
      options: registrationOptions.url(),
      submit: store.url(),
    },
    onSuccess() {
      name = '';
      showForm = false;
      router.reload({ only: ['passkeys'] });
    },
  });

  function cancelRegistration(): void {
    name = '';
    showForm = false;
  }

  function registerPasskey(): void {
    if (passkeyName === '') {
      return;
    }

    void passkeyRegister.register(passkeyName);
  }
</script>

{#if !passkeyRegister.isSupported}
  <p class="text-sm text-muted-foreground">
    This browser or device does not support passkeys.
  </p>
{:else if !showForm}
  <Button variant="outline" onclick={() => (showForm = true)}>
    Add passkey
  </Button>
{:else}
  <div class="space-y-4 rounded-lg border bg-muted/50 p-4">
    <div class="grid gap-2">
      <Label for="passkey-name">Passkey name</Label>
      <Input
        id="passkey-name"
        bind:value={name}
        placeholder="e.g., MacBook Pro, iPhone"
        autocomplete="off"
        disabled={passkeyRegister.isLoading}
        autofocus
      />
      <p class="text-xs text-muted-foreground">
        A name helps you identify this passkey later.
      </p>
      <InputError message={passkeyRegister.error ?? undefined} />
    </div>

    <div class="flex gap-2">
      <Button
        type="button"
        onclick={registerPasskey}
        disabled={passkeyRegister.isLoading || passkeyName === ''}
      >
        {#if passkeyRegister.isLoading}
          <Spinner />
        {/if}
        Register passkey
      </Button>

      <Button
        type="button"
        variant="ghost"
        onclick={cancelRegistration}
        disabled={passkeyRegister.isLoading}
      >
        Cancel
      </Button>
    </div>
  </div>
{/if}
