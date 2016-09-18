<?php
function castballot() {
	$a = array(
	    'status' => 'system-error'
	);

	// raw inputs
	$taint_e = isset($_POST['e']) ? $_POST['e'] : 0;
	$taint_r = isset($_POST['r']) ? $_POST['r'] : 0;

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

	// cast ballot

	if ($taint_r) {
		$r = validateArrayOfIntegers($taint_r);
		$e = 1;
		$u = 1;

		$votes = '';
		foreach ($r as $rank => $id) {
			$s = str_pad(strval($id), 7, '0', STR_PAD_LEFT);
			$votes .= $s;
		}
		$name = 'insert_votes';
		$sql = 'insert into vote.vote (userid, electionid, votes) values ($1, $2, $3)';
		$params = array($e, $u, $votes);
		$result = execSql($conn, $name, $sql, $params, false);
		if (!$result) {
			return $a;
		}
	}

	// return candidates

	// query candidate table
	$name = 'candidate_list';
	$sql = "select id, name, party";
	$sql .= " from vote.candidate";
	$sql .= " where electionid = $1";
	$params = array($e);
	$result = execSql($conn, $name, $sql, $params, false);
	if (!$result) {
		return $a;
	}

	// check result count
	$numrows = pg_num_rows($result);
	if ($numrows < 1) {
		Log::write(LOG_NOTICE, "$name candidates not found");
		$a['status'] = 'castballot-failed';
		return $a;
	}

	// build array for random ballot order assignment
	$ballotorder = array();
	for ($i=0; $i<$numrows; $i++) {
		$ballotorder[] = $i + 1;
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
		$cand['elim'] = 0;
		$candidates[] = $cand;
	}

	// count votes and return results
	
	// query vote table
	$name = 'read_votes';
	$sql = "select votes";
	$sql .= " from vote.vote";
	$sql .= " where electionid = $1";
	$params = array($e);
	$result = execSql($conn, $name, $sql, $params, false);
	if (!$result) {
		return $a;
	}

	// check result count
	$numrows = pg_num_rows($result);
	if ($numrows < 1) {
		Log::write(LOG_NOTICE, "$name votes not found");
		$a['status'] = 'castballot-failed';
		return $a;
	}

	// build array of votes
	$votes = array();
	$numrows = pg_num_rows($result);
	for ($i=0; $i<$numrows; $i++) {
		$row = pg_fetch_array($result, $i, PGSQL_ASSOC);
		$vote = array();
		$vote[] = intval(substr($row['votes'], 0, 7));
		$vote[] = intval(substr($row['votes'], 7, 7));
		$vote[] = intval(substr($row['votes'], 14, 7));
		$vote[] = intval(substr($row['votes'], 21, 7));
		$vote[] = intval(substr($row['votes'], 28, 7));
		$vote[] = intval(substr($row['votes'], 35, 7));
		$votes[] = $vote;
	}
	
	// count votes in rounds
	$rounds = array();

	$numRound = 1;
	$finalRound = 0;
	$winner = '';
	while (!$finalRound && ($numRound < count($candidates))) {
		
		$total = 0;
	
		// initialize the array of nominees in this round
		$nominees = array();
		foreach ($candidates as $cand) {
			if (!$cand['elim']) {
				$nominees[] = array('id'=>$cand['id'], 'name'=>$cand['name'], 'count'=>0, 'pct'=>0, 'note'=>'');
			}
		}
		
		// accumulate the votes for each nominee
		foreach ($votes as $vote) {
			$id = $vote[0];
			$ndx = getNdxById($nominees, $id);
			if ($ndx !== false) {
				$nominees[$ndx]['count'] += 1;
				$total += 1;
			}
		}
		
		// calculate percentages, find winner, find loser
		usort( $nominees, "sortByAscendingCount");
		$i = 1;
		foreach ($nominees as $key => $value) {
			$nominees[$key]['pct'] = round(($value['count'] / $total) * 100, 1);
			if ($i == 1) {
				if ($nominees[$key]['pct'] >= 50) {
					$nominees[$key]['note'] = 'Winner';
					$finalRound = $numRound;
					$winner = $nominees[$key]['name'];
				}
			}
			else if ($i == count($nominees) && !$finalRound) { // todo: handle tie for last place
				$nominees[$key]['note'] = 'Eliminated';
				$id = $nominees[$key]['id'];
				eliminate($id, $numRound, $candidates, $votes);
			}
			$i += 1;
		}

		// store the round
		$rounds[] = array('total'=>$total, 'nominees'=>array_values($nominees));
		$numRound += 1;
	}
	$results = array('rounds' => $rounds, 'winner' => $winner, 'finalround' => $finalRound);
	
	// success
	$a['status'] = 'ok';
	$a['candidates'] = array_values($candidates);
	//$a['votes'] = array_values($votes);
	$a['results'] = $results;
	return $a;
}

//-----------------------------------

function sortByAscendingCount($a,$b) {
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] > $b['count']) ? -1 : 1;	
}
function getNdxById($a, $id) {
	$r = false;
	foreach ($a as $key => $value) {
		if ($value['id'] == $id) {
			$r = $key;
		}
	}
	return $r;
}

// eliminate candidate from round
function eliminate($id, $numRound, &$candidates, &$votes) {
	// mark candidate in candidates array as eliminated
	$ndx = getNdxById($candidates, $id);
	$candidates[$ndx]['elim'] = $numRound;

	// remove votes for this candidate
	foreach ($votes as $key => $value) {
		foreach ($value as $k => $v) {
			if ($v == $id) {
				array_splice($votes[$key], $k, 1);
				$votes[$key][] = 0;
			}
		}
	}
}
?>
