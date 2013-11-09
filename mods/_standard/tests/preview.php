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
$sql = "SELECT title, random, num_questions, instructions FROM %stests WHERE test_id=%d";
$row_test	= queryDB($sql, array(TABLE_PREFIX, $tid), TRUE); 

if(count($row_test) == 0){
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$num_questions = $row_test['num_questions'];
$rand_err = false;

if ($row_test['random']) {
	/* !NOTE! this is a really awful way of randomizing questions !NOTE! */
	/* Retrieve 'num_questions' question_id randomly choosed from  
	those who are related to this content_id*/
	$sql	= "SELECT question_id FROM %stests_questions_assoc WHERE test_id=%d";
	$rows_questions	= queryDB($sql, array(TABLE_PREFIX, $tid)); 

	$i = 0;
	/* Store all related question in cr_questions */
	foreach($rows_questions as $row2){
		$cr_questions[$i] = $row2['question_id'];
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
		$sql = "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=%d AND TQA.test_id=%d AND TQA.question_id IN (%s) ORDER BY TQA.ordering, TQA.question_id";
        $rows_questions	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $tid, $random_id_string));
	}
} else {
	$sql	= "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=%d AND TQA.test_id=%d ORDER BY TQA.ordering, TQA.question_id";
    $rows_questions	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $tid));
}

$count = 1;
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="preview">';

if(count($rows_questions) > 0){
	?>
	<div class="input-form" style="width:95%">
	<div class="row"><h2><?php echo $row_test['title']; ?></h2></div>


	<?php if ($row_test['instructions'] != ''): ?>
		<div class="test_instruction">
			<strong><?php echo _AT('instructions'); ?></strong>
		</div>
		<div class="row" style="padding-bottom: 20px"><?php echo $row_test['instructions']; ?></div>
	<?php endif; ?>
	
	<?php
	foreach($rows_questions as $row){
		$o = TestQuestions::getQuestion($row['type']);
		$o->display($row);
	} 
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