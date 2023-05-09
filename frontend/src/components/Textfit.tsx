import { useRef, useEffect, useState } from 'preact/hooks';

export default function Textfit({ children }) {
	const [style, setStyle] = useState({
		'font-size': '100%',
		'--step': 1
	});

	const ref = useRef(null);
	useEffect(() => {
		const throttle = 1000 / 10;
		let last = Date.now();
		window.addEventListener('resize', () => {
			if(Date.now() - last < throttle) return;
			last = Date.now();

			setStyle({
				'font-size': '100%',
				'--step': 1
			});
		});
		setStyle({
			'font-size': '100%',
			'--step': 1
		});
	}, []);


	useEffect(() => {
		const span = ref.current;
		if(span.offsetHeight > span.parentElement.offsetHeight) {
			const step = style['--step'] + 1;
			if(step > 4) {
				console.error('What...');
				return;
			}
			setStyle({
				'font-size': ((100/step)|0) + '%',
				//'line-height': 1/step,
				'--step': step
			});
		}
	}, [style]);

	return <span ref={ref} style={{...style, 'display': 'inline-block'}}>{children}</span>;
}

/*
Panzerkampfwagen
Nea mulan
Masakrator
S.P.A.R.T.
Malcom Sylas Edjouma Laouari
Shigoku Khazzan
Shobolinsky
Tzanca Hurricane
*/
