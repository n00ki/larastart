import { page } from '@inertiajs/svelte';

export type Mode = 'light' | 'dark' | 'system';

export class Theme {
  current = $state<Mode>('system');
  private initialized = false;

  private readonly STORAGE_KEY = import.meta.env.VITE_APP_THEME_KEY || 'theme';
  private readonly MODES: Mode[] = ['light', 'dark', 'system'];

  constructor() {
    this.current = this.resolveInitialTheme();
  }

  setTheme(value: Mode): void {
    this.current = value;
    this.persistTheme(value);
    this.applyTheme(value);
  }

  cycleTheme(): void {
    const currentIndex = this.MODES.indexOf(this.current);
    const nextIndex = (currentIndex + 1) % this.MODES.length;
    this.setTheme(this.MODES[nextIndex]);
  }

  initialize(): void {
    if (typeof window === 'undefined' || this.initialized) return;
    this.initialized = true;

    this.current = this.resolveInitialTheme();
    this.applyTheme(this.current);
    this.setupSystemThemeListener();
    this.setupKeyboardListener();
  }

  reset(): void {
    this.setTheme('system');
  }

  get hasInitialized(): boolean {
    return this.initialized;
  }

  get selectedMode(): Mode {
    return this.initialized
      ? this.current
      : (this.getSharedTheme() ?? this.current);
  }

  private getStoredTheme(): Mode | null {
    if (typeof window === 'undefined') return null;

    const stored = localStorage.getItem(this.STORAGE_KEY);

    return this.toMode(stored);
  }

  private getSharedTheme(): Mode | null {
    return this.toMode(page.props.theme);
  }

  private resolveInitialTheme(): Mode {
    return this.getStoredTheme() ?? this.getSharedTheme() ?? 'system';
  }

  private toMode(value: unknown): Mode | null {
    if (typeof value !== 'string') {
      return null;
    }

    return this.MODES.includes(value as Mode) ? (value as Mode) : null;
  }

  private persistTheme(value: Mode): void {
    if (typeof window === 'undefined') return;

    localStorage.setItem(this.STORAGE_KEY, value);
    this.setThemeCookie(this.STORAGE_KEY, value);
  }

  private setThemeCookie(name: string, value: string, days = 365): void {
    if (typeof document === 'undefined') return;

    const maxAge = days * 24 * 60 * 60;
    const secure =
      typeof location !== 'undefined' && location.protocol === 'https:'
        ? ';Secure'
        : '';
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax${secure}`;
  }

  private applyTheme(value: Mode): void {
    if (typeof window === 'undefined') return;

    const el = document.documentElement;
    if (value === 'system') {
      const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      const systemTheme = mediaQuery.matches ? 'dark' : 'light';
      el.classList.toggle('dark', systemTheme === 'dark');
      el.style.colorScheme = systemTheme;
    } else {
      el.classList.toggle('dark', value === 'dark');
      el.style.colorScheme = value;
    }
  }

  getAppliedMode(): 'light' | 'dark' {
    if (typeof window !== 'undefined' && this.current === 'system') {
      return window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
    }
    return this.current === 'dark' ? 'dark' : 'light';
  }

  private setupSystemThemeListener(): void {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

    const handleSystemThemeChange = (): void => {
      if (this.current === 'system') {
        this.applyTheme('system');
      }
    };

    mediaQuery.addEventListener('change', handleSystemThemeChange);

    window.addEventListener('beforeunload', () => {
      mediaQuery.removeEventListener('change', handleSystemThemeChange);
    });
  }

  private setupKeyboardListener(): void {
    const handleKeydown = (e: KeyboardEvent): void => {
      if (
        e.target instanceof HTMLInputElement ||
        e.target instanceof HTMLTextAreaElement ||
        (e.target instanceof HTMLElement && e.target.isContentEditable)
      ) {
        return;
      }

      if (e.key.toLowerCase() === 't') {
        e.preventDefault();
        this.cycleTheme();
      }
    };

    window.addEventListener('keydown', handleKeydown);

    window.addEventListener('beforeunload', () => {
      window.removeEventListener('keydown', handleKeydown);
    });
  }
}

let theme: Theme | null = null;

export function useTheme(): Theme {
  theme ??= new Theme();

  return theme;
}
