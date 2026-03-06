<script lang="ts">
  import type { Mode } from '@/hooks/use-theme.svelte';
  import type { HTMLAttributes } from 'svelte/elements';

  import { Monitor, Moon, Sun } from '@lucide/svelte';
  import { fade, fly } from 'svelte/transition';

  import { useTheme } from '@/hooks/use-theme.svelte';
  import { cn } from '@/lib/utils';

  import Icon from '@/components/icon.svelte';
  import { Button } from '@/components/ui/button';
  import * as Kbd from '@/components/ui/kbd';
  import * as Tooltip from '@/components/ui/tooltip';

  interface Props extends HTMLAttributes<HTMLDivElement> {
    tooltipPosition?: 'left' | 'right' | 'top' | 'bottom';
    class?: string;
  }

  let {
    tooltipPosition = 'left',
    class: className = '',
    ...props
  }: Props = $props();

  const theme = useTheme();

  const modes = [
    { value: 'light' as Mode, label: 'Light', icon: Sun },
    { value: 'dark' as Mode, label: 'Dark', icon: Moon },
    { value: 'system' as Mode, label: 'System', icon: Monitor },
  ];

  const modesByValue = Object.fromEntries(
    modes.map((mode) => [mode.value, mode]),
  ) as Record<Mode, (typeof modes)[number]>;

  const nextModeByValue: Record<Mode, Mode> = {
    light: 'dark',
    dark: 'system',
    system: 'light',
  };

  const transitionByMode = {
    system: { transition: fade, props: { duration: 200 } },
    light: { transition: fly, props: { y: 20, duration: 200 } },
    dark: { transition: fly, props: { y: -20, duration: 200 } },
  };

  const nextMode = $derived(modesByValue[nextModeByValue[theme.current]]);
</script>

<div
  class={cn(
    'rounded-lg border border-border bg-background shadow-sm',
    className,
  )}
  {...props}
>
  <Tooltip.Provider>
    <Tooltip.Root delayDuration={250} disableCloseOnTriggerClick={true}>
      <Tooltip.Trigger onclick={() => theme.cycleTheme()}>
        {#snippet child({ props })}
          <Button
            variant="outline"
            size="icon"
            aria-label={`Switch to ${nextMode.label} theme`}
            class="size-9 rounded-md transition-none"
            {...props}
          >
            {#key nextMode.value}
              {@const { transition, props: transitionProps } =
                transitionByMode[nextMode.value]}
              <span in:transition={transitionProps}>
                <Icon name={nextMode.icon} />
              </span>
            {/key}
            <span class="sr-only">Switch Theme</span>
          </Button>
        {/snippet}
      </Tooltip.Trigger>
      <Tooltip.Content side={tooltipPosition}>
        <span class="flex items-center gap-1.5 text-xs">
          <p>switch to {nextMode.label.toLowerCase()} mode</p>
          <Kbd.Root>T</Kbd.Root>
        </span>
      </Tooltip.Content>
    </Tooltip.Root>
  </Tooltip.Provider>
</div>
