<?php

namespace StreamerDash\Perms;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;
use StreamerDash\Perms\Contracts\Permission;
use StreamerDash\Perms\Exceptions\PermissionDoesNotExist;

/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:13 PM
 */

class PermissionRegistrar
{
    /** @var \Illuminate\Contracts\Auth\Access\Gate */
    protected $gate;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var string */
    protected $cacheKey = 'streamerdash.permission.cache';

    public function __construct(Gate $gate, Repository $cache)
    {
        $this->gate = $gate;
        $this->cache = $cache;
        $this->cacheKey = explode('.', request()->getHttpHost())[0] . '.streamerdash.permission.cache';
    }

    public function registerPermissions(): bool
    {
        $this->gate->before(function (Authenticatable $user, string $ability) {
            try {
                if (method_exists($user, 'hasPermissionTo')) {
                    return $user->hasPermissionTo($ability) ?: null;
                }
            } catch (PermissionDoesNotExist $e) {
            }
        });
        return true;
    }

    public function forgetCachedPermissions()
    {
        $this->cache->forget($this->cacheKey);
    }

    public function getPermissions(): Collection
    {
        return $this->cache->remember($this->cacheKey, config('permission.cache_expiration_time'), function () {
            return app(Permission::class)->with('roles')->get();
        });
    }
}