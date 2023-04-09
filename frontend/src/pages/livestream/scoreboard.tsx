
import Scoreboard from '../../components/Scoreboard.tsx';
import UpcomingMatches from '../../components/UpcomingMatches.tsx';
import BottomBar from '../../components/BottomBar.tsx';

import './scoreboard.css';

import { useState, useEffect } from "preact/hooks";

export default function LivestreamScoreboardScene({ stageUrl }) {
	const [ stage, setStage ] = useState({
		name: 'NOT LOADED',
		scoreboard: [],
		matches: []
	});

	useEffect(async _ => {
		if(!stageUrl) {
			stageUrl = (await fetch('/api/currentState').then(res => res.json())).stage;
		}

		setStage(await fetch(stageUrl).then(res => res.json()));
	}, []);

	return (<>
		<div id="infos">
			<Scoreboard scoreboard={ stage.scoreboard } />
			<UpcomingMatches matches={ stage.matches.filter(match => match.status == 'upcoming') } />
		</div>

		<BottomBar nobg>
			<span id="stage-name">{ stage.name }</span>
		</BottomBar>
	</>);
}
