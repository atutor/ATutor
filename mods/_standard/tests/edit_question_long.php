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
    $_POST['feedback']            = trim($_POST['feedback']);
    $_POST['question']            = trim($_POST['question']);
    $_POST['category_id']        = intval($_POST['category_id']);
    $_POST['properties']        = intval($_POST['properties']);
    $_POST['remedial_content']    = trim($_POST['remedial_content']);

    if ($_POST['question'] == ''){
        $msg->addError(array('EMPTY_FIELDS', _AT('question')));
    }

    if (!$msg->containsErrors()) {

        $sql = "UPDATE %stests_questions SET category_id=%d,
            feedback='%s',
            question='%s',
            properties=%d,
            remedial_content='%s'
        WHERE question_id=%d AND course_id=%d";
        
        $result    = queryDB($sql, array(
                            TABLE_PREFIX,
                            $_POST['category_id'], 
                            $_POST['feedback'], 
                            $_POST['question'], 
                            $_POST['properties'], 
                            $_POST['remedial_content'],
                            $_POST['qid'],
                            $_SESSION['course_id']));
                            
        $msg->addFeedback('QUESTION_UPDATED');
        if ($_POST['tid']) {
            header('Location: questions.php?tid='.$_POST['tid']);
        } else {
            header('Location: question_db.php');
        }
        exit;
    }
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['submit'])) {

    $sql    = "SELECT * FROM %stests_questions WHERE question_id=%d AND course_id=%d AND type=3";
    $row    = queryDB($sql, array(TABLE_PREFIX, $qid, $_SESSION['course_id']), TRUE);
    
    if(count($row) == 0){
        $msg->printErrors('ITEM_NOT_FOUND');
        require (AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }

    $_POST    = $row;
}

$msg->printErrors();
?>
<form action="mods/_standard/tests/edit_question_long.php" method="post" name="form">
<input type="hidden" name="required" value="1" />
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />

<div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_open'); ?></legend>
    <div class="row">
        <label for="cats"><?php echo _AT('category'); ?></label>
        <select name="category_id" id="cats">
            <?php print_question_cats($_POST['category_id']); ?>
        </select>
    </div>

    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label>
        <?php print_VE('question'); ?>
        <textarea id="question" cols="50" rows="6" name="question"><?php echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
    </div>
    
    <div class="row">
        <?php echo _AT('answer_size'); ?><br />
        <input type="radio" name="properties" value="1" id="az1" <?php if ($_POST['properties'] == 1) { echo 'checked="checked"'; } ?> /><label for="az1"><?php echo _AT('one_word'); ?></label><br />
        <input type="radio" name="properties" value="2" id="az2" <?php if ($_POST['properties'] == 2) { echo 'checked="checked"'; } ?> /><label for="az2"><?php echo _AT('one_sentence'); ?></label><br />
        <input type="radio" name="properties" value="3" id="az3" <?php if ($_POST['properties'] == 3) { echo 'checked="checked"'; } ?> /><label for="az3"><?php echo _AT('short_paragraph'); ?></label><br />
        <input type="radio" name="properties" value="4" id="az4" <?php if ($_POST['properties'] == 4) { echo 'checked="checked"'; } ?> /><label for="az4"><?php echo _AT('one_page'); ?></label>
    </div>
    
    <?php require('question_footer.php'); ?>
    
    </fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>