<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class TeamController extends BaseController
{
	public function getTeam(Request $request, $teamId)
	{
		$team = Team::where('id', $teamId)->first();
		return $team ? new JsonResponse($team) : new JsonResponse([], 404);
	}

	public function updateTeam(Request $request, $teamId)
	{
		$team = Team::where('id', $teamId)->first();

		if (!$team) {
			return new JsonResponse([], 404);
		}

		if ($request->has('name')) $team->name = $request->input('name');
		if ($request->has('location')) $team->location = $request->input('location');
		if ($request->has('contactEmail')) $team->contactEmail = $request->input('contactEmail');

		$team->save();

		return new JsonResponse($team);
	}

	public function deleteTeam(Request $request, $teamId)
	{
		$team = Team::where('id', $teamId)->first();

		$success = false;
		if ($team) {
			$team->delete();
			$success = true;
		}

		return new JsonResponse([
			'success' => $success
		], $success ? 200 : 404);
	}
}