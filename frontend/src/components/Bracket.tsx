import { useState, useEffect } from 'preact/hooks';
import Match from './Match.tsx';
import EmptyMatch from './EmptyMatch.tsx';
import api from '../api.ts';
import './Bracket.css';

function Bracket({ bracket, matches, adminMatches }) {
	const [match, setMatch] = useState(null);

	useEffect(async () => {
		setMatch(matches.find(match => match.id === bracket.match));
	}, [bracket.match, matches]);

	async function assignMatch(x) {
		console.log('Bracket', bracket.id, 'assigned match', adminMatches[x].id);

		await api('/brackets/' + bracket.id, 'PUT', {
			match: adminMatches[x].id
		});

		setMatch(adminMatches[x]);
	}

	async function unassignMatch() {
		await api('/brackets/' + bracket.id, 'PUT', {
			match: null
		});

		setMatch(null);
	}

	return <div class='Bracket'>
		{ match ?
			<div style='position: relative'>
				<Match team1={match.team1} score1={match.score1} team2={match.team2} score2={match.score2} />
				{ match && adminMatches && <button style='position: absolute; right: 0; top: 50%; transform: translateY(-50%);' onClick={unassignMatch}>Unassign</button>}
			</div> : 
			adminMatches ?
				<EmptyMatch options={adminMatches} setOption={assignMatch} /> :
				<EmptyMatch/> }
		{ bracket.children && <>
			{ bracket.children[0] && <Bracket bracket={bracket.children[0]} matches={matches} adminMatches={adminMatches} /> }
			{ bracket.children[1] && <Bracket bracket={bracket.children[1]} matches={matches} adminMatches={adminMatches} /> }
		</> }
	</div>;
}

export default Bracket;
