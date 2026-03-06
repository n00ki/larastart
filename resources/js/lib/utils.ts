import type { LinkComponentBaseProps } from '@inertiajs/core';
import type { ClassValue } from 'clsx';

import { clsx } from 'clsx';
import { toast } from 'svelte-sonner';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function toUrl(
  href: NonNullable<LinkComponentBaseProps['href']>,
): string {
  return typeof href === 'string' ? href : href.url;
}

export type FlashMessageType = 'success' | 'error' | 'warning' | 'info';

export type FlashPayload = {
  type?: FlashMessageType;
  message?: string;
};

const flashToasts: Record<
  FlashMessageType,
  (message: string) => string | number
> = {
  success: (message) => toast.success(message),
  error: (message) => toast.error(message),
  warning: (message) => toast.warning(message),
  info: (message) => toast.info(message),
};

export function displayFlashMessage(type: FlashMessageType, message: string) {
  const showToast = flashToasts[type] ?? toast.info;

  showToast(message);
}

export function createFlashToastHandler() {
  let lastDisplayedMessage: string | null = null;
  let lastDisplayedAt = 0;

  return (flash?: FlashPayload | null): void => {
    if (!flash?.type || !flash.message) {
      return;
    }

    const message = flash.message.trim();

    if (message === '') {
      return;
    }

    const currentMessage = `${flash.type}:${message}`;
    const now = Date.now();

    if (
      currentMessage === lastDisplayedMessage &&
      now - lastDisplayedAt < 2_500
    ) {
      return;
    }

    lastDisplayedMessage = currentMessage;
    lastDisplayedAt = now;

    displayFlashMessage(flash.type, message);
  };
}

export type WithoutChild<T> = T extends { child?: any } ? Omit<T, 'child'> : T;
export type WithoutChildren<T> = T extends { children?: any }
  ? Omit<T, 'children'>
  : T;
export type WithoutChildrenOrChild<T> = WithoutChildren<WithoutChild<T>>;
export type WithElementRef<T, U extends HTMLElement = HTMLElement> = T & {
  ref?: U | null;
};
