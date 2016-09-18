/**
	@constructor
	class Vote
	A singleton object
*/
Vote = function() {
	// is singleton
	if ( arguments.callee._singletonInstance )
		return arguments.callee._singletonInstance;
	arguments.callee._singletonInstance = this;
	
	this.maxRank = 0;
	this.comm = null;
}

Vote.ordinals = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth'];
Vote.rankline = '<div rankline id=%id%> <span class=rankrank>%rank%</span> %name%, %party%</div>';
Vote.unrankline = '<div unrankline id=%id%>%name%, %party%</div>';

Vote.prototype.load = function() {
	this.comm = new Comm('http://vote.voyc.com/svc/', '', 0, true); 
	var id = 1;
	this.getElection(id);

	var that = this;
	document.getElementById('cast').addEventListener('click', function() {
		// loop thru candidates, build array
		var arank = [];
		var cand = {};
		for (var r=1; r<=that.maxRank; r++) {
			cand = that.getCandidateByRank(r);
			arank.push(cand.id);
		}
		var svcname = 'castballot';
		var postdata = {};
		postdata['e'] = 1;
		postdata['r'] = arank;
		that.comm.request(svcname, postdata, function(ok,response,xhr) {
			if (ok) {
				if (response['results']) {
					window['voyc']['vote']['results'] = response['results'];
				}
				that.drawElection();
				that.drawBallot();
				that.drawResults();
				that.showSection('results');
			}
		});
	}, false);
}

Vote.prototype.getElection = function(id) {
	var svcname = 'castballot';
	var postdata = {};
	postdata['e'] = 1;
	var that = this;
//	document.querySelector('#loader [name=spinner]').classList.add('spin');
	this.comm.request(svcname, postdata, function(ok,response,xhr) {
//		document.querySelector('#loader [name=spinner]').classList.remove('spin');
		if (ok) {
			if (response['candidates']) {
				window['voyc']['vote']['candidates'] = response['candidates'];
			}
			if (response['results']) {
				window['voyc']['vote']['results'] = response['results'];
			}
			that.drawElection();
			that.drawBallot();
			that.drawResults();
			that.showSection('ballot');
		}
	});
}

Vote.prototype.drawResults = function() {
	var i = 1;
	var s = '';
	var results = window['voyc']['vote']['results'];
	
	var winner = 'Winner: %name% in Round %finalround%';
	var t = winner;
	t = t.replace('%name%', results.winner);
	t = t.replace('%finalround%', results.finalround);
	document.getElementById('win').innerHTML = t;
	
	var rounds = results.rounds;
	var row = '<tr><td>%name%</td><td>%count%</td><td>(%pct%%)</td><td>%note%</td></tr>';
	s += '<table>';
	//var rowheader = '<tr><th>Candidate</th><th>Count</th><th>Percent</th><th></th></tr>';
	for (var n=0; n<rounds.length; n++) {
		s += '<tr><td colspan=4><h3>Round ' + (i++) + '</h3></td></tr>';
		//s += rowheader;
		for (var m=0; m<rounds[n].nominees.length; m++) {
			var set = rounds[n].nominees[m];

			var note = '';
			if (n == rounds.length-1) {
				if (m == 0) {
					note = '<b>Winner</b>';
				}
			}
			else {
				if (m == rounds[n].nominees.length-1) {
					note = 'Eliminated';
				}
			}
			var td = row;
			td = td.replace('%name%', set.name);
			td = td.replace('%count%', set.count);
			td = td.replace('%pct%', set.pct);
			td = td.replace('%note%', note);
			s += td;
		}
	}
	s += '</table>';
	document.getElementById('res').innerHTML = s;
}

Vote.prototype.drawElection = function() {
	/*
	<h2>Using Instant-Runoff Voting <a class='help' href='https://en.wikipedia.org/wiki/Instant-runoff_voting'>What is that?</a></h2>
	<h1>Election: </h1>
	*/
	document.getElementById('electionname').innerHTML = '2016 USA President'; // window['voyc']['vote']['election']['name'];
}

Vote.prototype.drawBallot = function() {
	var candidates = window['voyc']['vote']['candidates'];
	if (this.maxRank < candidates.length) {
		document.getElementById('ordinal').innerHTML = Vote.ordinals[this.maxRank];
		document.getElementById('choice').style.display = 'block';
	}
	else {
		document.getElementById('choice').style.display = 'none';
	}
	
	candidates.sort(function(a, b) {
		return a.rank - b.rank;
	});
	var s = '';
	var t = '';
	var cand = {};
	var nextrank = 1;
	for (var c=0; c<candidates.length; c++) {
		cand = candidates[c];
		if (cand['rank']) {
			t = Vote.rankline;
			t = t.replace('%id%', cand['id']);
			t = t.replace('%rank%', cand['rank']);
			t = t.replace('%name%', cand['name']);
			t = t.replace('%party%', cand['party']);
			s += t;
			nextrank = cand['rank'] + 1;
		}
	}
	if (this.maxRank < candidates.length) {
		t = '<div rankline id=0> <span class=rankrank>' + (this.maxRank + 1) + '</span> ...</div>';
		s += t;
	}
	document.getElementById('ranked').innerHTML = s;

	candidates.sort(function(a, b) {
		return a.rand - b.rand;
	});
	s = '';
	for (var c=0; c<candidates.length; c++) {
		cand = candidates[c];
		if (!cand['rank']) {
			t = Vote.unrankline;
			t = t.replace('%id%', cand['id']);
			t = t.replace('%name%', cand['name']);
			t = t.replace('%party%', cand['party']);
			s += t;
		}
	}
	document.getElementById('unranked').innerHTML = s;

	var a = document.querySelectorAll('[unrankline]');
	var that = this;
	for (var i=0; i<a.length; i++) {
		a[i].addEventListener('click', function(evt) {
			that.setRank(evt);
		}, false);
	}
	
	if (this.maxRank > 0) {
		document.getElementById('cast').disabled = false; 
	}
}

Vote.prototype.getCandidateById = function(id) {
	var candidates = window['voyc']['vote']['candidates'];
	var cand = {};
	var r = {};
	for (var c=0; c<candidates.length; c++) {
		cand = candidates[c];
		if (cand['id'] == id) {
			r = cand;
		}
	}
	return r;
}

Vote.prototype.getCandidateByRank = function(rank) {
	var candidates = window['voyc']['vote']['candidates'];
	var cand = {};
	var r = {};
	for (var c=0; c<candidates.length; c++) {
		cand = candidates[c];
		if (cand['rank'] == rank) {
			r = cand;
		}
	}
	return r;
}

Vote.prototype.setRank = function(evt) {
	var id = evt.currentTarget.id;
	var cand = this.getCandidateById(id);
	this.maxRank++;
	cand['rank'] = this.maxRank;
	this.drawBallot();
}

Vote.prototype.showSection = function(id) {
	var a = document.querySelectorAll('section');
	for (var i=0; i<a.length; i++) {
		a[i].style.display = (a[i].id == id) ? 'block' : 'none';
	}
}
