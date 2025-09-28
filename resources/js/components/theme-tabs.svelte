<script lang='ts'>
  import type { HTMLAttributes } from 'svelte/elements';
  import type { Mode } from '@/hooks/use-theme.svelte';

  import { Monitor, Moon, Sun } from '@lucide/svelte';

  import Icon from '@/components/icon.svelte';
  import * as ToggleGroup from '@/components/ui/toggle-group';

  import { useTheme } from '@/hooks/use-theme.svelte';
  import { cn } from '@/lib/utils';

  interface Props extends HTMLAttributes<HTMLDivElement> {
    class?: string;
  }

  const { class: className }: Props = $props();

  const theme = useTheme();

  const modes = [
    { value: 'light' as Mode, label: 'Light', icon: Sun },
    { value: 'dark' as Mode, label: 'Dark', icon: Moon },
    { value: 'system' as Mode, label: 'System', icon: Monitor },
  ];
</script>

<ToggleGroup.Root
  type='single'
  value={theme.current}
  onValueChange={(value) => value && theme.setTheme(value as Mode)}
  class={cn('inline-flex gap-1 rounded-lg bg-muted p-1', className)}
>
  {#each modes as { value, icon, label } (value)}
    <ToggleGroup.Item
      {value}
      class={cn(
        'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
        `text-muted-foreground hover:bg-accent hover:text-accent-foreground`,
        `data-[state=on]:bg-background data-[state=on]:text-foreground data-[state=on]:shadow-sm`,
      )}
    >
      <Icon name={icon} />
      <span class='ml-1.5 text-sm'>{label}</span>
    </ToggleGroup.Item>
  {/each}
</ToggleGroup.Root>
