<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

// Validate date
function isValidDate($date)
{
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) 
    {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    }

    return false;
}

function display_test_info($row)
{
		global $random, $num_questions, $total_weight, $questions, $total_score, $table_content, $csv_content;
		global $passscore, $passpercent;
		global $q_sql, $db;

		$row['login'] = $row['login'] ? $row['login'] : '- '._AT('guest').' -';
		$table_content .= '<tr>';
			
		if($anonymous == 1){
				$table_content .= '<td align="center">'._AT('anonymous').'</td>';
				$csv_content .= quote_csv(_AT('anonymous')).', ';
		}else{
				$table_content .= '<td align="center">'.$row['login'].'</td>';
				$csv_content .= quote_csv($row['login']).', ';
		}
		$startend_date_format=_AT('startend_date_format');
		$table_content .= '<td align="center">'.AT_date($startend_date_format, $row['date_taken'], AT_DATE_MYSQL_DATETIME).'</td>';
		$csv_content .= quote_csv($row['date_taken']).', ';

		if ($passscore <> 0)
		{
			$table_content .= '<td align="center">'.$passscore.'</td>';
			$csv_content .= $passscore.', ';
		}
		elseif ($passpercent <> 0)
		{
			$table_content .= '<td align="center">'.$passpercent.'%</td>';
			$csv_content .= $passpercent . '%, ';
		}
		else
		{
			$table_content .= '<td align="center">'._AT('na').'</td>';
			$csv_content .= _AT('na') . ', ';
		}

		$table_content .= '<td align="center">'.$row['final_score'].'/'.$total_weight.'</td>';
		$csv_content .= $row['final_score'].'/'.$total_weight;

		$total_score += $row['final_score'];

		$answers = array(); /* need this, because we dont know which order they were selected in */

		//get answers for this test result
		$sql = "SELECT question_id, score FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row[result_id] AND question_id IN ($q_sql)";
		$result2 = mysql_query($sql, $db);
		if ($result2){
			while ($row2 = mysql_fetch_assoc($result2)) {
				$answers[$row2['question_id']] = $row2['score'];
			}
		}
		//print answers out for each question
		for($i = 0; $i < $num_questions; $i++) {
			$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
			$table_content .= '<td align="center">';

			if ($answers[$questions[$i]['question_id']] == '') {
				$table_content .= '<span style="color:#ccc;">-</span>';
				$csv_content .= ', -';
			} else {
				$table_content .= $answers[$questions[$i]['question_id']];
				$csv_content .= ', '.$answers[$questions[$i]['question_id']];
				
				if ($random) {
					$questions[$i]['count']++;
				}
			}
			$table_content .= '</td>';
		}

		$table_content .= '</tr>';
		
		// append guest information into CSV content if the test is taken by a guest
		if (substr($row['member_id'], 0, 2) == 'g_' || substr($row['member_id'], 0, 2) == 'G_')
		{
			$sql = "SELECT * FROM ".TABLE_PREFIX."guests WHERE guest_id='".$row['member_id']. "'";
			$result3 = mysql_query($sql, $db);
			$row3 = mysql_fetch_assoc($result3);
			
			$csv_content .= ', '.quote_csv($row3['name']) . ', '.quote_csv($row3['organization']). ', '.quote_csv($row3['location']). ', '.quote_csv($row3['role']). ', '.quote_csv($row3['focus']);
		}
		
		$csv_content .= "\n";
}

function quote_csv($line) {
	$line = str_replace('"', '""', $line);
	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}

$tid = intval($_REQUEST['tid']);

$_pages['mods/_standard/tests/results_all.php']['title_var']  = 'mark_statistics';
$_pages['mods/_standard/tests/results_all.php']['parent'] = 'mods/_standard/tests/results_all_quest.php?tid='.$tid;

$_pages['mods/_standard/tests/results_all_quest.php?tid='.$tid]['title_var'] = 'question_statistics';
$_pages['mods/_standard/tests/results_all_quest.php?tid='.$tid]['parent'] = 'mods/_standard/tests/index.php';
$_pages['mods/_standard/tests/results_all_quest.php?tid='.$tid]['children'] = array('mods/_standard/tests/results_all.php');

if (isset($_POST['reset_filter'])) unset($_POST);

if (!isset($_POST['student_type'])) {
	$_POST['student_type'] = 'all';
}

if (isset($_POST["start_date"])) $start_date = trim($_POST["start_date"]);
if (isset($_POST["end_date"]))$end_date = trim($_POST["end_date"]);

if ($start_date != "" && !isValidDate($start_date)) {
	$msg->addError('START_DATE_INVALID');
}

if ($end_date != "" && !isValidDate($end_date)) {
	$msg->addError('END_DATE_INVALID');
}

$table_content = "";
$csv_content = "";

