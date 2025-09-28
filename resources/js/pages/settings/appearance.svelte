<script lang='ts'>
  import type { BreadcrumbItem } from '@/types';

  import AppHead from '@/components/app-head.svelte';
  import HeadingSmall from '@/components/heading-small.svelte';

  import ThemeTabs from '@/components/theme-tabs.svelte';
  import AppLayout from '@/layouts/app-layout.svelte';
  import SettingsLayout from '@/layouts/settings/layout.svelte';

  import { appearance } from '@/routes';

  const breadcrumbItems: BreadcrumbItem[] = [
    {
      title: 'Appearance settings',
      href: appearance().url,
    },
  ];
</script>

<AppHead title='Appearance settings' />

<AppLayout breadcrumbs={breadcrumbItems}>
  <SettingsLayout>
    <div class='space-y-6'>
      <HeadingSmall
        title='Appearance settings'
        description='Customize the look and feel of your application'
      />
      <div class='space-y-4'>
        <h3 class='text-lg font-medium'>Theme</h3>
        <p class='text-sm text-muted-foreground'>
          Choose your preferred color theme
        </p>
        <ThemeTabs />
      </div>
    </div>
  </SettingsLayout>
</AppLayout>
