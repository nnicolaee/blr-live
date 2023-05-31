<?php

declare(strict_types=1);

namespace BLRLive\Controllers;

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\NonBufferedBody as NonBufferedBody;
use BLRLive\REST\{Controller, HttpRoute};
use BLRLive\Models\LiveEvents;

#[Controller('/sse')]
class SSEController
{
    #[HttpRoute('GET')]
    public static function getLiveEvents(Request $req, Response $res)
    {
        $res = $res
            ->withBody(new NonBufferedBody())
            ->withHeader('Content-Type', 'text/event-stream')
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('X-Accel-Buffering', 'no');

        $sub = new LiveEvents();
        $body = $res->getBody();

        $probe_time = 0.25;
        $max_silence = 4;
        $silence = $max_silence; // get a ping at the very beginning

        while (!connection_aborted()) {
            usleep((int)(1000000 * $probe_time));

            if (!$sub->hasEvent()) {
                $silence += $probe_time;
                if ($silence > $max_silence) {
                    $body->write(": ping :)\n\n");
                    $silence = 0;
                }
            } else {
                while ($event = $sub->getEvent()) {
                    $body->write(sprintf("event: %s\ndata: %s\n\n", $event['event'], json_encode($event['data'])));
                    $silence = 0;
                }
            }
        }
    }
}
