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
 * For custom routes not covered by the enum cases, use the custom() constructor:
 *
 *   NextRecordAction::make()->navigateTo(NavigationPage::custom('verified-view'))
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

    /**
     * Navigate to a custom Filament route not covered by the enum cases.
     *
     * The $routeName must match a key registered in your resource's getPages():
     *
     *   public static function getPages(): array
     *   {
     *       return [
     *           'verified-view' => ViewVerifiedUser::route('/{record}/verified'),
     *       ];
     *   }
     *
     * Then use it on the action:
     *
     *   NextRecordAction::make()->navigateTo(NavigationPage::custom('verified-view'))
     *
     * Returns a CustomNavigationPage value object whose ->value property
     * is passed directly to Resource::getUrl().
     */
    public static function custom(string $routeName): CustomNavigationPage
    {
        return new CustomNavigationPage($routeName);
    }
}