import './EmptyMatch.css';

export default function EmptyMatch({ options, setOption }) {
	return <div class='EmptyMatch'>
		{ options && <form onSubmit={(e) => {e.preventDefault(); setOption(e.srcElement.elements.match.value);}}>
			<select name="match">
				<option disabled selected value>Assign match</option>
				{ options.map((match, i) => <option value={i}>{match.team1.name} vs {match.team2.name}</option>) }
			</select>
			<button>Set match</button>
		</form> }
	</div>;
}
