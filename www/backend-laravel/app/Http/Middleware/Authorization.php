<?php

namespace App\Http\Middleware;

use App\Models\Runner;
use Closure;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Illuminate\Http\JsonResponse;

class Authorization extends Middleware
{
    public function handle($request, Closure $next)
	{
		if (!$request->bearerToken()) {
			return self::unauthorized();
		}

		$user = Runner::where('token', $request->bearerToken())->first();

		if (!$user) {
			return self::unauthorized();
		}

		$request->user = $user;

		return $next($request);
	}

	public static function unauthorized(): JsonResponse
	{
		return new JsonResponse([
			'status' => 'error',
			'message' => 'Login failed'
		], 401);
	}

	public static function generateToken(): int 
	{
		return random_int(100000000, 999999999);
	}
}
