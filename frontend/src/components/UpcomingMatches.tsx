import './UpcomingMatches.css';

function Match({ score1, team1, score2, team2 }) {
	return (<div class='UpcomingMatch'>
		<div class='line'><div class='score'>{score1}</div><div class='team team-name'>{team1.name}</div></div>
		<div class='line'><div class='score'>{score2}</div><div class='team team-name'>{team2.name}</div></div>
	</div>);
}

export default function UpcomingMatches({ matches }) {
	return (
		<div class='UpcomingMatches'>
		<h2>Upcoming matches</h2>
		<ul>
			{ matches.map(match => (<li>
				<Match score1={match.score1} team1={match.team1} score2={match.score2} team2={match.team2} />
			</li>)) }
		</ul>
		</div>
	);
}
