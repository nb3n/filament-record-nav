# Changelog

## [Unreleased]

### Added

- `getRecordNavigationHeaderActions()` for **Filament v4** (header actions are not passed through `configureAction()`).
- Config: `rtl_locales`, `url_route`, `previous_tooltip_key`, `next_tooltip_key`.
- `getRecordNavigationUrlRoute()` hook for edit vs view pages.
- Record URLs use `getRouteKey()` (UUID / custom route keys) while `order_column` still drives adjacency (e.g. `id`).

### Changed

- `configureAction()` fall back: call `parent::configureAction()` only when the method exists (Filament v4 compatibility).
- `getAdjacentRecord()` uses `getAttribute($orderColumn)` and skips when value is null.
- `composer.json`: require `filament/filament` `^3.2 | ^4.0`.

## [v1.0.0] - 2025-05-27
### Added
- `NextRecordAction` Filament action
- `PreviousRecordAction` Filament action  
- `WithRecordNavigation` trait
- Config file (`config/filament-record-nav.php`)

### Changed
- Initial stable release