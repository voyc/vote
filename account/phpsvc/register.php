<?php
/*
	svc register
	Register a new user.
*/
function register() {
	$a = array(
	    'status' => 'system-error'
	);

	// raw inputs
	$taint_uname = isset($_POST['uname']) ? $_POST['uname'] : 0;
	$taint_email = isset($_POST['email']) ? $_POST['email'] : 0;
	$taint_pword = isset($_POST['pword']) ? $_POST['pword'] : 0;

	// validate inputs
	$uname = validateUname($taint_uname);
	$email = validateEmail($taint_email);
	$pword = validatePword($taint_pword);

	if (!$email || !$uname || !$pword) {
		Log::write(LOG_WARNING, "attempt with invalid parameter set");
		return $a;
	}

	$conn = getConnection();
	if (!$conn) {
		return $a;
	}

	// validate username is unique
	$name = 'test-unique-username';
	$sql  = "select id from accounts.user where username = $1";
	$params = array($uname);
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

	// validate email is unique
	$name = 'test-unique-email';
	$sql  = "select id from accounts.user where email = $1";
	$params = array($email);
	$result = execSql($conn, $name, $sql, $params, false);
	if (!$result) {
		return $a;
	}

	$numrows = pg_num_rows($result);
	if ($numrows > 0) {
		Log::write(LOG_NOTICE, "$name: email already on file");
		$a['status'] = 'email-in-use';
		return $a;
	}

	// get the next user id
	$name = 'get-next-user-id';
	$sql = "select nextval('accounts.user_id_seq')";
	$params = array();
	$result = execSql($conn, $name, $sql, $params, true);
	if (!$result) {
		return $a;
	}

	$row = pg_fetch_array($result, 0, PGSQL_ASSOC);
	$id = $row['nextval'];

	// hash the password
	$hashPassword = hashPassword($pword);

	$auth = DB::$auth_registered;
	$access = DB::$auth_novice;

	// get TIC
	$publicTic = generateTic();
	$hashTic = hashTic($publicTic);

	// write a session token
	$si = writeToken($conn, $id);
	if (!$si){
		return $a;
	}

	// write the user record
	$name = 'insert-user';
	$sql  = "insert into accounts.user (id, username, email, hashpassword, auth, hashtic, tmhashtic) values ($1, $2, $3, $4, $5, $6, now())";
	$params = array($id, $uname, $email, $hashPassword, $auth, $hashTic);
	$result = execSql($conn, $name, $sql, $params, true);
	if (!$result) {
		return $a;
	}

	// send TIC to user by email
	$boo = sendAuthenticationEmail($email, 'verify', $publicTic);
	if (!$boo) {
		$a['status'] = 'send-email-failed';
		return $a;
	}

	// success
	$a['status'] = 'ok';
	$a['si'] = $si;
	$a['auth'] = $auth;
	$a['access'] = $access;
	$a['uname'] = $uname;
	//$a['email'] = obscureEmail($email);
	return $a;
}
?>
