import type { PageProps as InertiaPageProps } from '@inertiajs/core';
import type { AxiosInstance } from 'axios';
import type { PageProps as AppPageProps } from './';

declare global {
  interface Window {
    axios: AxiosInstance;
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}
