import { createInertiaApp } from '@inertiajs/svelte';

import { theme } from '@/hooks/use-theme.svelte';

import '../css/app.css';

theme.initialize();

void createInertiaApp({
  pages: './pages',
  progress: {
    color: 'var(--primary)',
  },
});
