window['voyc']['vote']['candidates'] = [
	{id:1, rank:0, rand:5, name:'Donald Trump', party:'Republican'},
	{id:2, rank:0, rand:3, name:'Hillary Clinton', party:'Democrat'},
	{id:3, rank:0, rand:1, name:'Bernie Sanders', party:'Independent'},
	{id:4, rank:0, rand:4, name:'Gary Johnson', party:'Libertarian'},
	{id:5, rank:0, rand:2, name:'Jill Stein', party:'Green'},
];
window['voyc']['vote']['results'] = {
	numVoters: 24,
	avgRanked: 3,
	winner:'Bernie Sanders',
	winningRound:4,
	rounds: [
		{
			round: [
				{name: 'Donald Trump', count: 100, pct: 35 },
				{name: 'Hillary Clinton', count: 100, pct: 25 },
				{name: 'Bernie Sanders', count: 100, pct: 20 },
				{name: 'Gary Johnson', count: 100, pct: 11 },
				{name: 'Jill Stein', count: 100, pct: 9 },
			]
		},
		{
			round: [
				{name: 'Donald Trump', count: 100, pct: 35 },
				{name: 'Hillary Clinton', count: 100, pct: 28 },
				{name: 'Bernie Sanders', count: 100, pct: 26 },
				{name: 'Gary Johnson', count: 100, pct: 11 },
			]
		},
		{
			round: [
				{name: 'Donald Trump', count: 100, pct: 35 },
				{name: 'Bernie Sanders', count: 100, pct: 33 },
				{name: 'Hillary Clinton', count: 100, pct: 32 },
			]
		},
		{
			round: [
				{name: 'Bernie Sanders', count: 100, pct: 65 },
				{name: 'Donald Trump', count: 100, pct: 35 },
			]
		}
	]
};
//window['voyc']['vote']['onResultsReady']();
window['voyc']['vote']['onElectionReady']();
