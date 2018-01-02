<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:43 PM
 */

namespace StreamerDash\Perms\Traits;


use Illuminate\Support\Collection;
use StreamerDash\Perms\Exceptions\GuardDoesNotMatch;
use StreamerDash\Perms\Models\Permission;
use StreamerDash\Perms\PermissionRegistrar;

trait HasPermissions
{

    /**
     * Grant the given permission(s) to a role.
     *
     * @param string|array|\StreamerDash\Perms\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                return $this->getStoredPermission($permission);
            })
            ->each(function ($permission) {
                $this->ensureModelSharesGuard($permission);
            })
            ->all();
        $this->permissions()->saveMany($permissions);
        $this->forgetCachedPermissions();
        return $this;
    }

    /**
     * Remove all current permissions and set the given ones.
     *
     * @param string|array|\StreamerDash\Perms\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionTo($permissions);
    }

    /**
     * Revoke the given permission.
     *
     * @param \StreamerDash\Perms\Contracts\Permission|\StreamerDash\Perms\Contracts\Permission[]|string|string[] $permission
     *
     * @return $this
     */
    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($this->getStoredPermission($permission));
        $this->forgetCachedPermissions();
        return $this;
    }

    /**
     * @param string|array|\StreamerDash\Perms\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return \StreamerDash\Perms\Contracts\Permission|\StreamerDash\Perms\Contracts\Permission[]|\Illuminate\Support\Collection
     */
    protected function getStoredPermission($permissions)
    {
        if (is_string($permissions)) {
            return app(Permission::class)->findByName($permissions, $this->getDefaultGuardName());
        }
        if (is_array($permissions)) {
            return app(Permission::class)
                ->whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }
        return $permissions;
    }

    /**
     * @param \StreamerDash\Perms\Contracts\Permission|\StreamerDash\Perms\Contracts\Role $roleOrPermission
     *
     * @throws \StreamerDash\Perms\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (! $this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }

    protected function getGuardNames(): Collection
    {
        if ($this->guard_name) {
            return collect($this->guard_name);
        }
        return collect(config('auth.guards'))
            ->map(function ($guard) {
                return config("auth.providers.{$guard['provider']}.model");
            })
            ->filter(function ($model) {
                return get_class($this) === $model;
            })
            ->keys();
    }

    protected function getDefaultGuardName(): string
    {
        $default = config('auth.defaults.guard');
        return $this->getGuardNames()->first() ?: $default;
    }

    /**
     * Forget the cached permissions.
     */
    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}