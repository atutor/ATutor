<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

function print_result($q_text, $q_answer, $q_num, $a_num, $correct) {
	global $mark_right, $mark_wrong;

	if ($a_num == '') {
		$a_num = -1;
	}
	if ($a_num == $q_num) {
		echo '<input type="radio" checked="checked" />';

		if ($correct) {
			echo $mark_right;
		} else {
			echo $mark_wrong;
		}
		echo $q_text;

	} else {
		echo '<input type="radio" disabled="disabled" />';
	
		if ($q_answer == 1) {
			echo $mark_wrong;
		}

		echo $q_text;
	}

}

function print_score($correct, $weight, $qid, $score, $put_zero = true) {
	echo '<input type="text" class="formfieldR" size="2" name="scores['.$qid.']" value="';

	if ($score != '') {
		echo $score;
	/*} else if ($correct == $score){
		echo $weight;
	} else if ($correct!=$score){
		echo '0';*/
	} else if ($correct) {
		echo $weight;
	} else if ($put_zero) {
		echo '0';
	}
	echo '" style="width: 25px; font-weight: bold;" maxlength="4" /><b>/'.$weight.'</b>';
}

?>