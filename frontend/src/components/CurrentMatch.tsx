import ring from '../images/ring.png';
import Textfit from './Textfit.tsx';
import './CurrentMatch.css';

import bot2 from '../images/bot2.png';
import bot3 from '../images/bot3.png';

export default function CurrentMatch() {
	return <div class='CurrentMatch'>
		<div class='info'>
			<div class='robot-info side-left'>
				<Textfit>{'Malcom Sylas Edjouma Laouari'}</Textfit>
			</div>
			<div class='robot-info side-right'>
				<Textfit>{'S.P.A.R.T.'}</Textfit>
			</div>
			<div class='score'>
				<span>{1}</span>
				<span>-</span>
				<span>{2}</span>
			</div>
		</div>
		<div class='ring'>
			<img src={ring} alt='' />
			<img class='robot-img side-left' src={bot2} alt='' />
			<img class='robot-img side-right' src={bot3} alt='' />
		</div>
	</div>;
}
