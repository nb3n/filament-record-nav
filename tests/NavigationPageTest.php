<?php

namespace Nben\FilamentRecordNav\Tests;

use Nben\FilamentRecordNav\Enums\CustomNavigationPage;
use Nben\FilamentRecordNav\Enums\NavigationPage;
use PHPUnit\Framework\TestCase;

class NavigationPageTest extends TestCase
{
    public function test_navigation_page_enum_values(): void
    {
        $this->assertSame('view', NavigationPage::View->value);
        $this->assertSame('edit', NavigationPage::Edit->value);
    }

    public function test_custom_navigation_page_factory_returns_value_object(): void
    {
        $page = NavigationPage::custom('verified-view');

        $this->assertInstanceOf(CustomNavigationPage::class, $page);
        $this->assertSame('verified-view', $page->value);
    }
}
