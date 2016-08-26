<?php
/*
	validation functions.
*/

// integer is numeric.  Length 3-12.
function validateInteger($taint) {
	$clean = false;
 	$ok = preg_match('/^[0-9]{3,12}$/', $taint);
	if ($ok) {
		$clean = $taint;
	}
	return $clean;
}
?>
