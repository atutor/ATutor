<?php
/****************************************************************/
/* ATutor                                                        */
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                                */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.                */
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_question_queries.inc.php');

authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

if (isset($_POST['cancel'])) {
    $msg->addFeedback('CANCELLED');
    header('Location: question_db.php');
    exit;
} else if ($_POST['submit']) {
    $_POST['feedback']          = htmlspecialchars(trim($_POST['feedback']), ENT_QUOTES);
    $_POST['question']          = htmlspecialchars(trim($_POST['question']), ENT_QUOTES);
    $_POST['category_id']       = intval($_POST['category_id']);
    $_POST['properties']        = intval($_POST['properties']);
    $_POST['remedial_content']  = htmlspecialchars(trim($_POST['remedial_content']), ENT_QUOTES);

    if ($_POST['question'] == ''){
        $msg->addError(array('EMPTY_FIELDS', _AT('question')));
    }

    if (!$msg->containsErrors()) {
        $_POST['feedback']          = $addslashes($_POST['feedback']);
        $_POST['question']          = $addslashes($_POST['question']);
        $_POST['remedial_content']  = $addslashes($_POST['remedial_content']);

        $sql_params = array(    $_POST['category_id'],
                                $_SESSION['course_id'],
                                $_POST['feedback'], 
                                $_POST['question'], 
                                $_POST['properties'],
                                $_POST['remedial_content']);

        $sql = vsprintf(AT_SQL_QUESTION_LONG, preg_replace('#\'#','\\\'',preg_replace('#%#','%%',$sql_params)));
        $result    = queryDB($sql, array());

        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        header('Location: question_db.php');
        exit;
    }
}

$onload = 'document.form.category_id.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['properties'])) {
    $_POST['properties'] = 1;
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="required" value="1" />
<div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_open'); ?></legend>
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label><br />
        <select name="category_id" id="cats">
            <?php print_question_cats($_POST['category_id']); ?>
        </select>
    </div>

    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label>
        <?php print_VE('question'); ?>
        <textarea id="question" cols="50" rows="6" name="question"><?php echo stripslashes($_POST['question']); ?></textarea>
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