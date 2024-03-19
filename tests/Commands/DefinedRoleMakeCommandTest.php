<?php

namespace BinaryCats\Rbac\Tests\Commands;

use BinaryCats\Rbac\Commands\DefinedRoleMakeCommand;
use BinaryCats\Rbac\Tests\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\After;
use Spatie\Snapshots\MatchesSnapshots;

class DefinedRoleMakeCommandTest extends TestCase
{
    use MatchesSnapshots;

    #[After]
    protected function tearDown(): void
    {
        $stubPath = app_path('Roles/FooRole.php');

        if (File::exists($stubPath)) {
            unlink($stubPath);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_will_return_the_name_of_the_stub_for_the_make_contract_command(): void
    {
        $stubPath = app_path('Roles/FooRole.php');

        $this->artisan(DefinedRoleMakeCommand::class, ['name' => 'FooRole'])
            ->assertOk();

        $this->assertFileExists($stubPath);
        $this->assertStringContainsString('class FooRole extends DefinedRole', File::get($stubPath));
        $this->assertStringContainsString('namespace App\Roles;', File::get($stubPath));
        $this->assertMatchesFileSnapshot($stubPath);
    }
}
