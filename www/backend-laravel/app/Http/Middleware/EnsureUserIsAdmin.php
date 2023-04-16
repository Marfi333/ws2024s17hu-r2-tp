<?php

namespace App\Http\Middleware;

use App\Models\Runner;
use Closure;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureUserIsAdmin extends Middleware
{
    public function handle($request, Closure $next)
	{
		if (!$request->user || !$request->user->isAdmin)
		{
			return self::unauthorized();
		}

		return $next($request);
	}

	public static function unauthorized(): JsonResponse
	{
		return new JsonResponse([
			'status' => 'error',
			'message' => 'Admin area'
		], 401);
	}
}
