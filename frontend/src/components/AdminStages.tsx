import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';
import Scoreboard from './Scoreboard.tsx';
import Match from './Match.tsx';
import './AdminStages.css';

export default function AdminStages({}) {
	const [stages, setStages] = useState([]);
	const [selectedStage, setSelectedStage] = useState(null);
	const [stageInfo, setStageInfo] = useState(null);
	const [currentStage, setCurrentStage] = useState(null);

	async function reloadStages() {
		setStages((await api('/stages')).stages);
	}

	async function reloadStage() {
		setStageInfo(await api('/stages/' + encodeURIComponent(selectedStage)));
	}

	useEffect(async () => {
		reloadStages();

		const currentStage = (await api('/currentStatus')).stage;
		setCurrentStage(currentStage);
		setSelectedStage(currentStage);
	}, []);

	useEffect(async () => {
		if(!selectedStage) return;

		reloadStage();
	}, [selectedStage]);

	async function createStage(e) {
		e.preventDefault(); // don't submit :)

		await api('/stages', 'POST', {
			name: e.srcElement.elements.name.value
		});

		e.srcElement.reset(); // clear form
		reloadStages();
	}

	async function deleteCurrentStage() {
		if(selectedStage !== prompt(`Are you sure you want to delete ${selectedStage}? To confirm, please write its name:`)) {
			return;
		}

		await api(`/stages/${encodeURIComponent(selectedStage)}`, 'DELETE');
		reloadStages();
	}

	async function announceCurrentStage() {
		await api('/currentStatus', 'PUT', {
			stage: selectedStage
		});
		setCurrentStage(selectedStage);
	}

	async function addParticipation(e) {
		e.preventDefault();

		const team = e.srcElement.elements.username.value;

		await api('/stages/' + encodeURIComponent(selectedStage) + '/teams/' + encodeURIComponent(team), 'PUT');
		reloadStage();
	}

	async function deleteParticipation({ username }) {
		await api('/stages/' + encodeURIComponent(selectedStage) + '/teams/' + encodeURIComponent(username), 'DELETE');
		reloadStage();
	}

	async function qualifyTeam({ username }) {
		await api('/stages/' + encodeURIComponent(selectedStage) + '/teams/' + encodeURIComponent(username), 'PATCH', {
			status: 'qualified'
		});
		reloadStage();
	}

	async function disqualifyTeam({ username }) {
		await api('/stages/' + encodeURIComponent(selectedStage) + '/teams/' + encodeURIComponent(username), 'PATCH', {
			status: 'disqualified'
		});
		reloadStage();
	}

	return <div class='AdminStages'>
		<h2>Stages</h2>
		<ul>
			{ stages.map(stage => (<li className={stage.name == selectedStage ? 'selected' : ''}>
				<a	href='#'
					onClick={() => setSelectedStage(stage.name)}>
					{stage.name} { stage.name == currentStage && ' (current stage)'}
				</a>
			</li>))}
			<li>
				<form onSubmit={createStage}>
					<input type="text" name="name" placeholder="New stage name"/>
					<button>Create stage</button>
				</form>
			</li>
		</ul>
		{ stageInfo && 
			<div>
				<h2>{ stageInfo.name }</h2>
				{/* <pre>{ JSON.stringify(stageInfo) }</pre> */}
				<button onClick={deleteCurrentStage}>Delete stage</button>
				<button onClick={announceCurrentStage}>Set as current stage</button>

				<Scoreboard scoreboard={stageInfo.scoreboard} actions={{
					'Remove': deleteParticipation,
					'Qualify': qualifyTeam,
					'Disqualify': disqualifyTeam
				}} />

				<form onSubmit={addParticipation}>
					<input type="text" name="username" placeholder="username" data-paste="team.username" />
					<button>Add team to stage</button>
				</form>

				<h3>Matches</h3>

				{ stageInfo.matches.map(match => <Match score1={match.score1} team1={match.team1} score2={match.score2} team2={match.team2} />) }
			</div>
		}
	</div>;
}
