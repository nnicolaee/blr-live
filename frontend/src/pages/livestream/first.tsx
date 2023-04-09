import LiveRobot from '../../components/LiveRobot.tsx';

import blrLogo from '../../images/BLR_Logo_white.png';
import robot1img from '../../images/hand1.png';
import robot2img from '../../images/hand2.png';

import { getCurrentMatch, obsSceneState } from './livestream.ts';

import './first.css';

export default function LivestreamFirstScene() {

	let { team1name, team2name, team1score, team2score } = getCurrentMatch();
	let scene = obsSceneState();

	return (<div class={ scene === 'Showoff' ? 'state-showoff' : 'state-title' }>
		<img id="blr-logo-top" alt="" src={blrLogo} width="900" />
		<LiveRobot id="left-robot" side="left" name={team1name} score={team1score} img={robot1img} />
		<LiveRobot id="right-robot" side="right" name={team2name} score={team2score} img={robot2img} />
		<span id="score-sep">-</span>
	</div>);
}
