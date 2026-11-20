<script lang="ts">
  import { Form, useHttp } from '@inertiajs/svelte';
  import { Eye, EyeOff, LockKeyhole, RefreshCw } from '@lucide/svelte';
  import { onMount, tick } from 'svelte';

  import AlertError from '@/components/alert-error.svelte';
  import { Button } from '@/components/ui/button';
  import * as Card from '@/components/ui/card';

  import { recoveryCodes, regenerateRecoveryCodes } from '@/routes/two-factor';

  const request = useHttp<Record<string, never>, string[]>({});
  let codes = $state<string[]>([]);
  let errors = $state<string[]>([]);
  let visible = $state(false);
  let codesSection = $state<HTMLDivElement | undefined>();

  async function fetchCodes() {
    codes = [];
    errors = [];
    try {
      codes = await request.submit(recoveryCodes());
    } catch {
      errors = ['Failed to fetch recovery codes'];
    }
  }

  async function toggleVisible() {
    visible = !visible;
    if (!visible) {
      return;
    }

    if (!codes.length && !request.processing) {
      fetchCodes();
    }

    await tick();
    codesSection?.scrollIntoView({ behavior: 'smooth' });
  }

  onMount(fetchCodes);
</script>

<Card.Root class="w-full">
  <Card.Header>
    <Card.Title class="flex gap-3">
      <LockKeyhole class="size-4" />2FA Recovery Codes
    </Card.Title>
    <Card.Description>
      Recovery codes let you regain access if you lose your 2FA device. Store
      them in a secure password manager.
    </Card.Description>
  </Card.Header>
  <Card.Content>
    <div
      class="flex flex-col gap-3 select-none sm:flex-row sm:items-center sm:justify-between"
    >
      <Button onclick={toggleVisible} class="w-fit">
        {#if visible}
          <EyeOff class="size-4" />
        {:else}
          <Eye class="size-4" />
        {/if}
        {visible ? 'Hide' : 'View'} Recovery Codes
      </Button>

      <Form
        {...regenerateRecoveryCodes.form()}
        options={{ preserveScroll: true }}
        onSuccess={fetchCodes}
        class="empty:hidden"
      >
        {#snippet children({ processing })}
          {#if visible && codes.length}
            <Button variant="secondary" type="submit" disabled={processing}>
              <RefreshCw class="size-4" /> Regenerate Codes
            </Button>
          {/if}
        {/snippet}
      </Form>
    </div>

    <div
      class="relative overflow-hidden transition-all duration-300 {visible
        ? 'h-auto opacity-100'
        : 'h-0 opacity-0'}"
    >
      {#if errors.length}
        <div class="mt-6">
          <AlertError {errors} />
        </div>
      {:else}
        <div class="mt-3 space-y-3">
          <div
            bind:this={codesSection}
            class="grid gap-1 rounded-lg bg-muted p-4 font-mono text-sm"
          >
            {#if !codes.length}
              <div class="space-y-2">
                {#each { length: 8 } as _, n (n)}
                  <div
                    class="h-4 animate-pulse rounded bg-muted-foreground/20"
                  ></div>
                {/each}
              </div>
            {:else}
              {#each codes as code, index (index)}
                <div>{code}</div>
              {/each}
            {/if}
          </div>
          <p class="text-xs text-muted-foreground select-none">
            Each recovery code can be used once to access your account and will
            be removed after use. If you need more, click
            <span class="font-bold">Regenerate Codes</span> above.
          </p>
        </div>
      {/if}
    </div>
  </Card.Content>
</Card.Root>
