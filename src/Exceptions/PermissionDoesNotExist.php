<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:28 PM
 */

namespace StreamerDash\Perms\Exceptions;

use InvalidArgumentException;

class PermissionDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName = '')
    {
        return new static("There is no permission named `{$permissionName}` for guard `{$guardName}`.");
    }
}