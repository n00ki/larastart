import type { PageProps as InertiaPageProps } from '@inertiajs/core';
import type { SharedPageProps } from './';

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, SharedPageProps {}

  interface InertiaConfig {
    sharedPageProps: SharedPageProps;
    flashDataType: {
      type?: 'success' | 'error' | 'warning' | 'info';
      message?: string;
    };
  }
}
