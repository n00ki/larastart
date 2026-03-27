import type { NavItem } from '@/types';

import { BookOpen, Folder, LayoutGrid } from '@lucide/svelte';

import { dashboard } from '@/routes';

export const mainNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
    component: 'dashboard',
    icon: LayoutGrid,
  },
];

export const secondaryNavItems: NavItem[] = [
  {
    title: 'Repository',
    href: 'https://github.com/n00ki/larastart',
    icon: Folder,
  },
  {
    title: 'Documentation',
    href: 'https://github.com/n00ki/larastart#readme',
    icon: BookOpen,
  },
];
