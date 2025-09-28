<script lang='ts'>
  import type { HTMLAttributes } from 'svelte/elements';
  import type { Mode } from '@/hooks/use-theme.svelte';

  import { Monitor, Moon, Sun } from '@lucide/svelte';
  import { fade, fly } from 'svelte/transition';

  import Icon from '@/components/icon.svelte';

  import { Button } from '@/components/ui/button';
  import * as Tooltip from '@/components/ui/tooltip';
  import { useTheme } from '@/hooks/use-theme.svelte';

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

  const currentMode = $derived(
    modes.find((mode) => mode.value === theme.current)!,
  );
  const nextMode = $derived(
    modes[(modes.indexOf(currentMode) + 1) % modes.length],
  );

  const getTransitionProps = () => {
    if (nextMode.value === 'system') {
      return { transition: fade, props: { duration: 200 } };
    } else if (nextMode.value === 'light') {
      return { transition: fly, props: { y: 20, duration: 200 } }; // Sun comes up from below
    } else {
      return { transition: fly, props: { y: -20, duration: 200 } }; // Moon comes down from above
    }
  };
</script>

<div class={className} {...props}>
  <Tooltip.Provider>
    <Tooltip.Root delayDuration={250} disableCloseOnTriggerClick={true}>
      <Tooltip.Trigger onclick={() => theme.cycleTheme()}>
        {#snippet child({ props })}
          <Button
            variant='outline'
            size='icon'
            aria-label={`Switch to ${nextMode.label} theme`}
            class='size-9 rounded-md transition-none'
            {...props}
          >
            {#key nextMode.value}
              {@const { transition, props } = getTransitionProps()}
              <span in:transition={props}>
                <Icon name={nextMode.icon} />
              </span>
            {/key}
            <span class='sr-only'>Switch Theme</span>
          </Button>
        {/snippet}
      </Tooltip.Trigger>
      <Tooltip.Content side={tooltipPosition}>
        <span class='flex items-center gap-1.5 text-xs'>
          <p>switch to {nextMode.label.toLowerCase()} mode</p>
          <p
            class='rounded-sm bg-muted px-1 py-0.5 font-semibold text-foreground'
          >
            T
          </p>
        </span>
      </Tooltip.Content>
    </Tooltip.Root>
  </Tooltip.Provider>
</div>
