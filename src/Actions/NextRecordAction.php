<?php

namespace Nben\FilamentRecordNav\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Livewire\Component;
use Nben\FilamentRecordNav\Actions\Concerns\ResolvesAdjacentRecord;
use Nben\FilamentRecordNav\Enums\CustomNavigationPage;
use Nben\FilamentRecordNav\Enums\NavigationPage;

/**
 * A Filament header action that navigates to the next record.
 *
 * Drop into any ViewRecord or EditRecord page's getHeaderActions() method:
 *
 *   NextRecordAction::make()
 *
 * By default it navigates to the 'view' page of the next record, ordered
 * by the 'order_column' defined in config/filament-record-nav.php (default: id).
 *
 * The button is automatically disabled and turns gray when there is no
 * next record (i.e. the current record is the last one).
 *
 * All standard Filament Action fluent methods work normally, for example:
 *
 *   NextRecordAction::make()
 *       ->label('Next →')
 *       ->color('secondary')
 *       ->size(Size::Small)
 *       ->tooltip('Go to next record')
 *       ->keyBindings(['mod+right'])
 *
 * To navigate to the edit page instead of view:
 *
 *   NextRecordAction::make()->navigateTo(NavigationPage::Edit)
 *
 * To navigate to a custom route registered in getPages():
 *
 *   NextRecordAction::make()->navigateTo(NavigationPage::custom('verified-view'))
 *
 * To add custom filtering logic (e.g. only navigate published posts),
 * add the WithRecordNavigation trait to your page and override
 * getNextRecord(). See WithRecordNavigation for details.
 */
class NextRecordAction extends Action
{
    use ResolvesAdjacentRecord;

    /**
     * The Filament page type this action navigates to.
     *
     * Accepts either a NavigationPage enum case (View, Edit) or a
     * CustomNavigationPage value object created via NavigationPage::custom().
     * Defaults to NavigationPage::View. Change via navigateTo().
     */
    protected NavigationPage|CustomNavigationPage $navigationPage = NavigationPage::View;

    /**
     * The registered name used by Filament to identify this action.
     * Must be unique within a page's action list.
     */
    public static function getDefaultName(): ?string
    {
        return 'next-record';
    }

    /**
     * Set the Filament page type to navigate to when this action is triggered.
     *
     * Accepts a NavigationPage enum case or a custom route name:
     *
     *   NextRecordAction::make()->navigateTo(NavigationPage::Edit)
     *   NextRecordAction::make()->navigateTo(NavigationPage::custom('verified-view'))
     */
    public function navigateTo(NavigationPage|CustomNavigationPage $page): static
    {
        $this->navigationPage = $page;

        return $this;
    }

    /**
     * Configure the action's default appearance and behaviour.
     *
     * All three closures (color, disabled, url) call getCachedRecord() from
     * ResolvesAdjacentRecord, which ensures the database query runs only once
     * per render cycle regardless of how many closures consume the result.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->hiddenLabel()
            ->button()
            ->outlined()
            ->icon('heroicon-o-chevron-right')
            ->tooltip('Next')
            ->size(Size::Medium)
            // Gray when disabled (no next record), primary when active.
            ->color(function (Component $livewire): string {
                return $this->getCachedRecord('next', $livewire) ? 'primary' : 'gray';
            })
            // Disabled at the boundary (last record) - button renders but is not clickable.
            ->disabled(function (Component $livewire): bool {
                return $this->getCachedRecord('next', $livewire) === null;
            })
            // Null URL keeps the button rendered without a broken href.
            ->url(function (Component $livewire): ?string {
                $record = $this->getCachedRecord('next', $livewire);

                if ($record === null) {
                    return null;
                }

                return $this->resolveUrl($livewire, $record, $this->navigationPage);
            });
    }
}