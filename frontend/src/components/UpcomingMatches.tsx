import Match from './Match.tsx';
import './UpcomingMatches.css';

export default function UpcomingMatches({ matches }) {
	if(!matches) {
		matches = [];
	}
	
	return (
		<div class='UpcomingMatches'>
		<h2>Upcoming matches</h2>
		<ul>
			{ matches.filter(match => match.status == 'upcoming').map(match => (<li>
				<Match score1={match.score1} team1={match.team1} score2={match.score2} team2={match.team2} />
			</li>)) }
		</ul>
		</div>
	);
}
