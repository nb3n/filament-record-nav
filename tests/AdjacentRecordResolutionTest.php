<?php

namespace Nben\FilamentRecordNav\Tests;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Nben\FilamentRecordNav\Actions\Concerns\ResolvesAdjacentRecord;
use Nben\FilamentRecordNav\Enums\NavigationPage;
use PHPUnit\Framework\TestCase;

class TestResource
{
    public static function getUrl(string $page, array $params): string
    {
        return sprintf('resource://%s/%s', $page, $params['record']->id);
    }
}

class AdjacentRecordResolutionTest extends TestCase
{
    public function test_resolve_url_uses_resource_url_when_no_custom_override_exists(): void
    {
        $record = new class extends Model
        {
            public $id = 42;
        };

        $component = new class($record) extends Component
        {
            use ResolvesAdjacentRecord;

            public function __construct(private readonly Model $record) {}

            public static function getResource(): string
            {
                return TestResource::class;
            }

            public function getRecord(): Model
            {
                return $this->record;
            }
        };

        $url = $this->invokeMethod($component, 'resolveUrl', [$component, $record, NavigationPage::Edit]);

        $this->assertSame('resource://edit/42', $url);
    }

    private function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionMethod($object, $methodName);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $parameters);
    }
}
