# Changelog

All notable changes to this project will be documented in this file.

---

## [v2.1.0] - 2026-04-13

A backward-compatible feature release. No changes are required to existing code.

### Added

- **`NavigationPage::custom(string $routeName)`** - named constructor on the
  `NavigationPage` enum that returns a `CustomNavigationPage` value object.
  Allows actions to navigate to any route registered in the resource's
  `getPages()` array, beyond the built-in `View` and `Edit` cases.

  ```php
  NextRecordAction::make()->navigateTo(NavigationPage::custom('verified-view'))
  ```

- **`CustomNavigationPage`** (`src/Enums/CustomNavigationPage.php`) - a lightweight
  `final` value object that carries an arbitrary route name string in its
  `readonly string $value` property. Shares the same `->value` interface as the
  `NavigationPage` backed enum, so `resolveUrl()` calls `$page->value` on either
  type without any `instanceof` branching.

### Changed

- **`navigateTo()` now accepts `NavigationPage|CustomNavigationPage`** on both
  `NextRecordAction` and `PreviousRecordAction`. Passing a `NavigationPage` enum
  case continues to work exactly as before.

- **`resolveUrl()` type hint updated** in `ResolvesAdjacentRecord` to
  `NavigationPage|CustomNavigationPage`. No behaviour change for existing enum cases.

- **`getRecordNavigationUrl()` signature updated** in `WithRecordNavigation` and
  `HasRecordNavigation` to accept `NavigationPage|CustomNavigationPage` as the
  `$page` parameter. Existing overrides that type-hinted `NavigationPage` alone
  will need the union type added if strict typing / PHPStan is in use.

---

## [v2.0.0] - 2026-03-28

A full rewrite targeting Filament v4 and v5. The architecture has been overhauled
for long-term stability - actions no longer depend on internal Filament hooks and
work out of the box without any trait on the page class.

### Added

- **`NavigationPage` enum** (`View`, `Edit`) - controls which Filament page type
  an action navigates to. Pass it via `->navigateTo(NavigationPage::Edit)` on
  either action. Each action can target a different page type independently.

- **`HasRecordNavigation` contract** (`src/Contracts/HasRecordNavigation.php`) -
  optional interface for page classes that want strict typing, IDE autocompletion,
  or static analysis support (PHPStan / Psalm). Declares `getPreviousRecord()`,
  `getNextRecord()`, and `getRecordNavigationUrl()`.

- **`ResolvesAdjacentRecord` trait** (`src/Actions/Concerns/ResolvesAdjacentRecord.php`) -
  internal trait shared by both actions. Owns the per-render query cache,
  URL resolution logic, and the config-driven fallback query. Eliminates the
  duplicated `defaultAdjacentQuery()` method that previously existed in both
  action classes.

- **Per-render query cache** in `ResolvesAdjacentRecord::getCachedRecord()` -
  each action previously fired 3 separate database queries per render (one each
  for `color()`, `disabled()`, and `url()`). The cache reduces this to 1 query
  per action per render, keyed by direction and `spl_object_id($livewire)`.

- **`$livewire` injection architecture** - actions resolve the current record
  and URL entirely through Filament's official `$livewire` utility injection
  parameter. No internal Filament lifecycle hooks (`configureAction()` etc.) are
  used anywhere, making the package resilient to future Filament upgrades.

### Changed

- **Breaking - Filament version**: minimum supported version is now `^4.0|^5.0`
  (was `^3.0`). PHP minimum is `^8.2` (was `^8.1`).

- **Breaking - trait is now optional**: `WithRecordNavigation` no longer needs
  to be added to every page. `PreviousRecordAction` and `NextRecordAction` work
  without it using a config-driven fallback query. Add the trait only when you
  need to override `getPreviousRecord()`, `getNextRecord()`, or
  `getRecordNavigationUrl()`.

- **Breaking - method renamed**: `getRecordUrl(Model $record)` has been renamed
  to `getRecordNavigationUrl(Model $record, NavigationPage $page)`. The second
  parameter allows the method to know which page type the action is targeting
  and build the correct URL accordingly.

- **Breaking - `configureAction()` removed**: the v1 trait hooked into
  Filament's internal `configureAction()` method to wire up the record query
  and URL. This hook is not part of Filament's public API and is not guaranteed
  to be stable across versions. It has been removed entirely - wiring now happens
  inside the actions themselves via `$livewire` injection.

- **`method_exists()` replaces `instanceof`**: actions now check for navigation
  methods using `method_exists($livewire, 'getPreviousRecord')` instead of
  `$livewire instanceof HasRecordNavigation`. PHP traits do not satisfy
  `instanceof` checks - the class must explicitly declare `implements` for that
  to work. `method_exists()` correctly detects the method regardless of how it
  was added (trait, direct definition, or interface implementation).

- **`getRecordNavigationUrl` replaces `getNavigationUrl`**: the method was
  renamed from `getNavigationUrl` to `getRecordNavigationUrl` because
  Filament's base `Page` class already declares a static `getNavigationUrl()`
  method (used for sidebar navigation). Declaring the same name as non-static
  in a trait caused a fatal PHP error.

- **Default button style**: actions now call `->button()->outlined()` explicitly.
  In Filament v5, `->outlined()` alone does not render correctly without
  `->button()` being called first.

### Fixed

- **Fatal error on page load** (`Cannot make static method Page::getNavigationUrl()
  non static`) - caused by a method name collision with Filament's own
  `getNavigationUrl()` static method on the base `Page` class. Fixed by
  renaming to `getRecordNavigationUrl()` throughout.

- **Custom filter overrides silently ignored** - `getPreviousRecord()` and
  `getNextRecord()` overrides on the page class were never called because the
  `instanceof HasRecordNavigation` check always returned `false` when the
  interface was not explicitly declared. Fixed by switching to `method_exists()`.

- **3× redundant database queries per action per render** - `color()`,
  `disabled()`, and `url()` each independently called the record resolution
  method, resulting in 3 queries per action (6 per page render). Fixed by
  introducing `getCachedRecord()` in `ResolvesAdjacentRecord`.

### Removed

- **Facade** (`FilamentRecordNav`) - was registered in v1 but never provided
  any functionality. Removed to keep the package lean.

- **`configureAction()` hook** - removed from `WithRecordNavigation`. See
  "Changed" above for the rationale.

---

## [v1.0.0] - 2025-05-27

Initial stable release targeting Filament v3.

### Added

- `NextRecordAction` - Filament header action to navigate to the next record
- `PreviousRecordAction` - Filament header action to navigate to the previous record
- `WithRecordNavigation` trait - wires actions to the page via `configureAction()`
- `config/filament-record-nav.php` - configurable `order_column` and sort directions