/**
	class Nav
	@constructor
	singleton
	expects members keyword, onPageLoad
*/
voyc.Nav = function() {
	// is singleton
	if (voyc.Nav._instance) return voyc.Nav._instance;
	else voyc.Nav._instance = this;
	
	this.keyword = 'page';
	this.onPageLoad = function(page) {};
}
voyc.Nav.prototype.jump = function(page) {
	if (!(window.location.protocol.indexOf('file') > -1)) {
		window.history.pushState({page:page}, null, '?' + this.keyword + '='+page);
	}
	this.onPageLoad(page);
}
voyc.Nav.prototype.replace = function(page) {
	window.history.replaceState({page:page}, null, '?' + this.keyword + '='+page);
	this.onPageLoad(page);
}
voyc.Nav.prototype.startup = function() {
	var qstring = window.location.search;
	var page = '';
	if (qstring.length > 0) {
		var pos = qstring.indexOf(this.keyword + '=');
		if (pos > -1) {
			pos += this.keyword.length + 1;
			page = qstring.substring(pos);
		}
	}
	this.onPageLoad(page);
}

window.onpopstate = function(event) {
	var page = (event.state) ? event.state['page'] : '';
	(new voyc.Nav()).onPageLoad(page);
}
