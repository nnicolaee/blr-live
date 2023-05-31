import { useEffect, useState } from 'preact/hooks';
import api from './api.ts';

let _sse = null;
export function BLRSSE() {
	return _sse = _sse || new EventSource('/api/sse');
}

export function useCurrentStatus() {
	const [currentStatus, setCurrentStatus] = useState({
		stage: null,
		match: null,
		livestream: null
	});

	useEffect(async () => {
		const currentStatus = await api('/currentStatus');
		setCurrentStatus(currentStatus);

		const es = BLRSSE();
		es.addEventListener('currentStatus', (e) => {
			setCurrentStatus(JSON.parse(e.data));
		});
	}, []);

	return currentStatus;
}

export function useStageState(stageName) {
	if(!stageName) return { bracket: null, scoreboard: [], matches: [] };

	const [bracket, setBracket] = useState(null);
	const [scoreboard, setScoreboard] = useState([]);
	const [matches, setMatches] = useState([]);

	let throttle = null;
	function refresh(e) {
		if(throttle) clearTimeout(throttle);

		throttle = setTimeout(async () => {
			const stage = await api('/stages/' + encodeURIComponent(stageName));
			console.dir(stage);

			setMatches && setMatches(stage.matches);
			setScoreboard && setScoreboard(stage.scoreboard);

			if(stage.bracket === null) {
				setBracket && setBracket(null);
			} else {
				const bracket = await api('/brackets/' + stage.bracket);
				setBracket && setBracket(bracket);
			}

			throttle = null;
		}, 100);
	}

	useEffect(async () => {
		refresh();
	}, [stageName]);

	useEffect(async () => {
		const es = BLRSSE();
		es.addEventListener('scoreboard', refresh);
		es.addEventListener('bracket', refresh);
		es.addEventListener('match', refresh);
	}, []);

	return { bracket, scoreboard, matches };
}

export function useMatchState(matchId, { onGame }) {
	useEffect(async () => {
		const es = BLRSSE();

		es.addEventListener('game', (e) => {
			const data = JSON.parse(e.data);

			if(data.match === matchId) {
				onGame && onGame(data);
			}
		});
	}, [matchId]);
}
