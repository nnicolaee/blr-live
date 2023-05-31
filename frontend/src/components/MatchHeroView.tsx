import { useState, useEffect } from 'preact/hooks';
import ring from '../images/ring.png';
import Textfit from './Textfit.tsx';
import api from '../api.ts';
import './MatchHeroView.css';

export default function MatchHeroView({ match }) {
	const [stage, setStage] = useState(null);
	const team1 = match.team1;
	const team2 = match.team2;
	
	return <div class='MatchHeroView'>
		<div class='info'>
			<div class='robot-info side-left'>
				{ team1 && <Textfit>{team1.name}</Textfit> }
			</div>
			<div class='robot-info side-right'>
				{ team2 && <Textfit>{team2.name}</Textfit> }
			</div>
			<div class='score'>
				<span>{match.score1}</span>
				<span>-</span>
				<span>{match.score2}</span>
			</div>
		</div>
		<div class='ring'>
			<img src={ring} alt='' />
			{ team1 && <img class='robot-img side-left' src={team1.image} alt='' /> }
			{ team2 && <img class='robot-img side-right' src={team2.image} alt='' /> }
		</div>
	</div>;
}
