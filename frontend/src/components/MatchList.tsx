import Match from './Match.tsx';
import './MatchList.css';

export default function MatchList({ caption='Upcoming matches', matches }) {
	if(!matches) {
		matches = [];
	}
	
	return (
		<div class='MatchList'>
		<h2>{ caption }</h2>
		<ul>
			{ matches.map(match => (<li>
				<Match score1={match.score1} team1={match.team1} score2={match.score2} team2={match.team2} />
			</li>)) }
		</ul>
		</div>
	);
}
