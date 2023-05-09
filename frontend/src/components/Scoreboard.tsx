import './Scoreboard.css';

const medals = [
	<><svg width="24" height="24" viewBox="0 0 6.3 6.4" xmlns="http://www.w3.org/2000/svg"><title>1st place</title><circle style="fill:#ffd645;fill-rule:evenodd;stroke-width:.264583;fill-opacity:1" cx="3.2" cy="3.2" r="3.2"/><circle style="fill:#000;fill-opacity:.3;fill-rule:evenodd;stroke-width:.0881943" cx="3.2" cy="3.2" r="1.1"/></svg></>,
	<><svg width="24" height="24" viewBox="0 0 6.3 6.4" xmlns="http://www.w3.org/2000/svg"><title>2nd place</title><rect style="fill:#c5d1cd;stroke-width:.264583" width="6.3" height="6.3" rx="1.6"/><circle style="fill:#000;fill-opacity:.313725;stroke-width:.264583" cx="2" cy="2" r=".9"/><circle style="fill:#000;fill-opacity:.313725;stroke-width:.264583" cx="4.4" cy="4.4" r=".9"/></svg></>,
	<><svg width="24" height="24" viewBox="0 0 6.3 6.4" xmlns="http://www.w3.org/2000/svg"><title>3rd place</title><rect style="fill:#be7b2d;fill-opacity:1;stroke-width:.219105" width="5.3" height="5.3" x="1.9" y="-2.6" rx="1.3" transform="rotate(45)"/><circle style="fill:#000;fill-opacity:.313725;stroke-width:.226785" cx="4.2" cy="4" r=".8"/><circle style="fill:#000;fill-opacity:.313725;stroke-width:.226785" cx="2.1" cy="4" r=".8"/><circle style="fill:#000;fill-opacity:.313725;stroke-width:.226785" cx="3.2" cy="2.1" r=".8"/></svg></>
];

export default function Scoreboard({ scoreboard }) {
	//if(!scoreboard) {
		scoreboard = [
			{team: {name: 'Nea mulan'}, score: 13, tiebreaker: 23, status: 'qualified'},
			{team: {name: 'Panzerkampfwagen'}, score: 13, tiebreaker: 4, status: 'qualified'},
			{team: {name: 'Masakrator'}, score: 12, tiebreaker: -5, status: 'participant'},
			{team: {name: 'S.P.A.R.T.'}, score: 10, tiebreaker: 3, status: 'participant'},
			{team: {name: 'Malcom Sylas Edjouma Laouari'}, score: 5, tiebreaker: 1, status: 'participant'},
			{team: {name: 'Shigoku Khazzan'}, score: 3, tiebreaker: 5, status: 'disqualified'},
			{team: {name: 'Shobolinsky'}, score: 2, tiebreaker: 0, status: 'participant'},
			{team: {name: 'Tzanca Hurricane'}, score: -1, tiebreaker: 4, status: 'disqualified'},
		];
	//}
	
	return (
		<div class='Scoreboard'>
		<h2>Scoreboard</h2>
		<table>
			<thead>
				<tr><th></th><th></th><th>Score</th><td>&plusmn;</td></tr>
			</thead>
			<tbody>
				{
					scoreboard.map((line, i) => (<tr class={'status-' + line.status}>
						<td>{i+1}</td>
						<td><span class='team-name'>{line.team.name}</span> { i < 3 ? medals[i] : '' }</td>
						<td>{line.score}</td>
						<td>{line.tiebreaker}</td>
					</tr>))
				}
			</tbody>
		</table>
		</div>
	);
}


