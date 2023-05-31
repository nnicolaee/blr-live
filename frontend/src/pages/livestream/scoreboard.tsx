import Bracket from '../../components/Bracket.tsx';
import Scoreboard from '../../components/Scoreboard.tsx';
import UpcomingMatches from '../../components/UpcomingMatches.tsx';
import BottomBar from '../../components/BottomBar.tsx';
import api from '../../api.ts';
import './scoreboard.css';

import { useState, useEffect } from "preact/hooks";
import { useCurrentStatus, useStageState } from '../../states.ts';

export default function LivestreamScoreboardScene({ stageName }) {
	const currentStatus = useCurrentStatus();
	const { matches, scoreboard, bracket } = useStageState(currentStatus.stage);

	return (<>
		<div id="infos">
			{ bracket ? <Bracket bracket={bracket} matches={matches} /> : <Scoreboard scoreboard={ scoreboard } /> }
			<UpcomingMatches matches={ matches.filter(match => match.status == 'upcoming') } />
		</div>

		<BottomBar nobg>
			<span id="stage-name">{ currentStatus.stage }</span>
		</BottomBar>
	</>);
}
