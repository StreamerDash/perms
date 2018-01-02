<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:23 PM
 */

namespace StreamerDash\Perms\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany;

    /**
     * Find a permission by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \StreamerDash\Perms\Exceptions\PermissionDoesNotExist
     *
     * @return Permission
     */
    public static function findByName(string $name, $guardName): self;
}