import { useState, useEffect } from 'preact/hooks';
import api from '../api.ts';
import './AdminTeams.css';

export default function AdminTeams({}) {
	const [teamPageUrl, setPageUrl] = useState('/teams?offset=0');
	const [teamPage, setTeamPage] = useState([]);
	const [links, setLinks] = useState({
		first: null,
		next: null,
		prev: null,
		last: null
	});

	async function reloadTeams() {
		const page = await api(teamPageUrl);

		setTeamPage(page.items);
		setLinks({
			first: page.first,
			prev: page.prev,
			next: page.next,
			last: page.last
		});
	}

	useEffect(async () => {
		reloadTeams();
	}, [teamPageUrl]);

	async function createTeam(e) {
		e.preventDefault(); // don't submit :)

		const name = e.srcElement.elements.name.value;
		const username = name.toLowerCase().replace(/[^a-z0-9]+/g, '-');

		await api('/teams', 'POST', {
			username: username,
			name: name
		});

		e.srcElement.reset(); // clear form
		reloadTeams();
	}

	const offset = teamPageUrl.match(/offset=(\d+)/)[1] | 0;

	return <>
		<h2 id='teams'>Teams</h2>

		<table>
			<thead>
				<tr>
					<th>#</th>
					<th class='wide'>Name</th>
					<th>(copy)</th>
				</tr>
			</thead>
			<tbody>
			{
				teamPage.map((team, i) => <tr>
					<td>{i + 1 + offset}</td><td class='wide'>{team.name}</td><td><button onClick={() => {
						navigator.clipboard.writeText(team.username);
						document.querySelectorAll('[data-paste="team.username"]').forEach(el => el.value = team.username);
					}}>(copy)</button></td>
				</tr>)
			}
			</tbody>
		</table>
		<a href='#teams' onClick={() => links.first && setPageUrl(links.first)}>(first)</a>&nbsp;
		<a href='#teams' onClick={() => links.prev && setPageUrl(links.prev)}>(prev)</a>&nbsp;
		<a href='#teams' onClick={() => links.next && setPageUrl(links.next)}>(next)</a>&nbsp;
		<a href='#teams' onClick={() => links.last && setPageUrl(links.last)}>(last)</a>
		<form onSubmit={createTeam}>
			<input type="text" name="name" placeholder="New team name"/>
			<button>Create team</button>
		</form>
	</>;
}
