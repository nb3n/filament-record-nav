<?php

namespace Nben\FilamentRecordNav\Contracts;

use Illuminate\Database\Eloquent\Model;
use Nben\FilamentRecordNav\Enums\NavigationPage;

/**
 * Contract for pages that support record navigation.
 *
 * The actions (PreviousRecordAction / NextRecordAction) do NOT use instanceof
 * to check for this interface, because PHP traits do not satisfy instanceof
 * checks - a class must explicitly declare "implements HasRecordNavigation"
 * for that to work. Instead, the actions use method_exists(), which correctly
 * detects the methods whether they come from this interface, the
 * WithRecordNavigation trait, or a direct definition on the page class.
 *
 * This interface is therefore optional but recommended when you want:
 * - IDE autocompletion and type checking on your page class
 * - A clear contract to document what methods are expected
 * - Strict typing via static analysis tools (PHPStan, Psalm)
 *
 * Usage:
 *
 *   class ViewPost extends ViewRecord implements HasRecordNavigation
 *   {
 *       use WithRecordNavigation; // provides the default implementation
 *   }
 */
interface HasRecordNavigation
{
    /**
     * Return the previous record to navigate to, or null if at the boundary.
     */
    public function getPreviousRecord(): ?Model;

    /**
     * Return the next record to navigate to, or null if at the boundary.
     */
    public function getNextRecord(): ?Model;

    /**
     * Build the URL for the given record on the given page type.
     */
    public function getRecordNavigationUrl(Model $record, NavigationPage $page): string;
}