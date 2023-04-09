import robot1img from '../../images/hand1.png';
import robot2img from '../../images/hand2.png';
import ringImage from '../../images/ring.png';
import blrLogo from '../../images/BLR_Logo_white.png';

import BottomBar from '../../components/BottomBar.tsx';
import LiveRobot from '../../components/LiveRobot.tsx';
import LiveScoreLine from '../../components/LiveScoreLine.tsx';

import { getCurrentMatch, obsSceneState } from './livestream.ts';

import './arena.css';

export default function LivestreamArenaScene() {
	const classes = {
		'Arena': '',
		'WIN LEFT': 'state-winleft',
		'WIN RIGHT': 'state-winright',
		'DRAW': 'state-draw',
	};

	const { team1name, team2name, team1score, team2score } = getCurrentMatch();
	const scene = obsSceneState(scene => {
		// Prepare instant replay buffer from the moment the "Arena" scene is active until the moment one of the verdict scenes become active (immediately before their respective animations).
		if(scene == 'Arena') {
			window.obsstudio.startReplayBuffer();
		}
		if(scene == 'WIN LEFT' || scene == 'WIN RIGHT' || scene == 'DRAW') {
			window.obsstudio.saveReplayBuffer();
		}
	});

	return (<div class={ classes[scene] ?? '' }>
		<div id="left-curtain">
			{ team1name } scores
		</div>
		<div id="left-robot">
			<LiveRobot justimg side="left" img={robot1img} />
		</div>

		<div id="right-curtain">
			{ team2name } scores
		</div>
		<div id="right-robot">
			<LiveRobot justimg side="right" img={robot2img} />
		</div>

		<div id="draw-curtain">
			Draw
		</div>

		<BottomBar>
			<LiveScoreLine score={team1score} name={team1name} />
			<LiveScoreLine score={team2score} name={team2name} />
		</BottomBar>
	</div>);
}
