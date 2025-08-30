<script lang="ts">
  import type { Mode } from "@/hooks/use-theme.svelte";
  import type { HTMLAttributes } from "svelte/elements";

  import { Monitor, Moon, Sun } from "@lucide/svelte";

  import Icon from "@/components/icon.svelte";
  import { Button } from "@/components/ui/button";
  import * as DropdownMenu from "@/components/ui/dropdown-menu";
  import * as Tooltip from "@/components/ui/tooltip";

  import { useTheme } from "@/hooks/use-theme.svelte";

  interface Props extends HTMLAttributes<HTMLDivElement> {
    tooltipPosition?: "left" | "right" | "top" | "bottom";
    class?: string;
  }

  const { tooltipPosition = "left", class: className, ...props }: Props = $props();

  const theme = useTheme();

  const modes = [
    { value: "light" as Mode, label: "Light", icon: Sun },
    { value: "dark" as Mode, label: "Dark", icon: Moon },
    { value: "system" as Mode, label: "System", icon: Monitor },
  ];

  const currentMode = $derived(modes.find(mode => mode.value === theme.current) || modes[2]);
</script>

<div class={className} {...props}>
  <DropdownMenu.Root>
    <Tooltip.Root ignoreNonKeyboardFocus={true}>
      <Tooltip.Trigger>
        {#snippet child({ props })}
          <DropdownMenu.Trigger>
            {#snippet child({ props: triggerProps })}
              <Button variant="ghost" size="icon" class="size-9 rounded-md" {...props} {...triggerProps}>
                <Icon name={currentMode.icon} />
                <span class="sr-only">Switch Theme</span>
              </Button>
            {/snippet}
          </DropdownMenu.Trigger>
        {/snippet}
      </Tooltip.Trigger>
      <Tooltip.Content side={tooltipPosition}>
        <span class="flex items-center gap-1.5 text-xs">
          <p>switch theme</p>
          <p class="rounded-sm bg-muted px-1 py-0.5 font-semibold text-foreground">T</p>
        </span>
      </Tooltip.Content>
    </Tooltip.Root>
    <DropdownMenu.Content align="end" interactOutsideBehavior="close">
      {#each modes as { value, icon, label } (value)}
        <DropdownMenu.Item onclick={() => theme.setTheme(value)}>
          <span class="flex items-center gap-2">
            <Icon name={icon} />
            {label}
          </span>
        </DropdownMenu.Item>
      {/each}
    </DropdownMenu.Content>
  </DropdownMenu.Root>
</div>
