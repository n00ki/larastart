import type { LinkComponentBaseProps } from '@inertiajs/core';

import { page } from '@inertiajs/svelte';

import { toUrl } from '@/lib/utils';

type Href = NonNullable<LinkComponentBaseProps['href']>;

export type CurrentUrlState = {
  readonly currentPath: string;
  isCurrentUrl: (urlToCheck: Href) => boolean;
  startsWithCurrentUrl: (urlToCheck: Href) => boolean;
  whenCurrentUrl: <TIfTrue, TIfFalse = null>(
    urlToCheck: Href,
    ifTrue: TIfTrue,
    ifFalse?: TIfFalse,
  ) => TIfTrue | TIfFalse;
};

function resolvePathname(url: Href | string): string | null {
  const origin =
    typeof window === 'undefined' ? 'http://localhost' : window.location.origin;

  try {
    return new URL(typeof url === 'string' ? url : toUrl(url), origin).pathname;
  } catch {
    return null;
  }
}

export function currentUrlState(): CurrentUrlState {
  const currentPath = (): string => resolvePathname(page.url) ?? page.url;

  function isCurrentUrl(urlToCheck: Href): boolean {
    const targetPath = resolvePathname(urlToCheck);

    return targetPath !== null && targetPath === currentPath();
  }

  function startsWithCurrentUrl(urlToCheck: Href): boolean {
    const targetPath = resolvePathname(urlToCheck);

    if (targetPath === null) {
      return false;
    }

    const path = currentPath();

    return path === targetPath || path.startsWith(`${targetPath}/`);
  }

  function whenCurrentUrl<TIfTrue, TIfFalse = null>(
    urlToCheck: Href,
    ifTrue: TIfTrue,
    ifFalse: TIfFalse = null as TIfFalse,
  ): TIfTrue | TIfFalse {
    return isCurrentUrl(urlToCheck) ? ifTrue : ifFalse;
  }

  return {
    get currentPath() {
      return currentPath();
    },
    isCurrentUrl,
    startsWithCurrentUrl,
    whenCurrentUrl,
  };
}
