<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = config('auth.basic.user');
        $password = config('auth.basic.password');

        if ($request->getUser() !== $user || $request->getPassword() !== $password) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Web Watcher"',
            ]);
        }

        return $next($request);
    }
}
