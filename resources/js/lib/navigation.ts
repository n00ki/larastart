import type { NavItem } from '@/types';

import { BookOpen, LayoutGrid } from '@lucide/svelte';

import GitHubIcon from '@/components/github-icon.svelte';

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
    icon: GitHubIcon,
  },
  {
    title: 'Documentation',
    href: 'https://github.com/n00ki/larastart#readme',
    icon: BookOpen,
  },
];
