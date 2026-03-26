<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order column
    |--------------------------------------------------------------------------
    |
    | Column used to find adjacent records. Use a numeric or chronological
    | column (e.g. id, sort_order). If your model uses UUID in the URL but
    | integer id in the database, keep this as id — URLs still use getRouteKey().
    |
    */
    'order_column' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Adjacent record query direction
    |--------------------------------------------------------------------------
    */
    'previous_direction' => 'desc',
    'next_direction' => 'asc',

    /*
    |--------------------------------------------------------------------------
    | RTL locales (swap prev/next chevrons so “back” points visually correct)
    |--------------------------------------------------------------------------
    */
    'rtl_locales' => ['ar', 'ckb', 'he', 'fa', 'ur'],

    /*
    |--------------------------------------------------------------------------
    | Default Filament route name for getUrl() (view | edit)
    |--------------------------------------------------------------------------
    |
    | Override per page with getRecordNavigationUrlRoute() when needed.
    |
    */
    'url_route' => 'view',

    /*
    |--------------------------------------------------------------------------
    | Tooltip translation keys (optional)
    |--------------------------------------------------------------------------
    |
    | When set, __() is used so you can use models.previous_record etc.
    |
    */
    'previous_tooltip_key' => null,
    'next_tooltip_key' => null,
];
