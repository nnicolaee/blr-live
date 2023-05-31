import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';
import { useCurrentStatus, useStageState } from '../states.ts';
import Scoreboard from './Scoreboard.tsx';
import UpcomingMatches from './UpcomingMatches.tsx';
import MatchHeroView from './MatchHeroView.tsx';
import Bracket from './Bracket.tsx';
import './StageView.css';

export default function StageView() {
	const currentStatus = useCurrentStatus();

	const usp = new URLSearchParams(window.location.search);
	const stageName = usp.get('stage') || currentStatus.stage;
	const { bracket, scoreboard, matches } = useStageState(stageName);

	const currentMatch = matches.find(match => match.id == currentStatus.match);

	return <div class='StageView'>
		<h2><a href='/stages'>{ stageName }</a></h2>
		{ currentMatch && <MatchHeroView match={ currentMatch } /> }

		<div class='tables'>
			{ bracket ? 
				<Bracket matches={matches} bracket={bracket} /> :
				<Scoreboard scoreboard={scoreboard} />
			}
			<UpcomingMatches matches={matches} />
		</div>
	</div>;
}
