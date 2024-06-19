<?php

namespace BinaryCats\LaravelRbac\Commands;

use Spatie\Permission\Commands\CreateRole;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\PermissionRegistrar;

class CreateRoleExtended extends CreateRole
{
    protected $signature = 'permission:create-role-extended
    {name : The name of the role}
    {guard? : The name of the guard}
    {permissions? : A list of permissions to assign to the role, separated by | }
    {--team-id=}';

    public function handle(PermissionRegistrar $permissionRegistrar)
    {
        $roleClass = app(RoleContract::class);

        $teamIdAux = getPermissionsTeamId();
        setPermissionsTeamId($this->option('team-id'));

        if (!$permissionRegistrar->teams && $this->option('team-id')) {
            $this->warn('Teams feature disabled, argument --team-id has no effect. Either enable it in permissions config file or remove --team-id parameter');

            return;
        }

        $role = $roleClass::findOrCreate($this->argument('name'), $this->argument('guard'));
        setPermissionsTeamId($teamIdAux);

        $teams_key = $permissionRegistrar->teamsKey;
        if ($permissionRegistrar->teams && $this->option('team-id') && is_null($role->$teams_key)) {
            $this->warn("Role `{$role->name}` already exists on the global team; argument --team-id has no effect");
        }

        $role->givePermissionTo($this->makePermissions($this->argument('permissions')));

        $this->info("Role `{$role->name}` " . ($role->wasRecentlyCreated ? 'created' : 'updated'));
    }
}
