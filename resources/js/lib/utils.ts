import type { ClassValue } from 'clsx';

import { clsx } from 'clsx';
import { toast } from 'svelte-sonner';
import { twMerge } from 'tailwind-merge';

/**
 * A utility function to merge Tailwind classes with clsx.
 * @param inputs The classes to merge.
 * @returns The merged classes.
 */
export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/**
 * A utility type to remove the `child` prop from a type.
 * @template T The type to remove the `child` prop from.
 */
export type WithoutChild<T> = T extends { child?: any } ? Omit<T, 'child'> : T;

/**
 * A utility type to remove the `children` prop from a type.
 * @template T The type to remove the `children` prop from.
 */
export type WithoutChildren<T> = T extends { children?: any }
  ? Omit<T, 'children'>
  : T;

/**
 * A utility type to remove the `children` and `child` props from a type.
 * @template T The type to remove the `children` and `child` props from.
 */
export type WithoutChildrenOrChild<T> = WithoutChildren<WithoutChild<T>>;

/**
 * A utility type to add a `ref` prop to a type.
 * @template T The type to add the `ref` prop to.
 * @template U The type of the `ref`.
 */
export type WithElementRef<T, U extends HTMLElement = HTMLElement> = T & {
  ref?: U | null;
};

/**
 * A utility function to display a flash message.
 * @param type The type of the flash message.
 * @param message The message to display.
 */
export function displayFlashMessage(
  type: 'success' | 'error' | 'warning' | 'info',
  message: string,
) {
  switch (type) {
    case 'success':
      toast.success(message);
      break;
    case 'error':
      toast.error(message);
      break;
    case 'warning':
      toast.warning(message);
      break;
    case 'info':
      toast.info(message);
      break;
    default:
      toast.info(message);
      break;
  }
}
