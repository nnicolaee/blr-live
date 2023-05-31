import robot1img from '../../images/hand1.png';
import robot2img from '../../images/hand2.png';
import ringImage from '../../images/ring.png';
import blrLogo from '../../images/BLR_Logo_white.png';

import BottomBar from '../../components/BottomBar.tsx';
import LiveRobot from '../../components/LiveRobot.tsx';
import LiveScoreLine from '../../components/LiveScoreLine.tsx';

import { obsSceneState } from './livestream.ts';
import { useCurrentStatus, useStageState, BLRSSE } from '../../states.ts';
import { useEffect } from 'preact/hooks';

import './arena.css';

export default function LivestreamArenaScene() {
	const classes = {
		'Arena': '',
		'WIN LEFT': 'state-winleft',
		'WIN RIGHT': 'state-winright',
		'DRAW': 'state-draw',
	};

	const currentStatus = useCurrentStatus();
	const { matches } = useStageState(currentStatus.stage);
	const nl = {
		username: '~',
		name: 'NOT LOADED',
		image: '~'
	};
	const currentMatch = matches.find(match => match.id == currentStatus.match) || { team1: nl, team2: nl, score1: 0, score2: 0 };
	const { team1, team2, score1, score2 } = currentMatch;

	const currentObsScene = obsSceneState(scene => {
		// Prepare instant replay buffer from the moment the "Arena" scene is active until the moment one of the verdict scenes become active (immediately before their respective animations).
		if(scene == 'Arena') {
			window.obsstudio.startReplayBuffer();
		}
		if(scene == 'WIN LEFT' || scene == 'WIN RIGHT' || scene == 'DRAW') {
			window.obsstudio.saveReplayBuffer();
		}
	});

	useEffect(() => {
		const sse = BLRSSE();
		sse.addEventListener('game', (e) => {
			const data = JSON.parse(e.data);
			console.log('game: ', data);
			window.obsstudio.getCurrentScene(scene => {
				console.log('Scene:', scene.name)
				if(data.match_id == currentMatch.id && scene.name == 'Arena') {
					console.log('ANIM!', data.status);
					if(data.status == 'team1') {
						window.obsstudio.setCurrentScene('WIN LEFT');
					} else if(data.status == 'team2') {
						window.obsstudio.setCurrentScene('WIN RIGHT');
					} else if(data.status == 'draw') {
						window.obsstudio.setCurrentScene('DRAW');
					}
				}
			});
		});	
	}, []);

	return (<div class={ classes[currentObsScene] ?? '' }>
		<div id="left-curtain">
			{ team1.name } scores
		</div>
		<div id="left-robot">
			<LiveRobot justimg side="left" img={team1.image} />
		</div>

		<div id="right-curtain">
			{ team2.name } scores
		</div>
		<div id="right-robot">
			<LiveRobot justimg side="right" img={team2.image} />
		</div>

		<div id="draw-curtain">
			Draw
		</div>

		<BottomBar>
			<LiveScoreLine score={score1} name={team1.name} />
			<LiveScoreLine score={score2} name={team2.name} />
		</BottomBar>
	</div>);
}
