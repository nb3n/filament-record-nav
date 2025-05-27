<?php

namespace Nben\FilamentRecordNav\Actions;

use Filament\Actions\Action;

class PreviousRecordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'previous-record';
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->hiddenLabel()
            ->outlined()
            ->icon('heroicon-o-chevron-left')
            ->tooltip('Previous');
    }
}