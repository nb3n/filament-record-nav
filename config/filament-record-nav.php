<?php

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
    'next_direction' => 'asc',
];
