import { createInertiaApp } from '@inertiajs/svelte';

import { theme } from '@/hooks/use-theme.svelte';

theme.initialize();

void createInertiaApp({
  pages: './pages',
  progress: {
    color: 'var(--primary)',
  },
});
