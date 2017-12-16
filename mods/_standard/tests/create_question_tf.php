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
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_question_queries.inc.php');

authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

if (isset($_POST['cancel'])) {
    $msg->addFeedback('CANCELLED');
    header('Location: question_db.php');
    exit;
} else if ($_POST['submit']) {
    $_POST['required']            = 1; //intval($_POST['required']);
    $_POST['feedback']            = trim($_POST['feedback']);
    $_POST['question']            = trim($_POST['question']);
    $_POST['category_id']         = intval($_POST['category_id']);
    $_POST['answer']              = intval($_POST['answer']);
    $_POST['remedial_content']    = trim($_POST['remedial_content']);

    if ($_POST['question'] == ''){
        $msg->addError(array('EMPTY_FIELDS', _AT('statement')));
    }

    if (!$msg->containsErrors()) {
        $_POST['feedback']            = trim($_POST['feedback']);
        $_POST['question']            = trim($_POST['question']);
        $_POST['remedial_content']    = trim($_POST['remedial_content']);

        $sql_params = array(    $_POST['category_id'], 
                                $_SESSION['course_id'],
                                $_POST['feedback'], 
                                $_POST['question'], 
                                $_POST['answer'],
                                $_POST['remedial_content']);

        $sql = vsprintf(AT_SQL_QUESTION_TRUEFALSE, preg_replace('#\'#','\\\'',preg_replace('#%#','%%',$sql_params)));  
        $result    = queryDB($sql, array());
        
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        header('Location: question_db.php');
        exit;
    }
}

$onload = 'document.form.category_id.focus();';
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="mods/_standard/tests/create_question_tf.php" method="post" name="form">

<div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_tf'); ?></legend>
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label><br />
        <select name="category_id" id="cats">
            <?php print_question_cats($_POST['category_id']); ?>
        </select>
    </div>
    
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('statement'); ?></label>
        <?php print_VE('question'); ?>
        <textarea id="question" cols="50" rows="6" name="question"><?php echo htmlspecialchars($stripslashes($_POST['question'])); ?></textarea>
    </div>
    
    <div class="row">
        <?php echo _AT('answer'); ?><br />
        <input type="radio" name="answer" value="1" id="answer1" /><label for="answer1"><?php echo _AT('true'); ?></label>, 
        <input type="radio" name="answer" value="2" id="answer2" checked="checked" /><label for="answer2"><?php echo _AT('false'); ?></label>
    </div>
    
    <?php require('question_footer.php'); ?>
    
    </fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>