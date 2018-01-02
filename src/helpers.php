<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 9:36 PM
 *
 */


/**
 * @param string $guard
 * @return mixed
 */
function getModelForGuard(string $guard)
{
    return collect(config('auth.guards'))
        ->map(function ($guard) {
            return config("auth.providers.{$guard['provider']}.model");
        })->get($guard);
}

/**
 * @return bool
 */
function isNotLumen() : bool
{
    return ! preg_match('/lumen/i', app()->version());
}