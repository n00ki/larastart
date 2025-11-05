import '@inertiajs/svelte';

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
  name: string;
  auth: Auth;
  flash?: {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
  };
  [key: string]: unknown;
};

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: any;
  isActive?: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
