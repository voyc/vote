/** @const **/
svctest = {};
$ = function(eid) { return document.getElementById(eid); }
window.addEventListener('load', function(evt) {
	attachDomEventHandlers();
	svctest.comm = new Comm('http://vote.voyc.com/svc/', '', 0, true); 
}, false);

attachDomEventHandlers = function() {
	// attach click handler to submit button
	$('callserver').addEventListener('click', function(event) {
		var svcname = 'castballot';
		var postdata = {};
		postdata['e'] = 1;
		
		// display postdata
		$('postdata').innerHTML = dumpObject(postdata);
		$('response').innerHTML = '';
		
		// call the service
		// one indicator for svc in progress/success/fail
		// in the div with the submit button
		$('ok').innerHTML = 'waiting...';
		
		svctest.comm.request(svcname, postdata, function(ok,response,xhr) {
			$('ok').innerHTML = (ok) ? 'ok: true' : 'ok: false';
			
			// save and display the response
			svctest.response = response;
			$('response').innerHTML = dumpObject(response);
		});
	});
}
