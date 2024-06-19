<?php

namespace BinaryCats\LaravelRbac;

use BinaryCats\LaravelRbac\Actions\SyncDefinedRole;
use BinaryCats\LaravelRbac\Contracts\DefinedRole as DefinedRoleContract;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * @property-read string $name
 */
abstract class DefinedRole implements DefinedRoleContract
{
    /** @var array|string[] */
    protected array $guards = [];
    /** @var array|string[]|int[] */
    protected array $teams = [];
    protected string $name = '';

    /**
     * Guess the name of the role.
     *
     * @return string
     */
    public function name(): string
    {
        if ('' !== $this->name) {
            return $this->name;
        }

        $reflection = new ReflectionClass($this);

        return Str::of($reflection->getName())
            ->classBasename()
            ->replaceLast('Role', '')
            ->snake()
            ->headline();
    }

    public function handle(): void
    {
        $teamsEnabled = config('permission.teams');

        foreach ($this->guards() as $guard) {
            if ($teamsEnabled && !empty($teams = $this->teams())) {
                foreach ($teams as $team) {
                    SyncDefinedRole::run($this->name(), $guard, $this->{$guard}(), teamId: $team);
                }
            } else {
                SyncDefinedRole::run($this->name(), $guard, $this->{$guard}());
            }
        }
    }

    /**
     * @return array|string[]
     */
    protected function guards(): array
    {
        return $this->guards;
    }

    /**
     * @return array|string[]|int[]
     */
    protected function teams(): array
    {
        return $this->teams;
    }
}
