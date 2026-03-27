# Filament Record Navigation

A Laravel package that adds elegant next/previous record navigation to your Filament PHP admin panels. Navigate seamlessly between records with intuitive navigation buttons.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/php-8.2-blue)
![Laravel](https://img.shields.io/badge/Laravel-11.x%20|%2012.x%20|%2013.x-red)
![Filament](https://img.shields.io/badge/Filament-4.x%20|%205.x-orange)

## Features

- **Zero-config integration** - drop two actions into `getHeaderActions()` and you're done
- **No trait required** - actions work out of the box without any page class changes
- **Filament native** - extends Filament's `Action` class, all fluent methods work
- **Configurable** - set ordering column and direction globally via config
- **Fully overridable** - add the trait and override any method for custom query logic
- **Page-type aware** - choose `view` or `edit` as the target page per action
- **Future-proof** - depends only on Filament's stable `$livewire` injection API
- **Smart boundaries** - buttons auto-disable and turn gray at the first/last record
- **Optimised queries** - each action fires exactly one database query per render

## Requirements

- PHP `^8.2`
- Laravel `^10.0 | ^11.0 | ^12.0 | ^13.0`
- Filament `^4.0 | ^5.0`

## Demo
![Package Demo](example.gif)

## Installation

Install the package via Composer:

```bash
composer require nben/filament-record-nav
```

Optionally publish the config file to customise the order column and sort directions:
```bash
php artisan vendor:publish --tag=filament-record-nav-config
```

## Quick Start
 
Add the two actions to `getHeaderActions()` on any `ViewRecord` or `EditRecord` page.
**No trait required - this is all you need for the default behaviour.**
 
```php
<?php
 
namespace App\Filament\Resources\PostResource\Pages;
 
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ViewRecord;
use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
 
class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;
 
    protected function getHeaderActions(): array
    {
        return [
            PreviousRecordAction::make(),
            NextRecordAction::make(),
            // ... your other actions
        ];
    }
}
```
 
Out of the box the actions will:
 
- Order records by the `order_column` defined in config (default: `id`)
- Render as outlined primary buttons with chevron icons
- Disable and turn gray when there is no adjacent record (first / last)
- Navigate to the `view` page of the adjacent record
 
---
 
## Navigating to the Edit Page
 
Use `->navigateTo()` with the `NavigationPage` enum to control which page type
the action redirects to. Each action can target a different page type independently.
 
```php
use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
use Nben\FilamentRecordNav\Enums\NavigationPage;
 
protected function getHeaderActions(): array
{
    return [
        // Both navigate to the edit page
        PreviousRecordAction::make()->navigateTo(NavigationPage::Edit),
        NextRecordAction::make()->navigateTo(NavigationPage::Edit),
    ];
}
```
 
Or mix them:
 
```php
// Previous stays on view, next goes to edit
PreviousRecordAction::make()->navigateTo(NavigationPage::View),
NextRecordAction::make()->navigateTo(NavigationPage::Edit),
```
 
---
 
## Customising Button Appearance
 
Both actions extend Filament's `Action` class directly, so every fluent method
from the [Filament Actions docs](https://filamentphp.com/docs/5.x/actions/overview)
works as normal:
 
```php
use Filament\Support\Enums\Size;
use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
 
PreviousRecordAction::make()
    ->label('← Previous')
    ->color('secondary')
    ->size(Size::Small)
    ->tooltip('Go to previous record')
    ->keyBindings(['mod+left']),
 
NextRecordAction::make()
    ->label('Next →')
    ->icon('heroicon-o-arrow-right')
    ->color('secondary')
    ->size(Size::Small)
    ->tooltip('Go to next record')
    ->keyBindings(['mod+right']),
```
 
---
 
## Custom Navigation Logic
 
For custom query logic - filtering by status, scoping to a tenant, ordering by
a different column - add the `WithRecordNavigation` trait to your page class and
override the methods you need.
 
The trait is **optional**. Only add it when the default config-driven query is
not sufficient.
 
### Filtering which records can be navigated
 
```php
<?php
 
namespace App\Filament\Resources\PostResource\Pages;
 
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
use Nben\FilamentRecordNav\Concerns\WithRecordNavigation;
 
class ViewPost extends ViewRecord
{
    use WithRecordNavigation;
 
    protected static string $resource = PostResource::class;
 
    protected function getHeaderActions(): array
    {
        return [
            PreviousRecordAction::make(),
            NextRecordAction::make(),
        ];
    }
 
    // Only navigate through published posts, ordered by published_at
    public function getPreviousRecord(): ?Model
    {
        return $this->getRecord()
            ->newQuery()
            ->where('status', 'published')
            ->where('published_at', '<', $this->getRecord()->published_at)
            ->orderBy('published_at', 'desc')
            ->first();
    }
 
    public function getNextRecord(): ?Model
    {
        return $this->getRecord()
            ->newQuery()
            ->where('status', 'published')
            ->where('published_at', '>', $this->getRecord()->published_at)
            ->orderBy('published_at', 'asc')
            ->first();
    }
}
```
 
### Overriding the navigation URL
 
Override `getRecordNavigationUrl()` to fully control where the action redirects,
regardless of what `NavigationPage` value was passed to the action:
 
```php
use Illuminate\Database\Eloquent\Model;
use Nben\FilamentRecordNav\Enums\NavigationPage;
 
// Always navigate to the edit page, ignoring the action's NavigationPage setting
public function getRecordNavigationUrl(Model $record, NavigationPage $page): string
{
    return static::getResource()::getUrl('edit', ['record' => $record]);
}
```
 
### Strict typing with the contract (optional)
 
If you want IDE autocompletion and static analysis support (PHPStan / Psalm),
declare `implements HasRecordNavigation` alongside the trait:
 
```php
use Nben\FilamentRecordNav\Concerns\WithRecordNavigation;
use Nben\FilamentRecordNav\Contracts\HasRecordNavigation;
 
class ViewPost extends ViewRecord implements HasRecordNavigation
{
    use WithRecordNavigation; // provides the default implementation
}
```
 
> **Note:** The actions use `method_exists()` rather than `instanceof` to detect
> navigation methods. This means declaring `implements` is optional - the actions
> will call your overridden methods correctly either way. The interface is purely
> for your own type-safety tooling.
 
---
 
## Configuration
 
```php
// config/filament-record-nav.php
 
return [
    /*
    | The column used to determine record order for navigation.
    | When navigating previous, queries for the nearest record with a lower
    | value. When navigating next, queries for the nearest record with a higher
    | value. Add a database index on this column for best performance.
    |
    | Common choices: 'id', 'created_at', 'updated_at', 'sort_order'
    */
    'order_column' => 'id',
 
    /*
    | Sort directions for the previous and next queries.
    | 'desc' for previous ensures the closest lower record is returned first.
    | 'asc'  for next  ensures the closest higher record is returned first.
    | Only change these if you have a non-standard ordering requirement.
    */
    'previous_direction' => 'desc',
    'next_direction'     => 'asc',
];
```
 
---
 
## Architecture
 
```
src/
├── Actions/
│   ├── Concerns/
│   │   └── ResolvesAdjacentRecord.php  ← shared cache, URL resolution, fallback query
│   ├── NextRecordAction.php            ← thin action, delegates to the trait above
│   └── PreviousRecordAction.php        ← thin action, delegates to the trait above
├── Concerns/
│   └── WithRecordNavigation.php        ← optional page trait for custom query logic
├── Contracts/
│   └── HasRecordNavigation.php         ← optional interface for strict typing
├── Enums/
│   └── NavigationPage.php              ← View | Edit
└── FilamentRecordNavServiceProvider.php
```
 
### How it works
 
When a page renders, Filament evaluates each action's `color()`, `disabled()`,
and `url()` closures. Each closure receives the Livewire component instance via
Filament's `$livewire` utility injection - a stable, first-class API that is
not expected to change between Filament versions.
 
Inside each closure, `ResolvesAdjacentRecord::getCachedRecord()` is called.
On the first call it resolves the adjacent record (via the page's override or
the config fallback), stores it in `$resolvedRecordCache`, and returns it.
The second and third closure calls read from the cache - so only **one database
query** fires per action per render, regardless of how many closures need the result.
 
No internal Filament lifecycle hooks (`configureAction()`, `bootUsing()`, etc.)
are used anywhere in the package.
 
---
 
## Troubleshooting
 
**Buttons are always disabled (gray)**
 
- Confirm the `order_column` in config matches an actual column on your table.
- If using a timestamp column (`created_at`), ensure records have distinct values - records with identical timestamps will not find each other.
- If you have overridden `getPreviousRecord()` / `getNextRecord()`, add a temporary `\Log::info(...)` inside them to confirm they are being called and the query returns what you expect.
 
**Custom filter overrides are being ignored**
 
- Confirm the `WithRecordNavigation` trait is present on the page class with `use WithRecordNavigation;`.
- Confirm the method signatures match exactly: `public function getPreviousRecord(): ?Model` and `public function getNextRecord(): ?Model`.
- Confirm `use Illuminate\Database\Eloquent\Model;` is imported at the top of the page class.
 
**Wrong page type after navigation**
 
- Use `->navigateTo(NavigationPage::Edit)` on the action.
- Or override `getRecordNavigationUrl()` in the trait to always return the route you want.
 
**Performance on large tables**
 
- Add a database index on the `order_column`.
- Override `getPreviousRecord()` / `getNextRecord()` to add `where()` scopes that narrow the result set before the adjacent record query runs.
 
---
 
## Upgrade Guide (from v1.x)
 
v2.0.0 is a full rewrite for Filament v4/v5. Summary of breaking changes:
 
| v1.x | v2.x |
|------|------|
| Trait **required** on the page | Trait **optional** |
| `configureAction()` hook used internally | Removed - uses `$livewire` injection |
| No page-type enum | `NavigationPage::View` / `NavigationPage::Edit` |
| `getRecordUrl(Model $record)` | `getRecordNavigationUrl(Model $record, NavigationPage $page)` |
| Filament `^3.0` | Filament `^4.0 \| ^5.0` |
| PHP `^8.1` | PHP `^8.2` |
 
**Migration steps:**
 
1. Remove `configureAction()` from any page classes that overrode it - it is no longer used.
2. Rename `getRecordUrl(Model $record)` to `getRecordNavigationUrl(Model $record, NavigationPage $page)` wherever you defined it. Import `NavigationPage` at the top of the file.
3. Add `->navigateTo(NavigationPage::Edit)` to any actions that previously pointed to edit routes via `getRecordUrl()`.
4. The trait is now optional - you can remove `use WithRecordNavigation` from pages that relied only on the default behaviour (no method overrides).
 
---
 
## Contributing
 
Contributions are welcome. Please open an issue before submitting a pull request
for major changes so we can discuss the approach first.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Credits
- **Leandro Ferreira** – *Original Idea / Blog Post* – [leandrocfe](https://github.com/leandrocfe)
- **Nben Malla** – *Package Developer* – [nb3n](https://github.com/nb3n)

## Support

- **Issues**: [GitHub Issues](https://github.com/nb3n/filament-record-nav/issues) 
- **Source**: [GitHub Repository](https://github.com/nb3n/filament-record-nav)

---

Made with ❤️ for the Filament PHP community
