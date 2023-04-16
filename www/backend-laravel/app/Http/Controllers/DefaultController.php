<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Authorization;
use App\Models\Runner;
use App\Models\Stage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class DefaultController extends BaseController
{
    public function login(Request $request) 
	{
		if (!$request->has('token')) {
			return Authorization::unauthorized();
		}

		$user = Runner::where('token', $request->input('token'))->first();

		if (!$user) {
			return Authorization::unauthorized();
		}

		return new JsonResponse([
			'status' => 'success',
			'user' => $user
		]);
	}

	public function getStages(Request $request)
	{
		$response = new JsonResponse(Stage::orderBy('id', 'asc')->get());
		$response->header("Access-Control-Allow_origin", "*");
		return $response;
	}
}
