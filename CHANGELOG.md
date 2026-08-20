# Changelog

Headlines: Added, Changed, Deprecated, Removed, Fixed, Security

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.0] - 2026-08-20

### Changed

- Upgraded Pest and its plugins to version 5, including the agent and PHPStan plugins
- Made Test Impact Analysis opt-in through `composer test:tia` while keeping the default test suite uncached
- Made native parameter, property, and return types the source of truth for application code, with PHPDoc reserved for contracts PHP cannot express
- Kept 100% type coverage and Larastan level 7 while removing redundant annotations that added noise and could become stale
- Moved generated Eloquent metadata outside application source
- Expanded pre-commit PHPStan analysis to cover tests
- Updated dependencies
- Refreshed README

### Removed

- Stale Rector skip rule for adding `#[Override]` attributes

## [2.2.2] - 2026-08-14

### Added

- Laravel Multiplex for tabbed `php artisan dev` process management on macOS and Linux

### Changed

- Updated dependencies

### Removed

- Redundant `composer dev:ssr` workflow now that Inertia's Vite plugin handles development SSR automatically

## [2.2.1] - 2026-08-07

### Changed

- Enabled metric-adjusted font fallbacks to reduce layout shift

### Fixed

- Vite configuration compatibility with the forthcoming native config loader
- Intermittent Pest type-coverage cache corruption in CI
- CI hangs caused by leaked Playwright server processes

## [2.2.0] - 2026-08-07

### Added

- Codex integration for Laravel Boost
- Convention inference support for project-specific agent guidance
- Monthly log rotation channel

### Changed

- Aligned registration and password reset mutations with Fortify's runtime contracts
- Marked Fortify credential payloads as sensitive
- Updated dependencies and GitHub Actions
- Synced the latest official Laravel starter kit refinements

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
