<?php

namespace Nben\FilamentRecordNav\Tests;

use Nben\FilamentRecordNav\Actions\NextRecordAction;
use Nben\FilamentRecordNav\Actions\PreviousRecordAction;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function test_actions_have_expected_default_names(): void
    {
        $this->assertSame('next-record', NextRecordAction::getDefaultName());
        $this->assertSame('previous-record', PreviousRecordAction::getDefaultName());
    }
}
