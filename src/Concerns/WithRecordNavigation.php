<?php

namespace Nben\FilamentRecordNav\Concerns;

use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait WithRecordNavigation
{
    protected function configureAction(Action $action): void
    {
        match (true) {
            $action instanceof PreviousRecordAction => $this->configurePreviousAction($action),
            $action instanceof NextRecordAction => $this->configureNextAction($action),
            default => parent::configureAction($action),
        };
    }

    protected function configurePreviousAction(Action $action): void
    {
        $previous = $this->getPreviousRecord();
        
        $action->tooltip('Previous record')
            ->icon('heroicon-o-chevron-left')
            ->color($previous ? 'primary' : 'gray')
            ->disabled(!$previous)
            ->url($previous ? $this->getRecordUrl($previous) : null);
    }

    protected function configureNextAction(Action $action): void
    {
        $next = $this->getNextRecord();
        
        $action->tooltip('Next record')
            ->icon('heroicon-o-chevron-right')
            ->color($next ? 'primary' : 'gray')
            ->disabled(!$next)
            ->url($next ? $this->getRecordUrl($next) : null);
    }

    protected function getRecordUrl(Model $record): string
    {
        return static::getResource()::getUrl('view', ['record' => $record]);
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

        return $this->getRecord()
            ->newQuery()
            ->where($orderColumn, $operator, $this->getRecord()->{$orderColumn})
            ->orderBy($orderColumn, $orderDirection)
            ->first();
    }
}