<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;


class AdminAuthenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('admin.api.login');
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            abort(response()->json(['message' => 'Unauthorized'], 401));
        }

        return redirect()->route('admin.api.login');
    }

    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('admin')->check()) {
            return $this->auth->shouldUse('admin');
        }

        $this->unauthenticated($request, ['admin']);
    }
}


















// class AdminAuthenticate extends Middleware
// {
//     protected function redirectTo(Request $request): ?string
//     {
//         // Redirect to the admin login page if the request expects JSON, return null
//         return $request->expectsJson() ? null : route('admin.api.login');
//     }

//     protected function unauthenticated($request, array $guards)
//     {
//         if ($request->expectsJson()) {
//             abort(response()->json(['message' => 'Unauthorized'], 401));
//         }

//         // Redirect to the admin login page
//         return redirect()->route('admin.api.login');
//     }

//     protected function authenticate($request, array $guards)
//     {
//         if ($this->auth->guard('admin_api')->check()) {
//             return $this->auth->shouldUse('admin_api');
//         }

//         $this->unauthenticated($request, ['admin_api']);
//     }
// }















// namespace App\Http\Middleware;

// use Illuminate\Auth\Middleware\Authenticate as Middleware;
// use Illuminate\Http\Request;

// class AdminAuthenticate extends Middleware
// {
//     /**
//      * Get the path the user should be redirected to when they are not authenticated.
//      */
//     protected function redirectTo(Request $request): ?string
//     {
//         return $request->expectsJson() ? null : route('admin.login');
//     }

//     protected function authenticate($request, array $guards)
//     {
//         if ($this->auth->guard('admin')->check()) {
//             return $this->auth->shouldUse('admin');
//         }

//         $this->unauthenticated($request, ['admin']);
//     }
// }
