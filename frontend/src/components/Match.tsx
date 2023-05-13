import './Match.css';

export default function Match({ score1, team1, score2, team2 }) {
	return (<div class='Match'>
		<div class='line'><div class='score'>{score1}</div><div class='team team-name'>{team1.name}</div></div>
		<div class='line'><div class='score'>{score2}</div><div class='team team-name'>{team2.name}</div></div>
	</div>);
}
