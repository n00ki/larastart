import '@inertiajs/svelte';

export * from './auth';
export * from './navigation';

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
  name: string;
  auth: import('./auth').Auth;
  [key: string]: unknown;
};
