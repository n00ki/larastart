<script module lang="ts">
  import AppLayout from '@/layouts/app-layout.svelte';
  import SettingsLayout from '@/layouts/settings/layout.svelte';

  import { appearance } from '@/routes';

  export const layout = [
    [
      AppLayout,
      {
        breadcrumbs: [
          {
            title: 'Appearance settings',
            href: appearance(),
          },
        ],
      },
    ],
    SettingsLayout,
  ];
</script>

<script lang="ts">
  import AppHead from '@/components/app-head.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';
  import ThemeTabs from '@/components/theme-tabs.svelte';
</script>

<AppHead title="Appearance settings" />

<h1 class="sr-only">Appearance Settings</h1>

<div class="space-y-6">
  <HeadingSmall
    title="Appearance settings"
    description="Customize the look and feel of your application"
  />
  <div class="space-y-4">
    <h3 class="text-lg font-medium">Theme</h3>
    <p class="text-sm text-muted-foreground">
      Choose your preferred color theme
    </p>
    <ThemeTabs />
  </div>
</div>
