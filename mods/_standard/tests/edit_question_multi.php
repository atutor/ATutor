<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

$qid = intval($_GET['qid']);
if ($qid == 0){
    $qid = intval($_POST['qid']);
}

if (isset($_POST['cancel'])) {
    $msg->addFeedback('CANCELLED');
    if ($_POST['tid']) {
        header('Location: questions.php?tid='.$_POST['tid']);
    } else {
        header('Location: question_db.php');
    }
    exit;
} else if (isset($_POST['submit'])) {
    $_POST['required']            = intval($_POST['required']);
    $_POST['feedback']            = trim($_POST['feedback']);
    $_POST['question']            = trim($_POST['question']);
    $_POST['tid']                = intval($_POST['tid']);
    $_POST['qid']                = intval($_POST['qid']);
    $_POST['weight']            = intval($_POST['weight']);
    $_POST['answer']            = intval($_POST['answer']);
    $_POST['remedial_content']    = trim($_POST['remedial_content']);

    if ($_POST['question'] == ''){
        $msg->addError(array('EMPTY_FIELDS', _AT('question')));
    }

    if (!$msg->containsErrors()) {
        $answers    = array_fill(0, 10, 0);
        $answers[$_POST['answer']] = 1;

        for ($i=0; $i<10; $i++) {
            $_POST['choice'][$i] = trim($_POST['choice'][$i]);
        }

        $sql    = "UPDATE %stests_questions SET
            category_id=%d,
            feedback='%s',
            question='%s',
            choice_0='%s',
            choice_1='%s',
            choice_2='%s',
            choice_3='%s',
            choice_4='%s',
            choice_5='%s',
            choice_6='%s',
            choice_7='%s',
            choice_8='%s',
            choice_9='%s',
            answer_0=%d,
            answer_1=%d,
            answer_2=%d,
            answer_3=%d,
            answer_4=%d,
            answer_5=%d,
            answer_6=%d,
            answer_7=%d,
            answer_8=%d,
            answer_9=%d,
            remedial_content='%s'
            WHERE question_id=%d AND course_id=%d";

        $result    = queryDB($sql, array(
                        TABLE_PREFIX,
                        $_POST['category_id'],
                        $_POST['feedback'],
                        $_POST['question'],
                        $_POST['choice']['0'],
                        $_POST['choice']['1'],
                        $_POST['choice']['2'],
                        $_POST['choice']['3'],
                        $_POST['choice']['4'],
                        $_POST['choice']['5'],
                        $_POST['choice']['6'],
                        $_POST['choice']['7'],
                        $_POST['choice']['8'],
                        $_POST['choice']['9'],
                        $answers[0],
                        $answers[1],
                        $answers[2],
                        $answers[3],
                        $answers[4],
                        $answers[5],
                        $answers[6],
                        $answers[7],
                        $answers[8],
                        $answers[9],
                        $_POST['remedial_content'],
                        $_POST['qid'],
                        $_SESSION['course_id'] ));
        
        $msg->addFeedback('QUESTION_UPDATED');
        if ($_POST['tid']) {
            header('Location: questions.php?tid='.$_POST['tid']);            
        } else {
            header('Location: question_db.php');
        }
        exit;
    }
}

if (!isset($_POST['submit'])) {

    $sql    = "SELECT * FROM %stests_questions WHERE question_id=%d AND course_id=%d AND type=1";
    $row    = queryDB($sql, array(TABLE_PREFIX, $qid, $_SESSION['course_id']), TRUE);
    
    if(count($row ) == 0){
        require(AT_INCLUDE_PATH.'header.inc.php');
        $msg->printErrors('ITEM_NOT_FOUND');
        require (AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }
    $_POST['category_id']        = $row['category_id'];
    $_POST['feedback']            = $row['feedback'];
    $_POST['required']            = $row['required'];
    $_POST['weight']            = $row['weight'];
    $_POST['question']            = $row['question'];
    $_POST['remedial_content']    = $row['remedial_content'];
    

    for ($i=0; $i<10; $i++) {
        $_POST['choice'][$i] = $row['choice_'.$i];
        $_POST['answer'][$i] = $row['answer_'.$i];
    }
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="mods/_standard/tests/edit_question_multi.php" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo intval($_REQUEST['tid']); ?>" />
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="required" value="1" />

<div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_mc'); ?></legend>
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label>
        <select name="category_id" id="cats">
            <?php print_question_cats($_POST['category_id']); ?>
        </select>
    </div>

    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label>
        <?php print_VE('question'); ?>
        <textarea id="question" cols="50" rows="4" name="question"><?php echo htmlspecialchars_decode(stripslashes($_POST['question'])); ?></textarea>
    </div>

    <?php 
    for ($i=0; $i<10; $i++) { ?>
        <div class="row">
            <label for="choice_<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo ($i+1); ?></label> 
            <?php print_VE('choice_' . $i); ?>
            <br />
            <small><input type="radio" name="answer" id="answer_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($_POST['answer'][$i]) { echo 'checked="checked"';} ?>><label for="answer_<?php echo $i; ?>"><?php echo _AT('correct_answer'); ?></label></small>
            

            <textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]" class="formfield"><?php echo htmlspecialchars_decode(stripslashes($_POST['choice'][$i])); ?></textarea>
        </div>
    <?php } ?>

    <?php require('question_footer.php'); ?>
    </fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>