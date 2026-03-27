<?php

namespace Nben\FilamentRecordNav\Concerns;

use Illuminate\Database\Eloquent\Model;
use Nben\FilamentRecordNav\Enums\NavigationPage;

/**
 * Default implementation of record navigation for Filament resource pages.
 *
 * Add this trait to a ViewRecord or EditRecord page class when you need to
 * customise navigation behaviour - for example, filtering which records can
 * be navigated, or changing the column used for ordering.
 *
 * The trait is OPTIONAL. PreviousRecordAction and NextRecordAction work
 * without it using a config-driven fallback query. Only add the trait when
 * you need to override the default logic.
 *
 * Quick start (no customisation needed):
 *
 *   class ViewPost extends ViewRecord
 *   {
 *       protected function getHeaderActions(): array
 *       {
 *           return [
 *               PreviousRecordAction::make(),
 *               NextRecordAction::make(),
 *           ];
 *       }
 *   }
 *
 * With trait (custom query logic):
 *
 *   class ViewPost extends ViewRecord
 *   {
 *       use WithRecordNavigation;
 *
 *       public function getPreviousRecord(): ?Model
 *       {
 *           return $this->getRecord()
 *               ->newQuery()
 *               ->where('status', 'published')
 *               ->where('id', '<', $this->getRecord()->id)
 *               ->orderBy('id', 'desc')
 *               ->first();
 *       }
 *   }
 *
 * For strict typing / IDE support, also declare the interface:
 *
 *   class ViewPost extends ViewRecord implements HasRecordNavigation
 *   {
 *       use WithRecordNavigation;
 *   }
 *
 * @mixin \Filament\Resources\Pages\Page
 */
trait WithRecordNavigation
{
    // -------------------------------------------------------------------------
    // Public methods - override these in your page class for custom logic
    // -------------------------------------------------------------------------

    /**
     * Return the previous record to navigate to, or null at the first record.
     *
     * The default implementation delegates to getAdjacentRecord('previous'),
     * which uses the order_column and previous_direction from config.
     *
     * Override example - only navigate through published posts:
     *
     *   public function getPreviousRecord(): ?Model
     *   {
     *       return $this->getRecord()
     *           ->newQuery()
     *           ->where('status', 'published')
     *           ->where('published_at', '<', $this->getRecord()->published_at)
     *           ->orderBy('published_at', 'desc')
     *           ->first();
     *   }
     */
    public function getPreviousRecord(): ?Model
    {
        return $this->getAdjacentRecord('previous');
    }

    /**
     * Return the next record to navigate to, or null at the last record.
     *
     * The default implementation delegates to getAdjacentRecord('next'),
     * which uses the order_column and next_direction from config.
     *
     * Override example - only navigate through published posts:
     *
     *   public function getNextRecord(): ?Model
     *   {
     *       return $this->getRecord()
     *           ->newQuery()
     *           ->where('status', 'published')
     *           ->where('published_at', '>', $this->getRecord()->published_at)
     *           ->orderBy('published_at', 'asc')
     *           ->first();
     *   }
     */
    public function getNextRecord(): ?Model
    {
        return $this->getAdjacentRecord('next');
    }

    /**
     * Build the URL for navigating to $record on the given $page type.
     *
     * The default implementation calls getResource()::getUrl() with the
     * page type value from the NavigationPage enum ('view' or 'edit').
     *
     * Override example - always navigate to the edit page regardless of
     * what NavigationPage value was passed to the action:
     *
     *   public function getRecordNavigationUrl(Model $record, NavigationPage $page): string
     *   {
     *       return static::getResource()::getUrl('edit', ['record' => $record]);
     *   }
     */
    public function getRecordNavigationUrl(Model $record, NavigationPage $page): string
    {
        return static::getResource()::getUrl($page->value, ['record' => $record]);
    }

    // -------------------------------------------------------------------------
    // Protected helpers - internal, not intended to be overridden
    // -------------------------------------------------------------------------

    /**
     * Shared query for finding the record adjacent to the current one.
     *
     * Used by both getPreviousRecord() and getNextRecord() to avoid
     * duplicating the operator / direction logic in each method.
     *
     * Config keys read:
     *  - filament-record-nav.order_column       : column to compare (default: 'id')
     *  - filament-record-nav.previous_direction : sort direction     (default: 'desc')
     *  - filament-record-nav.next_direction     : sort direction     (default: 'asc')
     *
     * @param  'previous'|'next'  $direction
     */
    protected function getAdjacentRecord(string $direction): ?Model
    {
        $orderColumn    = config('filament-record-nav.order_column', 'id');
        $orderDirection = config(
            "filament-record-nav.{$direction}_direction",
            $direction === 'previous' ? 'desc' : 'asc'
        );

        // '<' finds records before the current one, '>' finds records after.
        $operator = $direction === 'previous' ? '<' : '>';

        return $this->getRecord()
            ->newQuery()
            ->where($orderColumn, $operator, $this->getRecord()->{$orderColumn})
            ->orderBy($orderColumn, $orderDirection)
            ->first();
    }
}