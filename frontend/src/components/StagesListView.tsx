import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';
import './StagesListView.css';

export default function StagesListView() {
	const [stages, setStages] = useState([]);

	useEffect(async () => {
		setStages((await api('/stages')).stages);
	}, []);

	return <ul class='StagesListView'>
		{ stages.map(stage => <li>
			<a href={'/?stage=' + encodeURIComponent(stage.name)}>{ stage.name }</a>
		</li>) }
	</ul>
}
