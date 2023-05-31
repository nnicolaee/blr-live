export default async function api(route, method='GET', body=null) {
	const url = '/api' + (route[0] == '/' ? '' : '/') + route;
	if(method === 'GET') {
		const res = await fetch(url);
		const body = await res.json();

		if(res.status < 200 || res.status > 299)
			throw new Error(body.error);
		
		return body;
	} else {
		const res = await fetch(url, {
			method,
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(body)
		});

		if(res.status < 200 || res.status > 299) {
			console.error(res);
			throw new Error(body.error);
		}

		try {
			const resbody = await res.json();
			return resbody;
		} catch { // empty response
			return null;
		}
	}
}
