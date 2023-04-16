<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Authorization;
use App\Models\Runner;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class RunnerController extends BaseController
{
	public function getRunners(Request $request, $teamId)
	{
		$team = Team::where('id', $teamId)->first();
		
		if (!$team) 
			return new JsonResponse([], 404);

		return new JsonResponse(Runner::where('teamId', $teamId)->get());
	}

	public function createRunner(Request $request, $teamId)
	{
		if (!$request->has('firstName', 'lastName', 'speed')) {
			return new JsonResponse(['status' => 'error', 'message' => 'Missing details'], 400);
		}

		$team = Team::where('id', $teamId)->first();
		
		if (!$team) 
			return new JsonResponse([], 404);

		$runner = new Runner();
		$runner->teamId = $team->id;
		$runner->firstName = $request->input('firstName');
		$runner->lastName = $request->input('lastName');
		$runner->speed = $request->input('speed');
		$runner->token = Authorization::generateToken();

		$runner->save();

		return new JsonResponse($runner);
	}


	public function getRunner(Request $request, $teamId, $runnerId)
	{
		$runner = Runner::where('teamId', $teamId)->where('id', $runnerId)->first();

		return new JsonResponse($runner, $runner ? 200 : 404);
	}

	public function updateRunner(Request $request, $teamId, $runnerId)
	{
		$runner = Runner::where('teamId', $teamId)->where('id', $runnerId)->first();

		if (!$runner) {
			return new JsonResponse([], 404);
		}

		if ($request->has('firstName')) $runner->firstName = $request->input('firstName');
		if ($request->has('lastName')) $runner->lastName = $request->input('lastName');
		if ($request->has('speed')) $runner->speed = $request->input('speed');

		$runner->save();

		return new JsonResponse($runner);
	}

	public function deleteRunner(Request $request, $teamId, $runnerId)
	{
		$runner = Runner::where('teamId', $teamId)->where('id', $runnerId)->first();

		if (!$runner) {
			return new JsonResponse(['success' => false], 404);
		}

		$runner->delete();

		return new JsonResponse(['success' => true]);
	}
}
