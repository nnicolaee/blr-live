<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use BLRLive\Config;
use BLRLive\Models\CurrentStatus;

class CurrentStatusController {
	public static function currentStatusJson(CurrentStatus $currentStatus) {
		return [
			'stage' => $currentStatus->stage,
			'match' => $currentStatus->match,
			'livestream' => $currentStatus->livestream
		];
	}

	public static function on($app) {
		$app->get('/currentStatus', function (Request $req, Response $res, $args) {
			$currentStatus = CurrentStatus::get();
			return $res->withJson(CurrentStatusController::currentStatusJson($currentStatus));
		});

		$app->put('/currentStatus', function (Request $req, Response $res, $args) {
			$data = $req->getParsedBody();
			$currentStatus = CurrentStatus::get();

			if(is_string($data['stage'])) {
				$currentStatus->stage = StageController::urlStage($data['stage']) or throw new HttpNotFoundException($req, 'Referenced stage does not exist');
			}

			if(is_string($data['match'])) {
				$currentStatus->match = MatchController::urlMatch($data['match']) or throw new HttpNotFoundException($req, 'Referenced match does not exist');
			}

			if(is_string($data['livestream'])) {
				$currentStatus->livestream = $data['livestream'];
			}

			$currentStatus->save();
			return $res->withJson(CurrentStatusController::currentStatusJson($currentStatus));
		});
	}
}
