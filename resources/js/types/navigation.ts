import type { LinkComponentBaseProps } from '@inertiajs/core';
import type { Icon } from '@lucide/svelte';

export type BreadcrumbItem = {
  title: string;
  href?: string;
};

export type NavItem = {
  title: string;
  href: NonNullable<LinkComponentBaseProps['href']>;
  icon?: typeof Icon;
  isActive?: boolean;
};
