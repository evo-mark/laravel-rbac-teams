<?php

namespace BinaryCats\Rbac\Tests;

use BinaryCats\Rbac\DiscoverAbilities;
use BinaryCats\Rbac\Tests\Fixtures\Abilities\FooAbility;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SplFileInfo;

class DiscoverAbilitiesTest extends TestCase
{
    /** @test */
    public function it_will_handle_class_from_file_resolution_closure_callback(): void
    {
        DiscoverAbilities::guessClassNamesUsing(function(SplFileInfo $file, $basePath) {
            return Str::of($file->getRealPath())
                ->replaceFirst($basePath, '')
                ->trim(DIRECTORY_SEPARATOR)
                ->after('/tests/')
                ->prepend('BinaryCats/Rbac/Tests/')
                ->replaceLast('.php', '')
                ->replace(DIRECTORY_SEPARATOR, '\\')
                ->toString();
        });

        $result = DiscoverAbilities::within(
            __DIR__.'/Fixtures/Abilities',
            $this->app->basePath()
        );

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertContains(FooAbility::One, $result);
        $this->assertContains(FooAbility::Two, $result);
    }
}
