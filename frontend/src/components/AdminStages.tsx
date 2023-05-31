import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';
import { useCurrentStatus } from '../states.ts';
import StageList from './StageList.tsx';
import StageEditor from './StageEditor.tsx';
import './AdminStages.css';

export default function AdminStages({}) {
	const [selectedStage, setSelectedStage] = useState(null);
	const currentStatus = useCurrentStatus();

	useEffect(async () => { // initialization
		if(currentStatus.stage && !selectedStage)
			setSelectedStage(currentStatus.stage);
	}, [currentStatus]);

	return <div class='AdminStages'>
		<h2>Stages</h2>
		<StageList
			setSelectedStage={setSelectedStage}
			selectedStage={selectedStage}
			currentStage={currentStatus.stage}
			admin={true} />

		{ selectedStage && <StageEditor
			stageName={selectedStage}
			currentStatus={currentStatus} /> }
	</div>;
}
