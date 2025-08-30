export type Mode = "light" | "dark" | "system";

/**
 * Theme management system.
 * Encapsulates all theme-related state and logic within a clean class interface.
 */
export class Theme {
  // State
  current = $state<Mode>("system");

  // Configuration
  private readonly STORAGE_KEY = "theme";
  private readonly MODES: Mode[] = ["light", "dark", "system"];

  constructor() {
    // Initialize with stored theme or default to system
    this.current = this.getStoredTheme() || "system";
  }

  /**
   * Sets the theme and persists it to storage
   */
  setTheme(value: Mode): void {
    this.current = value;
    this.persistTheme(value);
    this.applyTheme(value);
  }

  /**
   * Cycles through available themes: system → light → dark → system
   */
  cycleTheme(): void {
    const currentIndex = this.MODES.indexOf(this.current);
    const nextIndex = (currentIndex + 1) % this.MODES.length;
    this.setTheme(this.MODES[nextIndex]);
  }

  /**
   * Initializes the theme system with proper DOM handling and event listeners
   */
  initialize(): void {
    if (typeof window === "undefined")
return;

    // Apply initial theme
    this.applyTheme(this.current);

    // Handle system theme changes
    this.setupSystemThemeListener();

    // Setup keyboard shortcuts
    this.setupKeyboardListener();
  }

  /**
   * Resets theme to system default
   */
  reset(): void {
    this.setTheme("system");
  }

  // Private methods
  private getStoredTheme(): Mode | null {
    if (typeof window === "undefined")
return null;
    const stored = localStorage.getItem(this.STORAGE_KEY);
    return this.MODES.includes(stored as Mode) ? (stored as Mode) : null;
  }

  private persistTheme(value: Mode): void {
    if (typeof window === "undefined")
return;

    // Persist to localStorage for client-side sessions
    localStorage.setItem(this.STORAGE_KEY, value);

    // Persist to cookie for SSR
    this.setThemeCookie(this.STORAGE_KEY, value);
  }

  private setThemeCookie(name: string, value: string, days = 365): void {
    if (typeof document === "undefined")
return;

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
  }

  private applyTheme(value: Mode): void {
    if (typeof window === "undefined")
return;

    if (value === "system") {
      const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
      const systemTheme = mediaQuery.matches ? "dark" : "light";
      document.documentElement.classList.toggle("dark", systemTheme === "dark");
    }
 else {
      document.documentElement.classList.toggle("dark", value === "dark");
    }
  }

  private setupSystemThemeListener(): void {
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

    const handleSystemThemeChange = (): void => {
      if (this.current === "system") {
        this.applyTheme("system");
      }
    };

    mediaQuery.addEventListener("change", handleSystemThemeChange);

    // Cleanup on page unload
    window.addEventListener("beforeunload", () => {
      mediaQuery.removeEventListener("change", handleSystemThemeChange);
    });
  }

  private setupKeyboardListener(): void {
    const handleKeydown = (e: KeyboardEvent): void => {
      // Skip if user is typing in input fields
      if (
        e.target instanceof HTMLInputElement
        || e.target instanceof HTMLTextAreaElement
        || (e.target instanceof HTMLElement && e.target.isContentEditable)
      ) {
        return;
      }

      if (e.key.toLowerCase() === "t") {
        e.preventDefault();
        this.cycleTheme();
      }
    };

    window.addEventListener("keydown", handleKeydown);

    // Cleanup on page unload
    window.addEventListener("beforeunload", () => {
      window.removeEventListener("keydown", handleKeydown);
    });
  }
}

/**
 * Global theme instance - singleton pattern for consistent state
 */
export const theme = new Theme();

/**
 * Hook for accessing theme in components
 */
export function useTheme() {
  return theme;
}
