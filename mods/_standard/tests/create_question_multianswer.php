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

if (isset($_POST['cancel']) || isset($_POST['submit_no'])) {
    $msg->addFeedback('CANCELLED');
    header('Location: question_db.php');
    exit;
} else if ($_POST['submit'] || $_POST['submit_yes']) {
    $_POST['required']            = intval($_POST['required']);
    $_POST['feedback']            = trim($_POST['feedback']);
    $_POST['question']            = trim($_POST['question']);
    $_POST['category_id']         = intval($_POST['category_id']);
    $_POST['remedial_content']    = trim($_POST['remedial_content']);

    if ($_POST['question'] == ''){
        $msg->addError(array('EMPTY_FIELDS', _AT('question')));
    }
        
    if (!$msg->containsErrors()) {
        $choice_new = array(); // stores the non-blank choices
        $answer_new = array(); // stores the associated "answer" for the choices
        for ($i=0; $i<10; $i++) {
            /**
             * Db defined it to be 255 length, chop strings off it it's less than that
             * @harris
             */
            $_POST['choice'][$i] = validate_length($_POST['choice'][$i], 255);
            $_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
            $_POST['answer'][$i] = intval($_POST['answer'][$i]);

            if ($_POST['choice'][$i] == '') {
                /* an empty option can't be correct */
                $_POST['answer'][$i] = 0;
            } else {
                /* filter out empty choices/ remove gaps */
                $choice_new[] = $_POST['choice'][$i];
                $answer_new[] = $_POST['answer'][$i];

                if ($_POST['answer'][$i] != 0)
                    $has_answer = TRUE;
            }
        }
            
        if ($has_answer != TRUE && !$_POST['submit_yes']) {
    
            $hidden_vars['required']    = htmlspecialchars($_POST['required']);
            $hidden_vars['feedback']    = htmlspecialchars($_POST['feedback']);
            $hidden_vars['question']    = htmlspecialchars($_POST['question']);
            $hidden_vars['category_id'] = htmlspecialchars($_POST['category_id']);

            for ($i = 0; $i < count($choice_new); $i++) {
                $hidden_vars['answer['.$i.']'] = htmlspecialchars($answer_new[$i]);
                $hidden_vars['choice['.$i.']'] = htmlspecialchars($choice_new[$i]);
            }

            $msg->addConfirm('NO_ANSWER', $hidden_vars);
        } else {
        
            //add slahes throughout - does that fix it?
            $_POST['answer']            = $answer_new;
            $_POST['choice']            = $choice_new;
            $_POST['answer']            = array_pad($_POST['answer'], 10, 0);
            $_POST['choice']            = array_pad($_POST['choice'], 10, '');
            $_POST['feedback']            = $addslashes($_POST['feedback']);
            $_POST['question']            = $addslashes($_POST['question']);
            $_POST['remedial_content']    = $addslashes($_POST['remedial_content']);

            $sql_params = array(    $_POST['category_id'], 
                                    $_SESSION['course_id'],
                                    $_POST['feedback'], 
                                    $_POST['question'], 
                                    $_POST['choice'][0], 
                                    $_POST['choice'][1], 
                                    $_POST['choice'][2], 
                                    $_POST['choice'][3], 
                                    $_POST['choice'][4], 
                                    $_POST['choice'][5], 
                                    $_POST['choice'][6], 
                                    $_POST['choice'][7], 
                                    $_POST['choice'][8], 
                                    $_POST['choice'][9], 
                                    $_POST['answer'][0], 
                                    $_POST['answer'][1], 
                                    $_POST['answer'][2], 
                                    $_POST['answer'][3], 
                                    $_POST['answer'][4], 
                                    $_POST['answer'][5], 
                                    $_POST['answer'][6], 
                                    $_POST['answer'][7], 
                                    $_POST['answer'][8], 
                                    $_POST['answer'][9],
                                    $_POST['remedial_content']);
            $sql = vsprintf(AT_SQL_QUESTION_MULTIANSWER, $sql_params);

        $result    = queryDB($sql, array());

            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
            header('Location: question_db.php');
            exit;
        }
    }
}

$onload = 'document.form.category_id.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printConfirm();
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="required" value="1" />
<div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_ma'); ?></legend>
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label><br />
        <select name="category_id" id="cats">
            <?php print_question_cats($_POST['category_id']); ?>
        </select>
    </div>

    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label>
        <?php print_VE('question'); ?>
        <textarea id="question" cols="50" rows="4" name="question"><?php echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
    </div>

<?php
    for ($i=0; $i<10; $i++) {
?>
    <div class="row">
        <?php echo _AT('choice'); ?> <?php echo ($i+1); ?>
        
        <?php print_VE('choice_' . $i); ?>
        
        <br />

        <small><input type="checkbox" name="answer[<?php echo $i; ?>]" id="answer_<?php echo $i; ?>" value="1" <?php if($_POST['answer'][$i]) { echo 'checked="checked"';} ?>><label for="answer_<?php echo $i; ?>"><?php echo _AT('correct_answer'); ?></label></small>
        
        <textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]"><?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea>
    </div>
    <?php } ?>
    
    <?php require('question_footer.php'); ?>
    
    </fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>