require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

$sql	= "SELECT title, out_of, result_release, randomize_order, passscore, passpercent FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
$out_of = $row['out_of'];
$random = $row['randomize_order'];
$passscore = $row['passscore'];
$passpercent = $row['passpercent'];
$test_title = str_replace (' ', '_', str_replace(array('"', '<', '>'), '', $row['title']));

$table_content .= '<h3>'.$row['title'].'</h3><br />';

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";

//$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=$tid AND Q.course_id=$_SESSION[course_id] ORDER BY ordering";
$result	= mysql_query($sql, $db);
$questions = array();
$total_weight = 0;
$i = 0;

while ($row = mysql_fetch_assoc($result)) {
	$row['score']	= 0;
	$questions[$i]	= $row;
	$questions[$i]['count'] = 0;
	$q_sql .= $row['question_id'].',';
	$total_weight += $row['weight'];
	$i++;
}
$q_sql = substr($q_sql, 0, -1);
$num_questions = count($questions);

//get all the marked tests for this test
$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R LEFT JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE R.status=1 AND R.test_id=$tid AND R.final_score<>'' ";

if ($start_date)     $sql .= " AND R.date_taken >= '" . $start_date . "'";
if ($end_date)     $sql .= " AND R.date_taken <= '" . $end_date . "'";

if ($_POST["user_type"] == 1) $sql .= " AND R.member_id not like 'G_%' AND R.member_id > 0 ";
if ($_POST["user_type"] == 2) $sql .= " AND (R.member_id like 'G_%' OR R.member_id = 0) ";

$sql .= " ORDER BY M.login, R.date_taken";

$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);
if ($row = mysql_fetch_assoc($result)) {
	$total_score = 0;

	// generate table/csv header line
	$table_content .= '<table class="data static" summary="" style="width: 90%" rules="cols">';
	$table_content .= '<thead>';
	$table_content .= '<tr>';
	$table_content .= '<th scope="col">'._AT('login_name').'</th>';
	$table_content .= '<th scope="col">'._AT('date_taken').'</th>';
	$table_content .= '<th scope="col">'._AT('pass_score').'</th>';
	$table_content .= '<th scope="col">'._AT('mark').'</th>';

	$csv_content .= quote_csv(_AT('login_name')).', ';
	$csv_content .= quote_csv(_AT('date_taken')).', ';
	$csv_content .= quote_csv(_AT('pass_score')).', ';
	$csv_content .= quote_csv(_AT('mark'));

	for($i = 0; $i< $num_questions; $i++) {
		$table_content .= '<th scope="col">Q'.($i+1).' /'.$questions[$i]['weight'].'</th>';

		$csv_content .= ', '.quote_csv('Q'.($i+1).'/'.$questions[$i]['weight']);
	}
	$table_content .= '</tr>';
	$table_content .= '</thead>';
	$table_content .= '<tbody>';
	
	// if there's guest information to be exported into CSV, add header names
	while ($row = mysql_fetch_assoc($result))
	{
		if (substr($row['member_id'], 0, 2) == 'g_' || substr($row['member_id'], 0, 2) == 'G_')
		{
			$csv_content .= ', '. quote_csv(_AT('guest_name'));
			$csv_content .= ', '. quote_csv(_AT('organization'));
			$csv_content .= ', '. quote_csv(_AT('location'));
			$csv_content .= ', '. quote_csv(_AT('role'));
			$csv_content .= ', '. quote_csv(_AT('focus'));
			
			break;
		}
	}
	// reset $result for next loop
	mysql_data_seek($result, 0);
	
	$csv_content .= "\n";
	
	$sql2	= "SELECT anonymous FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result2	= mysql_query($sql2, $db);
	while($row2 =mysql_fetch_array($result2)){
			$anonymous = $row2['anonymous'];
	}

	while ($row = mysql_fetch_assoc($result))
	{
		if ($random) {
			$total_weight = get_random_outof($row['test_id'], $row['result_id']);
		}
		
		// display passed student
		if ($_POST['student_type'] == 'all' ||
		    $_POST['student_type'] == 'passed' &&
		    (($passscore<>0 && $row['final_score']>=$passscore) ||
			   ($passpercent<>0 && ($row['final_score']/$total_weight*100)>=$passpercent)))
			display_test_info($row);
		elseif ($_POST['student_type'] == 'all' ||
		        $_POST['student_type'] == 'failed' &&
		        (($passscore<>0 && $row['final_score']<$passscore) ||
			       ($passpercent<>0 && ($row['final_score']/$total_weight*100)<$passpercent)))
			display_test_info($row);
		elseif ($_POST['student_type'] == 'all')
			display_test_info($row);
	}
	
	$table_content .= '</tbody>';

	$table_content .= '<tfoot>';
	$table_content .= '<tr>';
	$table_content .= '<td colspan="3" align="right"><strong>'._AT('average').':</strong></td>';
	$table_content .= '<td align="center"><strong>'.number_format($total_score/$num_results, 1).'</strong></td>';

	for($i = 0; $i < $num_questions; $i++) {
		$table_content .= '<td class="row1" align="center"><strong>';
			if ($random) {
				$count = $questions[$i]['count'];
			}
			if ($questions[$i]['weight'] && $count) {
					$table_content .= number_format($questions[$i]['score']/$count, 1);
			} else {
				$table_content .= '0.0';
			}
			$table_content .= '</strong></td>';
	}
	$table_content .= '</tr>';

	$table_content .= '<tr>';
	$table_content .= '<td colspan="3">&nbsp;</td>';
	$table_content .= '<td align="center"><strong>';
	if ($total_weight) {
		$table_content .= number_format($total_score/$num_results/$total_weight*100, 1).'%';
	}
	$table_content .= '</strong></td>';

	for($i = 0; $i < $num_questions; $i++) {
		$table_content .= '<td align="center"><strong>';
			if ($random) {
				$count = $questions[$i]['count'];
			}

			if ($questions[$i]['weight'] && $count) {
				$table_content .= number_format($questions[$i]['score']/$count/$questions[$i]['weight']*100, 1).'%';
			} else {
				$table_content .= '00.0%';
			}
		$table_content .= '</strong></td>';
	}
	$table_content .= '</tr>';
	$table_content .= '</tfoot>';
} else {
	$table_content .= '<strong>'._AT('no_results_available').'</strong>';
	$no_result_found = true;
}

