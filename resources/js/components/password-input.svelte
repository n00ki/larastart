<script lang="ts">
  import type { WithElementRef } from '@/lib/utils';
  import type { HTMLInputAttributes } from 'svelte/elements';

  import { Eye, EyeOff } from '@lucide/svelte';

  import { cn } from '@/lib/utils';

  import { Input } from '@/components/ui/input';

  type Props = WithElementRef<Omit<HTMLInputAttributes, 'type' | 'files'>>;

  let { ref = $bindable(null), class: className, ...props }: Props = $props();

  let showPassword = $state(false);
</script>

<div class="relative">
  <Input
    bind:ref
    type={showPassword ? 'text' : 'password'}
    class={cn('pr-10', className)}
    {...props}
  />

  <button
    type="button"
    class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-3 text-muted-foreground transition-[color,box-shadow] outline-none hover:text-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
    aria-label={showPassword ? 'Hide password' : 'Show password'}
    tabindex={-1}
    onclick={() => (showPassword = !showPassword)}
  >
    {#if showPassword}
      <EyeOff class="size-4" />
    {:else}
      <Eye class="size-4" />
    {/if}
  </button>
</div>
