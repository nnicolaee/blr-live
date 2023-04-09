import './LiveScoreLine.css';

export default function LiveScoreLine({ score, name }) {
	return (
		<span class='LiveScoreLine'>
			<span class='score'>{score}</span>
			<span class='name team-name'>{name}</span>
		</span>
	);
}
