<?php
function castballot() {
	$a = array(
	    'status' => 'system-error'
	);

	// raw inputs
	$taint_e = isset($_POST['e']) ? $_POST['e'] : 0;

	// validated inputs
	$e = validateInteger($taint_e);

	// required parameters
	if (!$e) {
		Log::write(LOG_WARNING, 'attempt with invalid parameter set');
		return $a;
	}

	// connect to db
	$conn = getConnection();
	if (!$conn) {
		return $a;
	}

	// query candidates table
	$name = 'get_election';
	$sql = "select id, name, party";
	$sql .= " from vote.candidate";
	$sql .= " where eid = $1";
	$params = array($e);
	$result = execSql($conn, $name, $sql, $params, false);
	if (!$result) {
		return $a;
	}

	// check result count
	if ($numrows < 1) {
		Log::write(LOG_NOTICE, "$name candidates not found");
		$a['status'] = 'castballot-failed';
		return $a;
	}

	// build array for random ballot order assignment
	$ballotorder = array();
	for ($i=0; $i<$numrows; $i++) {
		$ballotorder.push($1+1);
	}
	shuffle($ballotorder);
	
	// build array of candidates
	$candidates = array();
	$numrows = pg_num_rows($result);
	for ($i=0; $i<$numrows; $i++) {
		$row = pg_fetch_array($result, $i, PGSQL_ASSOC);
		$cand = array();
		$cand['id'] = $row['id'];
		$cand['name'] = $row['name'];
		$cand['party'] = $row['party'];
		$cand['rand'] = $ballotorder[$i];
		$cand['rank'] = 0;
		array_push($candidates, $cand);
	}

	// success
	$a['status'] = 'ok';
	$a['candidates'] = $candidates;
	return $a;
}
?>
