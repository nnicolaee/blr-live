<?php

declare(strict_types=1);

namespace BLRLive\Models;

/*

create table CurrentCompetitionStatus (
    stage varchar(50) null,
    match_id int null,
    livestream_url varchar(50) null,

    foreign key (stage) references Stages(name) on delete set null,
    foreign key (match_id) references Matches(id) on delete set null
);

insert into CurrentCompetitionStatus values ('Development', 69, null);

*/

class CurrentStatus extends BaseModel
{
    protected static string $baseUrl = \BLRLive\Config::API_BASE_URL . "/currentStatus";

    public ?string $stage;
    public ?int $match;
    public ?string $livestream;

    public function getId()
    {
        return null;
    }

    public static function get(#[Unused] string $id): CurrentStatus
    {
        $db = Database::connect();
        [ $stage, $match, $livestream ] = $db->query(
            'select stage, match_id, livestream_url from CurrentCompetitionStatus'
        )->fetch_array();
        $db->close();

        $currentStatus = new CurrentStatus();
        $currentStatus->stage = $stage;
        $currentStatus->match = $match;
        $currentStatus->livestream = $livestream;
        return $currentStatus;
    }

    public function save()
    {
        $db = Database::connect();
        $db->execute_query(
            'update CurrentCompetitionStatus set stage = ?, match_id = ?, livestream_url = ?',
            [$this->stage, $this->match, $this->livestream]
        );
        $db->commit();
    }

    public function jsonSerialize(): \BLRLive\Schemas\CurrentStatus
    {
        return new \BLRLive\Schemas\CurrentStatus(
            stage: $this->stage,
            match: $this->match,
            livestream: $this->livestream
        );
    }
}
