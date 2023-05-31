import { useEffect, useState } from 'preact/hooks';
import api from '../../api.ts';

export function getCurrentMatch() {
	let [ state, setState ] = useState({
		'team1name': 'NOT LOADED', 'team1score': 0,
		'team2name': 'NOT LOADED', 'team2score': 0
	});

	async function reloadMatch() {
		const currentState = await api('/currentStatus');
		const match = await api('/matches/' + currentState.match);

		setState({
			'team1name': match.team1.name, 'team1score': match.score1,
			'team2name': match.team2.name, 'team2score': match.score2
		});
	}

	useEffect(reloadMatch, []);

	useEffect(async () => {
		const sse = new EventSource('/api/sse');

		sse.addEventListener('currentStatus', (e) => {
			console.log(e);
			reloadMatch();
		});

		sse.addEventListener('gameOutcome', (e) => {
			console.log(e);
			reloadMatch();
		});
	}, []);

	return state;
}

export function obsSceneState(handler) {
	let [ scene, setScene ] = useState('title');
	useEffect(_ => {
		window.addEventListener('obsSceneChanged', function(event) {
			if(handler) handler(event.detail.name);
			setScene(event.detail.name);
		});
	}, []);

	return scene;
}
