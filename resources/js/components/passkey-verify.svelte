<script lang="ts">
  import { router } from '@inertiajs/svelte';
  import { usePasskeyVerify } from '@laravel/passkeys/svelte';
  import { KeyRound } from '@lucide/svelte';

  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Spinner } from '@/components/ui/spinner';

  import {
    confirm,
    confirmOptions,
    login,
    loginOptions,
  } from '@/routes/passkey';

  type Mode = 'login' | 'confirm';

  interface Props {
    mode?: Mode;
    class?: string;
  }

  const { mode = 'login', class: className }: Props = $props();

  // svelte-ignore state_referenced_locally
  const routes =
    mode === 'confirm'
      ? {
          options: confirmOptions.url(),
          submit: confirm.url(),
        }
      : {
          options: loginOptions.url(),
          submit: login.url(),
        };

  const passkeyVerify = usePasskeyVerify({
    routes,
    onSuccess(response) {
      if (response.redirect) {
        router.visit(response.redirect);
      }
    },
  });

  const buttonText = $derived(
    mode === 'confirm' ? 'Confirm with passkey' : 'Log in with passkey',
  );
</script>

<div class={className}>
  <Button
    type="button"
    variant="outline"
    class="w-full"
    disabled={!passkeyVerify.isSupported || passkeyVerify.isLoading}
    onclick={passkeyVerify.verify}
  >
    {#if passkeyVerify.isLoading}
      <Spinner />
    {:else}
      <KeyRound class="size-4" />
    {/if}
    {buttonText}
  </Button>

  <InputError message={passkeyVerify.error ?? undefined} class="mt-2" />
</div>
