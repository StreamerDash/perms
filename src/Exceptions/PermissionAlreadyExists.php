<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:27 PM
 */

namespace StreamerDash\Perms\Exceptions;

use InvalidArgumentException;

class PermissionAlreadyExists extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName)
    {
        return new static("A `{$permissionName}` permission already exists for guard `{$guardName}`.");
    }
}