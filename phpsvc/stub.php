<?php
/*
	svc stub
	An empty client service, using authenticated, logged-in user.
*/
function stub() {
	$a = array(
		'status' => 'system-error'
	);

	// raw inputs
	$taint_si = isset($_POST['si']) ? $_POST['si'] : 0;
	$taint_a = isset($_POST['a']) ? $_POST['a'] : 0;
	$taint_b = isset($_POST['b']) ? $_POST['b'] : 0;

	// validate inputs
	$si = validateToken($taint_si);
	$a = validateUname($taint_a);
	$b = validateUname($taint_b);
Log::write(LOG_WARNING, "$a,$b,$si";

	// validate parameter set
	if (!$si || !$a || !$b){
		Log::write(LOG_WARNING, 'attempt with invalid parameter set');
		return $a;
	}

	// get database connection
	$conn = getConnection();
	if (!$conn) {
		return $a;
	}

	// get logged-in user
	$result = getUserByToken($conn, $si);
	if (!$result) {
		return $a;
	}

	// get data fields
	$row = pg_fetch_array($result, 0, PGSQL_ASSOC);
	$username = $row['username'];
	$access = $row['access'];
	$auth = $row['auth'];
	$email = $row['email'];

	// verify user authentication state
	if (!isUserVerified($auth)) {
		Log::write(LOG_NOTICE, "attempt by non-verified user");
		return $a;
	}

	// compose output message
	$message = "a is $a and b is $b and email is " . obscureEmail($email);

	// success
	$a['status'] = 'ok';
	$a['message'] = $message;
	return $a;
}
?>
