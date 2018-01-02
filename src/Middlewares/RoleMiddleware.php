<?php
/**
 * Created by PhpStorm.
 * User: josh
 * Date: 1/1/18
 * Time: 10:38 PM
 */

namespace StreamerDash\Perms\Middlewares;


use Closure;
use Illuminate\Support\Facades\Auth;
use StreamerDash\Perms\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        if (! Auth::user()->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}