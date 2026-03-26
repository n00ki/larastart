import { createInertiaApp } from '@inertiajs/svelte';

import { useTheme } from '@/hooks/use-theme.svelte';

useTheme().initialize();

void createInertiaApp({
  pages: './pages',
  progress: {
    color: 'var(--primary)',
  },
});