// header info has to be in front of any other output, so download
// before display page
if ($_POST['download']){
	if ($no_result_found)
	{
		require (AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	header('Content-Type: application/x-excel');
	header('Content-Disposition: inline; filename="'.$test_title.'.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	
	echo $csv_content;
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>
<script type='text/javascript' src='jscripts/lib/calendar.js'></script>

<div class="input-form">
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?tid='.$tid; ?>">
	<div class="row">
		<label for="start_date"><?php echo _AT('start_date'); ?>(YYYY-MM-DD)</label>
		<input id='start_date' name='start_date' type='text' value='<?php echo $start_date?>' />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('start_date'),event);" />

		<label for="end_date"><?php echo _AT('end_date'); ?>(YYYY-MM-DD)</label>
		<input id='end_date' name='end_date' type='text' value='<?php echo $end_date?>' />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('end_date'),event);" />
	</div>

	<div class="row">
		<?php echo _AT('user_type'); ?><br />
		<input type="radio" name="user_type" value="1" id="u0" <?php if ($_POST['user_type'] == 1) { echo 'checked="checked"'; } ?> /><label for="u0"><?php echo _AT('registered_members'); ?></label> 
		<input type="radio" name="user_type" value="2" id="u1" <?php if ($_POST['user_type'] == 2) { echo 'checked="checked"'; } ?> /><label for="u1"><?php echo _AT('guests'); ?></label> 
		<input type="radio" name="user_type" value="0" id="u2" <?php if (!isset($_POST['user_type']) || ($_POST['user_type'] != 1 && $_POST['user_type'] != 2)) { echo 'checked="checked"'; } ?> /><label for="u2"><?php echo _AT('all'); ?></label> 
	</div>

<?php
// display options for passed/failed students when pass score/percentage is defined
if ($passscore <> 0 || $passpercent <> 0)
{
?>
	<div class="row">
		<?php echo _AT('students'); ?><br />
		<input type="radio" name="student_type" value="all" id="all" <?php if ($_POST['student_type'] == 'all'){echo 'checked="true"';} ?> />
		<label for="all" title="<?php echo _AT('all_students');  ?>"><?php echo _AT('all_students'); ?></label>

		<input type="radio" name="student_type" value="passed" id="passed" <?php if ($_POST['student_type'] == 'passed'){echo 'checked="true"';} ?> />
		<label for="passed" title="<?php echo _AT('all_passed_students');  ?>"><?php echo _AT('all_passed_students'); ?></label>

		<input type="radio" name="student_type" value="failed" id="failed" <?php if ($_POST['student_type'] == 'failed'){echo 'checked="true"';} ?> />
		<label for="failed" title="<?php echo _AT('all_failed_students');  ?>"><?php echo _AT('all_failed_students'); ?></label>
	</div>
<?php
}
?>

	<div class="row buttons">
		<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
		<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		<input type="submit" name="download" value="<?php echo _AT('download_test_csv'); ?>" />
		<input type="hidden" name="test_id" value="<?php echo $tid; ?>" />
	</div>
</form>
</div>


<?php 
echo $table_content;
?>

</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
