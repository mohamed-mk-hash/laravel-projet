<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCheckMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->user();
        // dd($admin);
        
        if (!$admin) {
            return response()->json(['message' => 'You are Unauthorized'], 401);
        }

        if ($admin->role != 2) {
            return response()->json(['message' => 'Users can not login'], 403);
        }

        return $next($request);
    }
}
