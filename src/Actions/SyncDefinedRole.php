<?php

namespace BinaryCats\LaravelRbac\Actions;

use BackedEnum;
use Illuminate\Support\Facades\Artisan;
use Lorisleiva\Actions\Action;

class SyncDefinedRole extends Action
{
    /**
     * @return void
     */
    public function handle(string $name, string $guard, array $permissions, int|string $teamId = null): void
    {
        $permissions = collect($permissions)
            ->map(fn ($permission) => match (true) {
                $permission instanceof BackedEnum => $permission->value,
                default                           => (string) $permission
            })->implode('|');

        $teamsEnabled = config('permission.teams');
        $fields = [
            'name'        => $name,
            'guard'       => $guard,
            'permissions' => $permissions,
        ];
        if ($teamsEnabled) {
            $fields['--team-id'] = $teamId;
        }

        Artisan::call('permission:create-role', $fields);
    }
}
