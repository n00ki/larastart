import '@inertiajs/svelte';

import type { Auth } from './auth';

export * from './auth';
export * from './navigation';

export type AppVariant = 'header' | 'sidebar';

export interface SharedPageProps {
  name: string;
  app_url: string;
  auth: Auth;
  sidebarOpen: boolean;
  theme: 'light' | 'dark' | 'system';
}

export type PageProps<
  T extends Record<string, unknown> = Record<string, never>,
> = SharedPageProps &
  T & {
    [key: string]: unknown;
  };
