import './LiveRobot.css';

export interface Props {
	id: string;
	side: string;
	name: string;
	score: number;
	img: string;
	href: string;
	justimg: string;
}

export default function LiveRobot({ id, side, name, score, img, href, justimg }) {
	return (
	<div class={"LiveRobot side-"+side}>
		<img src={img} alt="" height="480" />
		{ justimg ? [] : <>
			<div class="name team-name">{name}</div>
			<div class="score">{score}</div>
		</> }
	</div>);
}
