import type { ResolvedComponent } from '@inertiajs/svelte';

import { createInertiaApp } from '@inertiajs/svelte';
import { hydrate, mount } from 'svelte';

import { theme } from '@/hooks/use-theme.svelte';

import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  progress: {
    color: 'var(--primary)',
  },
  resolve: (name: string) => {
    const pages = import.meta.glob<ResolvedComponent>('./pages/**/*.svelte', {
      eager: true,
    });
    return pages[`./pages/${name}.svelte`];
  },
  setup({ el, App, props }) {
    if (!el) return;
    theme.initialize();

    if (el.dataset.serverRendered === 'true') {
      hydrate(App, { target: el, props });
    } else {
      mount(App, { target: el, props });
    }
  },
});
