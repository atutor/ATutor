<?php

if ($_GET['f']) {
	$f = intval($_GET['f']);
	if ($f > 0) {
		print_feedback($f);
	} else {
		/* it's probably an array */
		$f = unserialize(urldecode(stripslashes($_GET['f'])));
		print_feedback($f);
	}
} else if ($feedback) {
	print_feedback($feedback);
}

if (isset($_info)) {
	print_infos($_info);
}

if (isset($warnings)) {
	print_warnings($warnings);
}


if (isset($errors)) {
	print_errors($errors);
}

?>