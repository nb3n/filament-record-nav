<?php

namespace Nben\FilamentRecordNav\Enums;

/**
 * Represents a custom Filament route name for record navigation.
 *
 * Created via NavigationPage::custom('your-route-name').
 * Passed to navigateTo() on either action in place of a NavigationPage enum case.
 *
 * Both this class and NavigationPage expose a ->value string property,
 * so resolveUrl() in ResolvesAdjacentRecord can call $page->value on either
 * type without any instanceof branching.
 *
 * Usage:
 *
 *   NextRecordAction::make()->navigateTo(NavigationPage::custom('verified-view'))
 */
final class CustomNavigationPage
{
    public function __construct(public readonly string $value) {}
}