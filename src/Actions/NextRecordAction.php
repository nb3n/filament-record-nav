<?php

namespace Nben\FilamentRecordNav\Actions;

use Filament\Actions\Action;

class NextRecordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'next-record';
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->hiddenLabel()
            ->outlined()
            ->icon('heroicon-o-chevron-right')
            ->tooltip('Next');
    }
}