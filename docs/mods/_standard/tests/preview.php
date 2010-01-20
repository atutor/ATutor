<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: preview.php 7208 2008-01-09 16:07:24Z greg $
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

if ($_POST['back']) {
	header('Location: index.php');
	exit;
} 

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);

/* Retrieve the content_id of this test */
$sql = "SELECT title, random, num_questions, instructions FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db); 
if (!($test_row = mysql_fetch_assoc($result))) {
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$num_questions = $test_row['num_questions'];
$rand_err = false;

if ($row['random']) {
	/* !NOTE! this is a really awful way of randomizing questions !NOTE! */

	/* Retrieve 'num_questions' question_id randomly choosed from  
	those who are related to this content_id*/
	$sql	= "SELECT question_id FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
	$result	= mysql_query($sql, $db); 
	$i = 0;
	$row2 = mysql_fetch_assoc($result);
	/* Store all related question in cr_questions */
	while ($row2['question_id'] != '') {
		$cr_questions[$i] = $row2['question_id'];
		$row2 = mysql_fetch_assoc($result);
		$i++;
	}
	if ($i < $num_questions) {
		/* this if-statement is misleading. */
		/* one should still be able to preview a test before all its questions have been added. */
		/* ie. preview as questions are added. */
		/* bug # 0000615 */
		$rand_err = true;
	} else {
		/* Randomly choose only 'num_question' question */
		$random_idx = rand(0, $i-1);
		$random_id_string = $cr_questions[$random_idx];
		$j = 0;
		$extracted[$j] = $random_idx;
		$j++;
		$num_questions--;
		while ($num_questions > 0) {
			$done = false;
			while (!$done) {
				$random_idx = rand(0, $i-1);
				$done = true;
				for ($k=0;$k<$j;$k++) {
					if ($extracted[$k]== $random_idx) {
						$done = false;
						break;
					}
				}
			}
			$extracted[$j] = $random_idx;
			$j++;
			$random_id_string = $random_id_string.','.$cr_questions[$random_idx];
			$num_questions--;
		}
		$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid AND TQA.question_id IN ($random_id_string) ORDER BY TQA.ordering, TQA.question_id";
	}
} else {
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
}
$result	= mysql_query($sql, $db);
$count = 1;
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="preview">';

if (($row = mysql_fetch_assoc($result)) && !$rand_err) {
	?>
	<div class="input-form" style="width:80%">
	<div class="row"><h2><?php echo $test_row['title']; ?></h2></div>


	<?php if ($test_row['instructions'] != ''): ?>
		<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
			<strong><?php echo _AT('instructions'); ?></strong>
		</div>
		<div class="row" style="padding-bottom: 20px"><?php echo $test_row['instructions']; ?></div>
	<?php endif; ?>
	
	<?php
	do {
		$o = TestQuestions::getQuestion($row['type']);
		$o->display($row);
	} while ($row = mysql_fetch_assoc($result));
	?>
	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('back'); ?>" name="back" />
	</div>

	</div>
	</form>
<script type="text/javascript">
//<!--
function iframeSetHeight(id, height) {
	document.getElementById("qframe" + id).style.height = (height + 20) + "px";
}
//-->
</script>
<?php
} else {
	$msg->printErrors('NO_QUESTIONS');
}


require(AT_INCLUDE_PATH.'footer.inc.php');
?>