import type { LinkComponentBaseProps } from '@inertiajs/core';
import type { LucideIcon } from '@lucide/svelte';

type Href = NonNullable<LinkComponentBaseProps['href']>;

export type BreadcrumbItem = {
  title: string;
  href: Href;
};

export type NavItem = {
  title: string;
  href: Href;
  component?: string;
  icon?: LucideIcon;
  isActive?: boolean;
};
