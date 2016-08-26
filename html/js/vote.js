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
}

Vote.ordinals = ['first', 'second', 'third', 'fourth', 'fifth'];
Vote.rankline = '<div rankline id=%id%>%rank% %name%, %party%</div>';
Vote.unrankline = '<div unrankline id=%id%>%name%, %party%</div>';

Vote.prototype.load = function() {
	var that = this;
	document.getElementById('vote').addEventListener('click', function() {
		that.drawBallot();
		that.showSection('ballot');
	}, false);
	document.getElementById('cast').addEventListener('click', function() {
		that.drawResults();
		that.showSection('results');
	}, false);

	appendScript('results.js');
}

Vote.prototype.onResultsReady = function() {
}
Vote.prototype.onElectionReady = function() {
	this.showSection('vote');
}

Vote.prototype.drawResults = function() {
	var i = 1;
	var s = '';
	var results = window['voyc']['vote']['results'];
	var rounds = results.rounds;
	var row = '<tr><td>%name%</td><td>%count%</td><td>%pct%</td><td>%note%</td></tr>';
	for (var n=0; n<rounds.length; n++) {
		s += '<h3>Round ' + (i++) + '</h3>';
		s += '<table>';
		for (var m=0; m<rounds[n].round.length; m++) {
			var set = rounds[n].round[m];
			var note = '';
			if (m == 0) {
				if (set.pct < 50) {
					note = 'No winner.  Less than 50%.';
				}
				else {
					note = 'Winner';
				}
			}
			else if (m == rounds[n].round.length-1 && m > 1) {
				note = 'Eliminated';
			}
			var td = row;
			td = td.replace('%name%', set.name);
			td = td.replace('%count%', set.count);
			td = td.replace('%pct%', set.pct);
			td = td.replace('%note%', note);
			s += td;
		}
		s += '</table>';
	}
	document.getElementById('res').innerHTML = s;
}

Vote.prototype.drawBallot = function() {
	if (this.maxRank < 5) {
		document.getElementById('ordinal').innerHTML = Vote.ordinals[this.maxRank];
		document.getElementById('choice').style.display = 'block';
	}
	else {
		document.getElementById('choice').style.display = 'none';
	}
	
	var candidates = window['voyc']['vote']['candidates'];
	candidates.sort(function(a, b) {
		return a.rank - b.rank;
	});
	var s = '';
	var t = '';
	var cand = {};
	for (var c=0; c<candidates.length; c++) {
		cand = candidates[c];
		if (cand['rank']) {
			t = Vote.rankline;
			t = t.replace('%id%', cand['id']);
			t = t.replace('%rank%', cand['rank']);
			t = t.replace('%name%', cand['name']);
			t = t.replace('%party%', cand['party']);
			s += t;
		}
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

appendScript = function(file) {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = file;
	document.getElementsByTagName("head")[0].appendChild(script);
}

window['voyc']['vote']['onResultsReady'] = function() {
	vote.onResultsReady();
}
