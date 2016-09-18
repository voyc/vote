<?php
/*
	validation functions.
*/

// integer is numeric.  Length 1-12.
function validateInteger($taint) {
	$clean = false;
 	$ok = preg_match('/^[0-9]{1,12}$/', $taint);
	if ($ok) {
		$clean = $taint;
	}
	return $clean;
}

// array of integers is array of numerics, each length 1-12.  max length of array: 10
function validateArrayOfIntegers($taint) {
	$clean = false;
	$clean_a = array();
	$limit = 10;
	if (is_string($taint)) {
		$ok = preg_match('/^[0-9,]{1,512}$/', $taint);
		if ($ok) {
			$taint_a = explode(',', $taint, $limit);
			$num = count($taint_a);
			if ($num > 0 && $num < $limit) {
				for ($i=0; $i<$num; $i++) {
					$taint_t = $taint_a[$i];
					$ok = preg_match('/^[0-9]{1,12}$/', $taint_t);
					if ($ok) {
						$clean_a[] = intval($taint_t);
					}
				}
				if (count($clean_a) == $num) {
					$clean = $clean_a;
				}
			}
		}
	}
	return $clean;
}
?>
