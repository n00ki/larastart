import type { LinkComponentBaseProps } from '@inertiajs/core';
import type { Icon } from '@lucide/svelte';

type Href = NonNullable<LinkComponentBaseProps['href']>;

export type BreadcrumbItem = {
  title: string;
  href: Href;
};

export type NavItem = {
  title: string;
  href: Href;
  icon?: typeof Icon;
  isActive?: boolean;
};
