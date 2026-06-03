<?php

use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Navigation Column
    |--------------------------------------------------------------------------
    |
    | The column used to determine the order of records during navigation.
    | When navigating to the previous record, the package queries for the
    | nearest record with a lower value in this column. For next, it queries
    | for the nearest record with a higher value.
    |
    | Common choices: 'id', 'created_at', 'updated_at', 'sort_order'
    |
    | Note: for best performance this column should be indexed in your database,
    | especially on large tables.
    |
    */
    'order_column' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Navigation Directions
    |--------------------------------------------------------------------------
    |
    | The sort direction applied when querying for the previous or next record.
    |
    | previous_direction: 'desc' - after filtering with '<', order descending
    |   so the record closest to the current one comes first.
    |
    | next_direction: 'asc' - after filtering with '>', order ascending
    |   so the record closest to the current one comes first.
    |
    | Only change these if you have a non-standard ordering requirement.
    |
    */
    'previous_direction' => 'desc',
    'next_direction'     => 'asc',

    /*
     |--------------------------------------------------------------------------
     | Display Record Title
     |--------------------------------------------------------------------------
     | determine if the title should be displayed in the button
     */
    'display_record_title' => true,

    /*
    |--------------------------------------------------------------------------
    | Icons Used
    |--------------------------------------------------------------------------
    | specified the icons used for next and previous buttons, you can override this
    | global configuration by adding following attributes to the page:
    |    public static $previousRecordIcon = Heroicon::SignalSlash;
    |    public static $nextRecordIcon = Heroicon::CheckCircle;
    | the value can be string|BackedEnum|Htmlable similar to resource icon
    */
    'previous_icon' => Heroicon::ChevronLeft,
    'next_icon' => Heroicon::ChevronRight,

    /*
    |--------------------------------------------------------------------------
    | Icons Position
    |--------------------------------------------------------------------------
    | specified the icon position it make different when the record title is enabled,
    | the value can be string|BackedEnum referee to iconPosition @ filament doc.
    */
    'previous_icon_position' => IconPosition::After,
    'next_icon_position' => IconPosition::Before,
];