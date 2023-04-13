<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use BLRLive\Config;
use BLRLive\Models\CurrentStatus;

use BLRLive\REST\{Controller, HttpRoute};

#[Controller('/currentStatus')]
class CurrentStatusController
{
	#[HttpRoute('GET')]
	public static function getCurrentStatus(Request $req, Response $res) {
		return $res->withJson(CurrentStatus::get());
	}

	#[HttpRoute('PUT')]
	public static function updateCurrentStatus(Request $req, Response $res) {
		$data = $req->getParsedBody();
		$currentStatus = CurrentStatus::get();

		if(isset($data['stage']) && is_string($data['stage'])) {
			$currentStatus->stage = Stage::fromUrl($data['stage'])?->getId() or throw new HttpNotFoundException($req, 'Referenced stage does not exist');

		}

		if(isset($data['match']) && is_string($data['match'])) {
			$currentStatus->match = MMatch::fromUrl($data['match'])?->getId() or throw new HttpNotFoundException($req, 'Referenced match does not exist');

		}

		if(isset($data['livestream']) && is_string($data['livestream'])) {
			$currentStatus->livestream = $data['livestream'];

		}

		$currentStatus->save();
		return $res->withJson($currentStatus);
	}
}
