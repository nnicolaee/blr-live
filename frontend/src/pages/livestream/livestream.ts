import { useEffect, useState } from 'preact/hooks';

export function getCurrentMatch() {
	let [ state, setState ] = useState({
		'team1name': 'NOT LOADED', 'team1score': 0,
		'team2name': 'NOT LOADED', 'team2score': 0
	});

	useEffect(async _ => {
		const currentState = await fetch('/api/currentState').then(res => res.json());
		const match = await fetch(currentState.match).then(res => res.json());

		setState({
			'team1name': match.team1.name, 'team1score': match.score1,
			'team2name': match.team2.name, 'team2score': match.score2
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
