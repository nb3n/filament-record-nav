<?php

namespace Nben\FilamentRecordNav\Enums;

/**
 * The Filament resource page type to navigate to when an action is triggered.
 *
 * Pass to navigateTo() on either action:
 *
 *   PreviousRecordAction::make()->navigateTo(NavigationPage::Edit)
 *   NextRecordAction::make()->navigateTo(NavigationPage::View)
 *
 * The string value maps directly to the Filament route name used in
 * Resource::getUrl($page->value, ['record' => $record]).
 */
enum NavigationPage: string
{
    /**
     * Navigate to the ViewRecord page (default).
     * Uses the 'view' Filament route.
     */
    case View = 'view';

    /**
     * Navigate to the EditRecord page.
     * Uses the 'edit' Filament route.
     */
    case Edit = 'edit';
}