<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:31 PM
 */

namespace StreamerDash\Perms\Exceptions;

use InvalidArgumentException;

class RoleDoesNotExist extends InvalidArgumentException
{
    public static function named(string $roleName)
    {
        return new static("There is no role named `{$roleName}`.");
    }
    public static function withId(int $roleId)
    {
        return new static("There is no role with id `{$roleId}`.");
    }
}