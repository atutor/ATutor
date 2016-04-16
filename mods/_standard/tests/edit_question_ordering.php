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
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/likert_presets.inc.php');

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
    $missing_fields = array();

    $_POST['feedback']    = trim($_POST['feedback']);
    $_POST['question']    = trim($_POST['question']);
    $_POST['category_id'] = intval($_POST['category_id']);

    if ($_POST['question'] == ''){
        $missing_fields[] = _AT('question');
    }

    if (trim($_POST['choice'][0]) == '') {
        $missing_fields[] = _AT('item').' 1';
    }
    if (trim($_POST['choice'][1]) == '') {
        $missing_fields[] = _AT('item').' 2';
    }

    if ($missing_fields) {
        $missing_fields = implode(', ', $missing_fields);
        $msg->addError(array('EMPTY_FIELDS', $missing_fields));
    }
    if (!$msg->containsErrors()) {

        $_POST['remedial_content']    = trim($_POST['remedial_content']);

        $choice_new = array(); // stores the non-blank choices
        $answer_new = array(); // stores the non-blank answers
        $order = 0; // order count
        for ($i=0; $i<10; $i++) {
            /**
             * Db defined it to be 255 length, chop strings off it it's less than that
             * @harris
             */
            $_POST['choice'][$i] = validate_length($_POST['choice'][$i], 255);
            $_POST['choice'][$i] = trim($_POST['choice'][$i]);

            if ($_POST['choice'][$i] != '') {
                /* filter out empty choices/ remove gaps */
                $choice_new[] = $_POST['choice'][$i];
                $answer_new[] = $order++;
            }
        }
        
        $_POST['choice']   = array_pad($choice_new, 10, '');
        $answer_new        = array_pad($answer_new, 10, 0);

        $sql    = "UPDATE %stests_questions SET
            category_id=%d,
            feedback='%s',
            question='%s',
            remedial_content='%s',
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
            answer_9=%d

            WHERE question_id=%d AND course_id=%d";
        $result    = queryDB($sql, array(
                        TABLE_PREFIX,
                        $_POST['category_id'],
                        $_POST['feedback'],
                        $_POST['question'],
                        $_POST['remedial_content'],
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
                        $answer_new['0'],
                        $answer_new['1'],
                        $answer_new['2'],
                        $answer_new['3'],
                        $answer_new['4'],
                        $answer_new['5'],
                        $answer_new['6'],
                        $answer_new['7'],
                        $answer_new['8'],
                        $answer_new['8'],
                        $_POST['qid'],
                        $_SESSION['course_id']));
        
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        if ($_POST['tid']) {
            header('Location: questions.php?tid='.$_POST['tid']);
        } else {
            header('Location: question_db.php');
        }
        exit;
    }
} else {

    $sql    = "SELECT * FROM %stests_questions WHERE question_id=%d AND course_id=%d AND type=6";
    $row    = queryDB($sql, array(TABLE_PREFIX, $qid, $_SESSION['course_id']), TRUE);
    
    if(count($row) == 0){
        require(AT_INCLUDE_PATH.'header.inc.php');
        $msg->printErrors('ITEM_NOT_FOUND');
        require (AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }

    $_POST['required']            = $row['required'];
    $_POST['question']            = $row['question'];
    $_POST['category_id']        = $row['category_id'];
    $_POST['feedback']            = $row['feedback'];
    $_POST['remedial_content']    = $row['remedial_content'];

    for ($i=0; $i<10; $i++) {
        $_POST['choice'][$i] = $row['choice_'.$i];
    }
}
require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo intval($_REQUEST['tid']); ?>" />

<div class="input-form">
    <div class="row">
        <label for="cats"><?php echo _AT('category'); ?></label><br />
        <select name="category_id" id="cats">
            <?php print_question_cats($_POST['category_id']); ?>
        </select>
    </div>

    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label>
        <?php print_VE('question'); ?>
        <textarea id="question" cols="50" rows="6" name="question"><?php echo htmlspecialchars_decode(stripslashes($_POST['question'])); ?></textarea>
    </div>

    <?php for ($i=0; $i<10; $i++): ?>
        <div class="row">
            <?php if ($i < 2): ?>
                <span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
            <?php endif; ?> <?php echo _AT('item'); ?> <?php echo ($i+1); ?>
            
            <?php print_VE('choice_' . $i); ?>
            
            <br />
    
            <textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]"><?php 
            echo htmlspecialchars_decode(stripslashes($_POST['choice'][$i])); ?></textarea> 
        </div>
    <?php endfor; ?>
    
    <?php require('question_footer.php'); ?>
    
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>