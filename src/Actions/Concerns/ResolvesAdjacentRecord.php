<?php

namespace Nben\FilamentRecordNav\Actions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Nben\FilamentRecordNav\Enums\NavigationPage;

/**
 * Shared behaviour for PreviousRecordAction and NextRecordAction.
 *
 * Extracted into a trait to avoid duplicating the same logic in both action
 * classes. Responsibilities:
 *
 *  1. Per-render query cache     - getCachedRecord()
 *  2. Adjacent record resolution - delegates to the page or falls back to config
 *  3. URL resolution             - delegates to the page or falls back to getResource()
 *  4. Default fallback query     - defaultAdjacentQuery()
 */
trait ResolvesAdjacentRecord
{
    /**
     * Per-render cache for the resolved adjacent record.
     *
     * Problem this solves: setUp() wires three separate closures onto each
     * action - color(), disabled(), and url() - and all three need the same
     * adjacent record. Without caching, each closure would fire an independent
     * database query, resulting in 3 queries per action (6 per page render).
     *
     * Solution: the first closure to run stores the result here; the other two
     * read from it. The cache is keyed by "{direction}-{spl_object_id}" so it
     * is safe when both actions exist on the same page simultaneously and is
     * automatically scoped to a single Livewire component instance.
     *
     * @var array<string, Model|null>
     */
    private array $resolvedRecordCache = [];

    /**
     * Return the adjacent record for $direction, hitting the cache if possible.
     *
     * Resolution order:
     *  1. Return from cache if already resolved this render cycle.
     *  2. Call getPreviousRecord() / getNextRecord() on the Livewire component
     *     if the method exists (i.e. the page uses WithRecordNavigation or
     *     defines the method directly). This is where custom filter logic lives.
     *  3. Fall back to the config-driven defaultAdjacentQuery() if the page
     *     has no custom method.
     *
     * @param  'previous'|'next'  $direction
     */
    protected function getCachedRecord(string $direction, Component $livewire): ?Model
    {
        $cacheKey = $direction . '-' . spl_object_id($livewire);

        if (array_key_exists($cacheKey, $this->resolvedRecordCache)) {
            return $this->resolvedRecordCache[$cacheKey];
        }

        $method = $direction === 'previous' ? 'getPreviousRecord' : 'getNextRecord';

        // method_exists() is used intentionally instead of instanceof.
        // PHP traits do not satisfy instanceof checks - a class needs an
        // explicit "implements" declaration for that to pass. method_exists()
        // correctly detects the method regardless of how it was added to the
        // class (trait, direct definition, or interface implementation).
        $record = method_exists($livewire, $method)
            ? $livewire->{$method}()
            : $this->defaultAdjacentQuery($livewire, $direction);

        return $this->resolvedRecordCache[$cacheKey] = $record;
    }

    /**
     * Build the redirect URL for $record on the given $page type.
     *
     * Resolution order:
     *  1. Call getRecordNavigationUrl() on the Livewire component if it exists.
     *     This allows the page to fully control the URL (e.g. always edit page).
     *  2. Call getResource()::getUrl() directly on the page if available.
     *     This is the standard Filament pattern and covers most cases.
     *  3. Return '#' as a last resort so the button renders without crashing.
     *     In practice this branch should never be reached on a valid resource page.
     */
    protected function resolveUrl(Component $livewire, Model $record, NavigationPage $page): string
    {
        if (method_exists($livewire, 'getRecordNavigationUrl')) {
            return $livewire->getRecordNavigationUrl($record, $page);
        }

        if (method_exists($livewire, 'getResource')) {
            return $livewire::getResource()::getUrl(
                $page->value,
                ['record' => $record]
            );
        }

        return '#';
    }

    /**
     * Config-driven fallback query for finding an adjacent record.
     *
     * Used only when the host page does not define getPreviousRecord() or
     * getNextRecord() - i.e. the WithRecordNavigation trait is not present
     * and no custom method has been added. This ensures the actions work
     * out of the box with zero setup on the page class.
     *
     * The query reads three values from config/filament-record-nav.php:
     *  - order_column       : the column to compare against (default: 'id')
     *  - previous_direction : sort direction for previous queries (default: 'desc')
     *  - next_direction     : sort direction for next queries     (default: 'asc')
     *
     * @param  'previous'|'next'  $direction
     */
    protected function defaultAdjacentQuery(Component $livewire, string $direction): ?Model
    {
        // Guard: getRecord() must exist on the page (it always does on
        // ViewRecord / EditRecord, but this keeps the method safe if the
        // action is ever placed on a non-standard Livewire component).
        if (! method_exists($livewire, 'getRecord')) {
            return null;
        }

        $record         = $livewire->getRecord();
        $orderColumn    = config('filament-record-nav.order_column', 'id');
        $orderDirection = config(
            "filament-record-nav.{$direction}_direction",
            $direction === 'previous' ? 'desc' : 'asc'
        );

        // '<' for previous (records with a lower value), '>' for next.
        $operator = $direction === 'previous' ? '<' : '>';

        return $record
            ->newQuery()
            ->where($orderColumn, $operator, $record->{$orderColumn})
            ->orderBy($orderColumn, $orderDirection)
            ->first();
    }
}