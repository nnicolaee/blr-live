import './BottomBar.css';
import blrLogo from '../images/BLR_Logo_white.png';

export default function BottomBar({ nobg, children }) {
	return (<div class={'BottomBar' + (nobg ? ' nobg' : '')}>
		<img id="blr-logo-bottom" alt="" src={blrLogo} height="168" />
		<div>{children}</div>
	</div>);
}
