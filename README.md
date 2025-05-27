# Filament Record Navigation

A Laravel package that adds elegant next/previous record navigation to your Filament PHP admin panels. Navigate seamlessly between records with intuitive navigation buttons.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/php-8.1-blue)
![Laravel](https://img.shields.io/badge/laravel-10.0-red)
![Filament](https://img.shields.io/badge/filament-3.0-orange)

## Features

- 🎯 **Simple Integration** - Add navigation with just a trait
- 🎨 **Filament Native** - Uses Filament's action system and styling
- ⚙️ **Configurable** - Customize ordering column and directions
- 🚀 **Performance Optimized** - Efficient database queries
- 🎭 **Smart States** - Automatically disables buttons at boundaries
- 📱 **Responsive** - Works beautifully on all devices

## Requirements

- PHP ^8.1
- Laravel ^10.0
- Filament ^3.0

## Demo
![Package Demo](example.gif)

## Installation

Install the package via Composer:

```bash
composer require nben/filament-record-nav
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=filament-record-nav-config
```

## Quick Start

### 1. Add the trait to your Filament resource pages

Add the `WithRecordNavigation` trait to your `ViewRecord` or `EditRecord` pages:

```php
<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ViewRecord;

use Nben\FilamentRecordNav\Concerns\WithRecordNavigation;

class ViewPost extends ViewRecord
{
    use WithRecordNavigation;
    
    protected static string $resource = PostResource::class;
}
```

### 2. Add navigation actions to your page

Add the navigation actions to your page's action array:

```php
<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ViewRecord;

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
            // ... your other actions
        ];
    }
}
```

That's it! Your Filament resource pages now have beautiful next/previous navigation buttons.

## Configuration

The package comes with sensible defaults, but you can customize the behavior by publishing and modifying the configuration file:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Navigation Column
    |--------------------------------------------------------------------------
    | 
    | This column will be used to determine the order of records
    | for navigation. Common choices: 'id', 'created_at', 'updated_at'
    |
    */
    'order_column' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Navigation Directions
    |--------------------------------------------------------------------------
    |
    | Define the sort directions for previous and next navigation
    |
    */
    'previous_direction' => 'desc',
    'next_direction' => 'asc',
];
```

## Advanced Usage

### Custom Navigation Logic

You can override the navigation methods in your page class for custom behavior:

```php
<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ViewRecord;

use Illuminate\Database\Eloquent\Model;
use Nben\FilamentRecordNav\Concerns\WithRecordNavigation;

class ViewPost extends ViewRecord
{
    use WithRecordNavigation;
    
    protected static string $resource = PostResource::class;
    
    // Custom logic for finding the previous record
    protected function getPreviousRecord(): ?Model
    {
        return $this->getRecord()
            ->newQuery()
            ->where('status', 'published') // Only navigate through published posts
            ->where('created_at', '<', $this->getRecord()->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
    }
    
    // Custom logic for finding the next record
    protected function getNextRecord(): ?Model
    {
        return $this->getRecord()
            ->newQuery()
            ->where('status', 'published') // Only navigate through published posts
            ->where('created_at', '>', $this->getRecord()->created_at)
            ->orderBy('created_at', 'asc')
            ->first();
    }
}
```
### Custom Record URLs

By default, the navigation uses the `view` route. You can customize this:

```php
<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Database\Eloquent\Model; 
use Nben\FilamentRecordNav\Concerns\WithRecordNavigation;

class EditPost extends EditRecord
{
    use WithRecordNavigation;

    protected static string $resource = PostResource::class;

    protected function getRecordUrl(Model $record): string
    {
        return static::getResource()::getUrl('edit', ['record' => $record]);
    }
}
```

### Customizing Action Appearance

You can customize the appearance of navigation buttons:

```php
protected function getHeaderActions(): array
{
    return [
        PreviousRecordAction::make()
            ->label('← Previous')
            ->color('secondary')
            ->size('sm'),

        NextRecordAction::make()
            ->label('Next →')
            ->color('secondary')
            ->size('sm'),
    ];
}
```

## Working with Different Resource Types

### Edit Pages

The trait works seamlessly with edit pages:

```php
<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;
use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
use Nben\FilamentRecordNav\Concerns\WithRecordNavigation;

class EditPost extends EditRecord
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
}
```

### Multiple Resource Types

You can use the same navigation pattern across different resources:

```php
// For Users
class ViewUser extends ViewRecord
{
    use WithRecordNavigation;
    // ... configuration
}

// For Orders  
class ViewOrder extends ViewRecord
{
    use WithRecordNavigation;
    // ... configuration
}
```

## Navigation Behavior

### Smart Button States

- **Previous Button**: Automatically disabled when viewing the first record
- **Next Button**: Automatically disabled when viewing the last record
- **Visual Feedback**: Disabled buttons are visually distinct (gray color)

### Performance Considerations

The package uses efficient database queries:
- Only fetches the next/previous record when needed
- Uses indexed columns for optimal performance
- Minimal database overhead

## Troubleshooting

### Navigation Not Working

1. **Ensure the trait is added**: Make sure `WithRecordNavigation` trait is used in your page class
2. **Check action registration**: Verify that `NextRecordAction` and `PreviousRecordAction` are added to your `getHeaderActions()` method
3. **Database ordering**: Ensure your ordering column exists and has appropriate indexes

### Custom Ordering Issues

If using a custom order column, make sure:
1. The column exists in your database table
2. The column is properly indexed for performance
3. The column has appropriate data types for comparison

### Performance Issues

For large datasets:
1. Ensure your ordering column is indexed
2. Consider adding database indexes on frequently filtered columns
3. Override navigation methods to add appropriate `where` clauses

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

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
