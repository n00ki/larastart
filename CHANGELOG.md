# Changelog

Headlines: Added, Changed, Deprecated, Removed, Fixed, Security

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.1] - 2026-07-21

### Changed

- Updated dependencies
- Configured pnpm build policies for Git hooks
- Synced the latest official Laravel starter kit refinements
- Made README badges repository-aware

### Fixed

- Playwright version detection in CI after pnpm policy enforcement
- Application branding now respects the configured app name

## [2.1.0] - 2026-07-04

### Added

- Passkey authentication support
- Laravel PAO for improved development workflows
- JSON formatting for stderr logs

### Changed

- Replaced Bun with pnpm as the default package manager
- Updated the default development command to `php artisan dev`
- Refreshed dependencies across the Laravel, Inertia, Svelte, and tooling stack
- Aligned with the latest official Laravel starter kit refinements
- Refreshed shadcn-svelte primitives and polished the application UI
- Improved Svelte, Vite, and SSR tooling alignment with the official starter kit
- Normalized user names consistently across registration, profile updates, and user creation
- Tightened app form requests to reject unexpected fields
- Expanded Larastan coverage to level 7

### Fixed

- Missing login toast after two-factor authentication
- Two-factor setup confirmation feedback
- App layout overscroll background
- pnpm build script in CI
- Keyboard-submitted account deletion

## [2.0.0] - 2026-03-27

### Changed

- Laravel 13
- Inertia 3
- Persisted layouts, instant visits, and more
- Alignment with the latest official starter kits
- DX and UX improvements across the board

## [1.0.0] - 2025-02-20

### Added

- v1.0.0 release 🚀
