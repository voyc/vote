<?php
function changeusername() {
	$a = array(
	    'status' => 'system-error'
	);

	// raw inputs
	$taint_si = isset($_POST['si']) ? $_POST['si'] : 0;
	$taint_pword = isset($_POST['pword']) ? $_POST['pword'] : 0;
	$taint_uname = isset($_POST['uname']) ? $_POST['uname'] : 0;

	// validate inputs
	$si = validateToken($taint_si);
	$pword = validatePword($taint_pword);
	$uname = validateUname($taint_uname);

	// validate parameter set
	if (!$si || !$pword || !$uname) {
		Log::write(LOG_WARNING, "attempt with invalid parameter set.");
		return $a;
	}

	// get db connection
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
	$id = $row['id'];
	$dbpw = $row['hashpassword'];
	$auth = $row['auth'];
	$email = $row['email'];

	// verify user authentication state
	if (!isUserVerified($auth)) {
		Log::write(LOG_NOTICE, "attempt by non-verified user");
		return $a;
	}

	// verify password
	$boo = verifyPassword($pword, $dbpw);
	if (!$boo) {
		Log::write(LOG_NOTICE, "attempt with bad password");
		return $a;
	}

	// validate username is unique
	$name = 'test-unique-username';
	$sql  = "select id from accounts.user where username = $1 and id <> $2";
	$params = array($uname, $id);
	$result = execSql($conn, $name, $sql, $params, false);
	if (!$result) {
		return $a;
	}
	$numrows = pg_num_rows($result);
	if ($numrows > 0) {
		Log::write(LOG_NOTICE, "$name: username already on file");
		$a['status'] = 'username-in-use';
		return $a;
	}

	// update the user record
	$name = 'change-user-username';
	$sql  = "update accounts.user set username = $1 where id = $2";
	$params = array($uname, $id);
	$result = execSql($conn, $name, $sql, $params, true);
	if (!$result) {
		return $a;
	}

	// send security notice
	$boo = sendAuthenticationEmail($email, 'changedusername', '');
	if (!$boo) {
		$a['status'] = 'send-email-failed';
		return $a;
	}

	// success
	$a['status'] = 'ok';
	$a['uname'] = $uname;
	return $a;
}
?>
