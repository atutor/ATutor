<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

function quote_csv($line) {
	$line = str_replace('"', '""', $line);
	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}

$tid = intval($_GET['tid']);

//get test info
$sql	= "SELECT title, randomize_order FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))){
	require (AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$test_title = str_replace(array('"', '<', '>'), '', $row['title']);
$test_title = str_replace (' ', '_', $test_title);
$random = $row['randomize_order'];

//get test questions
$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
$result	= mysql_query($sql, $db);
$num_questions = mysql_num_rows($result);
$questions = array();
$total_weight = 0;
$i=0;
while ($row = mysql_fetch_array($result)) {
	$row['score']	= 0;
	$questions[$i]	= $row;
	$questions[$i]['count']	= 0;
	$q_sql .= $row['question_id'].',';
	$total_weight += $row['weight'];
	$i++;
}
$q_sql = substr($q_sql, 0, -1);


header('Content-Type: application/x-excel');
header('Content-Disposition: inline; filename="'.$test_title.'.csv"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

$nl = "\n";

echo quote_csv(_AT('login_name')).', ';
echo quote_csv(_AT('date_taken')).', ';
echo quote_csv(_AT('mark'));
for($i = 0; $i< $num_questions; $i++) {
	echo ', '.quote_csv('Q'.($i+1).'/'.$questions[$i]['weight']);
}
echo $nl;

$guest_text = '- '._AT('guest').' -';

//get test results
$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R LEFT JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE R.status=1 AND R.test_id=$tid AND R.final_score<>'' ORDER BY M.login, R.date_taken";
$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);





if ($row = mysql_fetch_array($result)) {
	$sql2	= "SELECT anonymous FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result2	= mysql_query($sql2, $db);
	while($row2 =mysql_fetch_array($result2)){
			$anonymous = $row2['anonymous'];
	}

	do {
		$row['login']     = $row['login']     ? $row['login']     : $guest_text;
		if($anonymous ==1){
				echo quote_csv(_AT('anonymous')).', ';
		}else{
				echo quote_csv($row['login']).', ';
		}
		echo quote_csv($row['date_taken']).', ';

		if ($random) {
			$total_weight = get_random_outof($row['test_id'], $row['result_id']);
		}
		echo $row['final_score'].'/'.$total_weight;

		$answers = array(); /* need this, because we dont know which order they were selected in */
		$sql = "SELECT question_id, score FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row[result_id] AND question_id IN ($q_sql)";
		$result2 = mysql_query($sql, $db);
		while ($row2 = mysql_fetch_array($result2)) {
			$answers[$row2['question_id']] = $row2['score'];
		}
		for($i = 0; $i < $num_questions; $i++) {
			$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
			if ($answers[$questions[$i]['question_id']] == '') {
				echo ', -';
			} else {
				echo ', '.$answers[$questions[$i]['question_id']];
				if ($random) {
					$questions[$i]['count']++;
				}
			}			
		}

		echo $nl;
	} while ($row = mysql_fetch_array($result));

	echo $nl;
}

?>
