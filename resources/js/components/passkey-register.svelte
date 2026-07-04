<script lang="ts">
  import { router } from '@inertiajs/svelte';
  import { usePasskeyRegister } from '@laravel/passkeys/svelte';

  import InputError from '@/components/input-error.svelte';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Spinner } from '@/components/ui/spinner';

  import { registrationOptions, store } from '@/routes/passkey';

  type UserAgentLabel = {
    pattern: RegExp;
    name: string;
  };

  const browsers: UserAgentLabel[] = [
    { pattern: /Edg|Edge/, name: 'Edge' },
    { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
    { pattern: /Firefox|FxiOS/, name: 'Firefox' },
    { pattern: /Chrome|CriOS/, name: 'Chrome' },
    { pattern: /Safari/, name: 'Safari' },
  ];

  const operatingSystems: UserAgentLabel[] = [
    { pattern: /iPhone/, name: 'iPhone' },
    { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
    { pattern: /Android/, name: 'Android' },
    { pattern: /Mac/, name: 'Mac' },
    { pattern: /Windows/, name: 'Windows' },
  ];

  function getDefaultPasskeyName(): string {
    if (typeof navigator === 'undefined') {
      return '';
    }

    const userAgent = navigator.userAgent;
    const browser = browsers.find(({ pattern }) =>
      pattern.test(userAgent),
    )?.name;
    const operatingSystem = operatingSystems.find(({ pattern }) =>
      pattern.test(userAgent),
    )?.name;

    return [browser, operatingSystem].filter(Boolean).join(' on ');
  }

  let name = $state(getDefaultPasskeyName());
  let showForm = $state(false);
  const passkeyName = $derived(name.trim());

  const passkeyRegister = usePasskeyRegister({
    routes: {
      options: registrationOptions.url(),
      submit: store.url(),
    },
    onSuccess() {
      name = getDefaultPasskeyName();
      showForm = false;
      router.reload({ only: ['passkeys'] });
    },
  });

  function startRegistration(): void {
    name = getDefaultPasskeyName();
    showForm = true;
  }

  function cancelRegistration(): void {
    name = getDefaultPasskeyName();
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
  <Button variant="outline" onclick={startRegistration}>Add passkey</Button>
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
