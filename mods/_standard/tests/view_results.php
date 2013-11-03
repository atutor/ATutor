<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.                */
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php'); // for print_result and print_score
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/_standard/tests/js/tests.js"></script>';
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
    $content_base_href = 'get.php/';
} else {
    $course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

if (isset($_REQUEST['goto_content']))
{
    header('Location: '.url_rewrite('content.php?cid='.$cid, AT_PRETTY_URL_IS_HEADER));
    exit;
}
if (isset($_REQUEST['back']))
{
    header('Location: '.url_rewrite('mods/_standard/tests/my_tests.php', AT_PRETTY_URL_IS_HEADER));
    exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);
if (isset($_REQUEST['cid'])) $cid = $_REQUEST['cid'];

$sql    = "SELECT title, random, passfeedback, failfeedback, passscore, passpercent FROM %stests WHERE test_id=%d AND course_id=%d";
$row    = queryDB($sql, array(TABLE_PREFIX, $tid, $_SESSION['course_id']), TRUE);

$test_title    = $row['title'];
$passfeedback    = $row['passfeedback'];
$failfeedback    = $row['failfeedback'];
$passscore    = $row['passscore'];
$passpercent    = $row['passpercent'];
$is_random  = $row['random'];

$mark_right = ' <img src="'.$_base_path.'images/check.gif" alt="'._AT('correct_answer').'" title="'._AT('correct_answer').'" />';
$mark_wrong = ' <img src="'.$_base_path.'images/x.gif" alt="'._AT('wrong_answer').'" title="'._AT('wrong_answer').'" />';

$sql    = "SELECT * FROM %stests_results WHERE result_id=%d";
$row    = queryDB($sql, array(TABLE_PREFIX, $rid), TRUE); 

if (count($row) == 0){
    $msg->printErrors('ITEM_NOT_FOUND');
    require(AT_INCLUDE_PATH.'footer.inc.php');
    exit;
}
$final_score= $row['final_score'];

//make sure they're allowed to see results now
$sql    = "SELECT result_release, out_of FROM %stests WHERE test_id=%d AND course_id=%d";
$row    = queryDB($sql, array(TABLE_PREFIX, $tid, $_SESSION['course_id']), TRUE); 

if ( ($row['result_release']==AT_RELEASE_NEVER) || ($row['result_release']==AT_RELEASE_MARKED && $final_score=='') ) {
    $msg->printErrors('RESULTS_NOT_RELEASED');
    require(AT_INCLUDE_PATH.'footer.inc.php');
    exit;
}

$out_of = $row['out_of'];

/* Retrieve randomly choosed questions */
$sql    = "SELECT question_id FROM %stests_answers WHERE result_id=%d";
$rows_answers = queryDB($sql, array(TABLE_PREFIX, $rid)); 

$count = 0;
foreach($rows_answers as $row){
    $random_id_string .= $rows_answers[$count]['question_id'].",";
    $count++;
}

$random_id_string = rtrim($random_id_string, ",");

if (!$random_id_string) {
    $random_id_string = 0;
}
if ($is_random) {
    $sql    = "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=%d AND TQ.question_id IN (%s) ORDER BY TQ.question_id";
} else {
    $sql    = "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=%d AND TQ.question_id IN (%s) ORDER BY TQA.ordering, TQ.question_id";
}

$rows_questions  = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $tid, $random_id_string));

if(count($rows_questions) == 0){
    echo '<p>'._AT('no_questions').'</p>';
    require(AT_INCLUDE_PATH.'footer.inc.php');
    exit;
}

foreach($rows_questions as $row){

    $sql_this_score = "SELECT * FROM %stests_answers WHERE result_id=%d AND question_id=%d AND member_id=%d";
    $this_score    = queryDB($sql_this_score, array(TABLE_PREFIX, $rid, $row['question_id'], $_SESSION['member_id']), TRUE); 
    
    $my_score+=$this_score['score'];
    $this_total += $row['weight'];
}
?>
<form method="get" action="<?php echo $_REQUEST['PHP_SELF']; ?>">
<?php if (isset($_REQUEST['cid'])) {?> <input type="hidden" name="cid" value="<?php echo $cid; ?>" /> <?php }?>

<div class="input-form">
    <div class="row">
        <h2><?php echo AT_print($test_title, 'tests.title'); ?>
            <a href="#" style="display:none;" class="hideAllRemedialLink">
                <span class="hideAllLabel"><?php echo _AT('hide_all_remedial'); ?></span>
                <span class="showAllLabel" style="display:none;"><?php echo _AT('show_all_remedial'); ?></span>
            </a>
        </h2>
    </div>

    <div class="row">
        <h3 align="center">
            <?php 
                // don't display any feedback if test is created as "no pass score"
                if (($passscore == 0 && $passpercent == 0) || ($passpercent <> 0 && $this_total == 0))
                    echo '';
                // display pass feedback for passed students
                elseif (($passscore<>0 && $my_score>=$passscore) ||
                    ($passpercent<>0 && ($my_score/$this_total*100)>=$passpercent))
                    echo '<span style="color:green">' . $passfeedback . '</span>';
                // otherwise, display fail feedback
                else
                    echo '<span style="color:red">' . $failfeedback . '</span>'; 
            ?>
        </h3>
    </div>

    <?php if ($row['instructions'] != ''): ?>
        <div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
            <strong><?php echo _AT('instructions'); ?></strong>
        </div>
        <div class="row" style="padding-bottom: 20px"><?php echo $row['instructions']; ?></div>
    <?php endif; ?>

    <?php
    foreach($rows_questions as $row){

        $sql        = "SELECT * FROM %stests_answers WHERE result_id=%d AND question_id=%d AND member_id=%d";
        $answer_row    = queryDB($sql, array(TABLE_PREFIX, $rid, $row['question_id'], $_SESSION['member_id']), TRUE); 

        $obj = TestQuestions::getQuestion($row['type']);
        $obj->displayResult($row, $answer_row);

        if ($row['feedback']) {
            echo '<div class="row"><p><strong>'._AT('feedback').':</strong> ';
            echo AT_print(nl2br($row['feedback']), 'tests_questions.feedback').'</p></div>';
        }
        
        if ($row['remedial_content'] && $answer_row['score'] != $row['weight']) {
        ?>
        <fieldset class="group_form">
            <legend class="group_form"><?php echo _AT('remedial_content'); ?>&nbsp;
                <a href='#' class='collapsible hide'>
                    <span class="hideLabel"><?php echo _AT('hide'); ?></span>
                    <span class="showLabel" style="display:none;"><?php echo _AT('show'); ?></span>
                </a>
            </legend>
            <div class="row">
                <?php echo $row['remedial_content']; ?>
            </div>
        </fieldset>
        <?php
        }
    }
    ?>

    <?php if ($this_total): ?>
        <div class="test_instruction">
            <strong>
                <span style="float: right"><?php echo $my_score .' / '.$this_total; ?> <?php echo _AT('points'); ?></span>
                <?php echo _AT('final_score'); ?>:
            </strong>
        </div>
    <?php else: ?>
        <div class="test_instruction">
            <strong>
                <?php echo _AT('done'); ?>!
            </strong>
        </div>
    <?php endif; ?>

    <div class="row buttons">
        <?php if (isset($cid)) {?>
        <input type="submit" value="<?php echo _AT('goto_content'); ?>" name="goto_content" />
        <?php } else {?>
        <input type="submit" value="<?php echo _AT('back'); ?>" name="back" />
        <?php }?>
    </div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>