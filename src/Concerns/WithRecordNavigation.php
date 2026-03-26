<?php

declare(strict_types=1);

namespace Nben\FilamentRecordNav\Concerns;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;

trait WithRecordNavigation
{
    /**
     * Filament v4: header actions are not passed through configureAction().
     * Merge this into getHeaderActions(), e.g. return $this->getRecordNavigationHeaderActions();
     *
     * @return array<int, PreviousRecordAction|NextRecordAction>
     */
    protected function getRecordNavigationHeaderActions(): array
    {
        $previous = $this->getPreviousRecord();
        $next = $this->getNextRecord();
        $rtl = $this->recordNavigationIsRtl();

        return [
            PreviousRecordAction::make()
                ->tooltip($this->getPreviousRecordTooltip())
                ->icon($rtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left')
                ->color($previous ? 'primary' : 'gray')
                ->disabled(! $previous)
                ->url($previous ? $this->getRecordUrl($previous) : null),
            NextRecordAction::make()
                ->tooltip($this->getNextRecordTooltip())
                ->icon($rtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right')
                ->color($next ? 'primary' : 'gray')
                ->disabled(! $next)
                ->url($next ? $this->getRecordUrl($next) : null),
        ];
    }

    protected function getPreviousRecordTooltip(): string
    {
        $key = config('filament-record-nav.previous_tooltip_key');

        return $key ? __($key) : __('Previous record');
    }

    protected function getNextRecordTooltip(): string
    {
        $key = config('filament-record-nav.next_tooltip_key');

        return $key ? __($key) : __('Next record');
    }

    protected function recordNavigationIsRtl(): bool
    {
        $locales = config('filament-record-nav.rtl_locales', ['ar', 'ckb', 'he', 'fa', 'ur']);

        return in_array(app()->getLocale(), $locales, true);
    }

    /**
     * Filament v3 / legacy — not used for header actions in Filament v4.
     */
    protected function configureAction(Action $action): void
    {
        match (true) {
            $action instanceof PreviousRecordAction => $this->configurePreviousAction($action),
            $action instanceof NextRecordAction => $this->configureNextAction($action),
            default => $this->invokeParentConfigureAction($action),
        };
    }

    protected function invokeParentConfigureAction(Action $action): void
    {
        if (method_exists(parent::class, 'configureAction')) {
            parent::configureAction($action);
        }
    }

    protected function configurePreviousAction(Action $action): void
    {
        $previous = $this->getPreviousRecord();
        $rtl = $this->recordNavigationIsRtl();

        $action->tooltip($this->getPreviousRecordTooltip())
            ->icon($rtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left')
            ->color($previous ? 'primary' : 'gray')
            ->disabled(! $previous)
            ->url($previous ? $this->getRecordUrl($previous) : null);
    }

    protected function configureNextAction(Action $action): void
    {
        $next = $this->getNextRecord();
        $rtl = $this->recordNavigationIsRtl();

        $action->tooltip($this->getNextRecordTooltip())
            ->icon($rtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right')
            ->color($next ? 'primary' : 'gray')
            ->disabled(! $next)
            ->url($next ? $this->getRecordUrl($next) : null);
    }

    /**
     * Uses the model’s route key (UUID, slug, etc.) for the URL parameter.
     */
    protected function getRecordUrl(Model $record): string
    {
        return static::getResource()::getUrl(
            $this->getRecordNavigationUrlRoute(),
            ['record' => $record->getRouteKey()],
        );
    }

    /**
     * @return 'view'|'edit'|string
     */
    protected function getRecordNavigationUrlRoute(): string
    {
        return config('filament-record-nav.url_route', 'view');
    }

    protected function getPreviousRecord(): ?Model
    {
        return $this->getAdjacentRecord('previous');
    }

    protected function getNextRecord(): ?Model
    {
        return $this->getAdjacentRecord('next');
    }

    protected function getAdjacentRecord(string $direction): ?Model
    {
        $orderColumn = config('filament-record-nav.order_column', 'id');
        $orderDirection = config("filament-record-nav.{$direction}_direction", $direction === 'previous' ? 'desc' : 'asc');
        $operator = $direction === 'previous' ? '<' : '>';

        $record = $this->getRecord();
        $currentOrderValue = $record->getAttribute($orderColumn);

        if ($currentOrderValue === null) {
            return null;
        }

        return $record->newQuery()
            ->where($orderColumn, $operator, $currentOrderValue)
            ->orderBy($orderColumn, $orderDirection)
            ->first();
    }
}
