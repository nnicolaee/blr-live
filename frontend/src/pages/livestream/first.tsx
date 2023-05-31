import LiveRobot from '../../components/LiveRobot.tsx';

import blrLogo from '../../images/BLR_Logo_white.png';
import robot1img from '../../images/hand1.png';
import robot2img from '../../images/hand2.png';

import { obsSceneState } from './livestream.ts';
import { useCurrentStatus, useStageState } from '../../states.ts';

import './first.css';

export default function LivestreamFirstScene() {
	const currentStatus = useCurrentStatus();
	const { matches } = useStageState(currentStatus.stage);
	const nl = {
		username: '~',
		name: 'NOT LOADED',
		image: '~'
	};
	const currentMatch = matches.find(match => match.id == currentStatus.match) || { team1: nl, team2: nl, score1: 0, score2: 0 };
	const { team1, team2, score1, score2 } = currentMatch;

	let scene = obsSceneState();

	return (<div class={ scene === 'Showoff' ? 'state-showoff' : 'state-title' }>
		<img id="blr-logo-top" alt="" src={blrLogo} width="900" />
		<LiveRobot id="left-robot" side="left" name={team1.name} score={score1} img={team1.image} />
		<LiveRobot id="right-robot" side="right" name={team2.name} score={score2} img={team2.image} />
		<span id="score-sep">-</span>
	</div>);
}
