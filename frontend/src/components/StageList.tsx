import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';

export default function StageList({ setSelectedStage, selectedStage, currentStage, admin = false }) {
	const [ stages, setStages ] = useState([]);

	useEffect(async () => {
		setStages((await api('/stages')).stages);
	}, []);

	async function createStage(name) {
		await api('/stages', 'POST', { name: name });
		window.location.reload();
	}

	return <ul class="StageList">
		{ stages.map(stage => <li className={stage.name == selectedStage ? 'selected' : ''}>
			<a href='javascript:;' onClick={() => setSelectedStage(stage.name)}>
				{ stage.name } { stage.name == currentStage ? '(current stage)' : '' }
			</a>
		</li>) }
		{ admin && <li>
			<form onSubmit={(e) => { e.preventDefault(); createStage(e.srcElement.elements.name.value); e.srcElement.reset(); }}>
				<input type="text" name="name" placeholder="New stage name"/>
				<button>Create stage</button>
			</form>
		</li> }
	</ul>;
}
