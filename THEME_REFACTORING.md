### Theme Management Refactor (Oct 2025)

This document summarizes the theme system improvements made in this session.

### Summary

- Improved SSR safety and pre‑paint behavior to avoid flashes and mismatches
- Centralized cookie key configuration and aligned server/client behavior
- Hardened client theme logic with guards, secure cookies, and color‑scheme
- Added tests covering SSR behavior and cookie handling

### Changes by file

- `resources/js/hooks/use-theme.svelte.ts`
  - Added initialization guard to prevent duplicate listeners.
  - Apply `color-scheme` on `<html>` alongside the `dark` class.
  - Append `;Secure` to the theme cookie when running over HTTPS.
  - New method: `getAppliedMode(): 'light' | 'dark'` to resolve `'system'` to an applied mode.

- `resources/views/app.blade.php`
  - Added `data-theme` attribute to `<html>` with the server‑resolved theme.
  - Rewrote pre‑paint script to be read‑only and minimal:
    - Reads theme from `localStorage` and cookie using a single key.
    - Computes whether to apply dark based on stored value or system preference.
    - Sets both `class="dark"` and `style.colorScheme` pre‑paint.

- `app/Http/Middleware/HandleTheme.php`
  - Reads the theme cookie via `config('app.theme_key', 'theme')`.
  - Shares `theme` with views (defaults to `'system'`).

- `config/app.php`
  - Added: `'theme_key' => env('APP_THEME_KEY', 'theme')`.

- `bootstrap/app.php`
  - Updated cookie encryption configuration. Current setting encrypts all cookies except `'sidebar_state'`.
  - Note: Laravel automatically decrypts incoming cookies; SSR receives the plaintext theme value.

- `tests/Feature/ThemeTest.php`
  - Added feature tests verifying:
    - `<html>` has `class="dark"` when `theme=dark` cookie is present.
    - `<html>` does not have `dark` when `theme=light`.
    - Default `data-theme="system"` when no theme cookie exists.

### Behavior notes

- **Preference vs applied mode**
  - `theme.current` is the user preference: `'light' | 'dark' | 'system'`.
  - `getAppliedMode()` returns the effective mode applied to the DOM: `'light' | 'dark'`.

- **Pre‑paint**
  - The inline script in `app.blade.php` runs before styles load, preventing FOUC and hydration mismatches.
  - It does not write to storage; it only reads and applies.

- **Cookie security**
  - The theme cookie is marked `SameSite=Lax` and `Secure` on HTTPS from the client side.
  - Server configuration currently encrypts cookies by default; Laravel decrypts them on requests.

### Configuration

- Change the cookie/storage key via `.env`:
  - `APP_THEME_KEY=theme`
  - The Blade pre‑paint and middleware both use `config('app.theme_key')`.
  - Client storage key in `use-theme.svelte.ts` currently defaults to `'theme'`.

### API updates (Svelte Theme)

- `initialize()` — idempotent; safe to call once globally (already called in `resources/js/app.ts`).
- `setTheme(mode: 'light' | 'dark' | 'system')`
- `cycleTheme()` — cycles through modes.
- `reset()` — sets preference to `'system'`.
- `getAppliedMode(): 'light' | 'dark'` — resolves `'system'` using `matchMedia`.

### Migration notes

- If you previously relied on reading the theme cookie in plaintext outside Laravel, note that cookies are now encrypted by default. Options:
  - Keep as‑is and read the theme via Laravel (server‑side) or client storage.
  - Or, exclude the theme key from encryption in `bootstrap/app.php` if you need plaintext externally.

### Optional next steps

- Align client storage key with `APP_THEME_KEY` (e.g., read `import.meta.env.VITE_APP_THEME_KEY`) to allow changing the key without code edits.
- Add small e2e/browser checks to validate no FOUC across navigation.
