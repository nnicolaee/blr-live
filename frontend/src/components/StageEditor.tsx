import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';
import { useStageState } from '../states.ts';
import Scoreboard from './Scoreboard.tsx';
import Match from './Match.tsx';
import Bracket from './Bracket.tsx';

export default function StageEditor({ stageName, currentStatus }) {
	if(!stageName) return;

	const { bracket, scoreboard, matches } = useStageState(stageName);

	const currentMatch = matches.find(match => match.id == currentStatus.match);
	const teams = scoreboard.map(line => line.team);

	async function deleteStage() {
		if(stageName !== prompt(`Are you sure you want to delete ${stageName}? To confirm, please write its name:`)) {
			return;
		}

		await api(`/stages/${encodeURIComponent(stageName)}`, 'DELETE');
		window.location.reload();
	}

	async function announceCurrentStage() {
		await api('/currentStatus', 'PUT', {
			stage: stageName
		});
	}

	async function announceCurrentMatch(matchId) {
		await api('/currentStatus', 'PUT', { match: matchId });
	}

	async function addParticipation(username) {
		await api('/stages/' + encodeURIComponent(stageName) + '/teams/' + encodeURIComponent(username), 'PUT');
	}

	async function deleteParticipation({ username }) {
		await api('/stages/' + encodeURIComponent(stageName) + '/teams/' + encodeURIComponent(username), 'DELETE');
	}

	async function qualifyTeam({ username }) {
		await api('/stages/' + encodeURIComponent(stageName) + '/teams/' + encodeURIComponent(username), 'PATCH', {
			status: 'qualified'
		});
	}

	async function disqualifyTeam({ username }) {
		await api('/stages/' + encodeURIComponent(stageName) + '/teams/' + encodeURIComponent(username), 'PATCH', {
			status: 'disqualified'
		});
	}

	async function createMatch(e) {
		e.preventDefault();

		const team1 = e.srcElement.elements.team1.value;
		const team2 = e.srcElement.elements.team2.value;

		await api('/matches', 'POST', {
			stage: stageName,
			team1: team1,
			team2: team2
		});
	}

	async function deleteMatch(match) {
		if(!confirm('Delete match?')) return;

		await api('/matches/' + match, 'DELETE');
	}

	async function createGroupMatches() {
		const requests = [];

		for(let i = 0; i < teams.length; i++) {
			for(let j = i+1; j < teams.length; j++) {
				const team1 = teams[i];
				const team2 = teams[j];
				console.log(team1.name + " vs " + team2.name);

				requests.push(api('/matches', 'POST', {
					stage: stageName,
					team1: team1.username,
					team2: team2.username
				}));
			}
		}

		await Promise.all(requests);
	}

	async function createBracket() {
		const depth = (Math.log2(scoreboard.length) + 0.9999) | 0;

		const bracket = await api('/brackets', 'POST', { depth });

		await api('/stages/' + encodeURIComponent(stageName), 'PUT', {
			bracket: bracket.id
		});
	}

	async function deleteBracket() {
		await api('/brackets/' + encodeURIComponent(bracket.id), 'DELETE');
	}

	async function finishMatch(match) {
		await api('/matches/' + match + '/finished', 'PUT');
	}

	async function unfinishMatch(match) {
		await api('/matches/' + match + '/finished', 'DELETE');
	}

	async function createGame(outcome) {
		await api('/matches/' + currentMatch.id + '/games', 'POST', {
			outcome: outcome
		});
	}

	async function deleteGame(game) {
		await api('/matches/games/' + game, 'DELETE');
	}

	return <div>
		{ true && <code>{JSON.stringify({scoreboard, matches, bracket, currentStatus})}</code> }
		<h2>{ stageName }</h2>
		<button onClick={deleteStage}>Delete stage</button>
		<button onClick={announceCurrentStage}>Set as current stage</button>

		<Scoreboard scoreboard={scoreboard} actions={{
			'Remove': deleteParticipation,
			'Qualify': qualifyTeam,
			'Disqualify': disqualifyTeam
		}} />

		<form onSubmit={e => { e.preventDefault(); addParticipation(e.srcElement.elements.username.value); e.srcElement.reset(); }}>
			<input type="text" name="username" placeholder="Team to add (username)" data-paste="team.username" />
			<button>Add team to stage</button>
		</form>

		<h3>Current match</h3>

		{ !currentMatch ? <i>Current match not in this stage</i> : <div id="currentMatch">
			<Match score1={currentMatch.score1} team1={currentMatch.team1} score2={currentMatch.score2} team2={currentMatch.team2} />
			<br/>
			{ currentMatch.status == 'upcoming' ? <>
				<button onClick={() => createGame('team1')}>{ currentMatch.team1.name } scores</button>&nbsp;
				<button onClick={() => createGame('team2')}>{ currentMatch.team2.name } scores</button>&nbsp;
				<button onClick={() => createGame('draw')}>Game draw</button>
				{ currentMatch.games.length >= 3 && <>
					There are {currentMatch.games.length} games:
					<button onClick={() => finishMatch(currentMatch.id)}>Mark match as finished!</button>
				</> }
			</> : <>Game is finished, with verdict: <b>{currentMatch.status}</b> <button onClick={() => unfinishMatch(currentMatch.id)}>"Un-finish" match</button></> }

			<h4>Games:</h4>
			<ul>
				{ currentMatch.games.map(game => <li>
					{ game.time }: {{
						'team1': <><b>{currentMatch.team1.name}</b> scored</>,
						'team2': <><b>{currentMatch.team2.name}</b> scored</>,
						'draw': <>Drawn</>
					}[game.status]}
					<button onClick={() => deleteGame(game.id)}>(remove)</button>
				</li>) }
			</ul>
		</div> }

		<h3>Match list</h3>

		<ul id="matches">
			{ matches.map(match => <li>
				<Match
					score1={match.score1}
					team1={match.team1}
					score2={match.score2}
					team2={match.team2} />
				{ (currentMatch && match.id == currentMatch.id) ? <b>CURRENT MATCH</b> : <button onClick={() => announceCurrentMatch(match.id)}>Set current match</button> }
				<button onClick={() => {deleteMatch(match.id)}}>Delete match</button>
				<span>Id: {match.id}</span>
			</li>) }
			<li>
				<form onSubmit={createMatch}>
					<select name="team1">
						{ teams.map(team => <option value={team.username}>{team.name}</option>) }
					</select><span> vs. </span>
					<select name="team2">
						{ teams.map(team => <option value={team.username}>{team.name}</option>) }
					</select>
					<button>Create match</button>
				</form>
			</li>
			<li>
				<button onClick={createGroupMatches}>Automatically create round robin matches</button>
			</li>
			<li>
				<button onClick={createBracket}>Create bracket</button>
				
			</li>
		</ul>

		{ bracket && <>
			<h3>Bracket</h3>
			<button onClick={deleteBracket}>Delete bracket</button>

			<div style='overflow-x: auto'>
				<Bracket bracket={bracket} matches={matches} adminMatches={matches} />
			</div>
		</>}
	</div>;
}
