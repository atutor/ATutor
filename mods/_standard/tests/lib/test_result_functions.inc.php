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

if (!defined('AT_INCLUDE_PATH')) { exit; }
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/_standard/tests/js/tests.js"></script>';

// if a valid user, then can come from the DB, otherwise
// this might come from _POST or even _SESSION
function get_test_result_id($test_id, &$max_pos) {

    if ($_SESSION['member_id']) {
        $sql    = "SELECT result_id, max_pos FROM %stests_results WHERE test_id=%d AND member_id=%d AND status=0";
        $rows_results    = queryDB($sql, array(TABLE_PREFIX, $test_id, $_SESSION['member_id']), TRUE);
    } else if ($_SESSION['test_result_id']) {
        // guest with on-going test
        $sql    = "SELECT result_id, max_pos FROM %stests_results WHERE test_id=%d AND result_id=%d AND status=0";
        $rows_results    = queryDB($sql, array(TABLE_PREFIX, $test_id, $_SESSION['test_result_id']), TRUE);
    } else {
        return 0; // new guest
    }

   if(count($rows_results) > 0){
        $max_pos = $rows_results['max_pos'];
        return $rows_results['result_id'];
    }

    return 0;
}

function init_test_result_questions($test_id, $is_random, $num_questions, $mid) {

    $sql    = "INSERT INTO %stests_results VALUES (NULL, %d, '%s', NOW(), '', 0, NOW(), 0)";
    $result = queryDB($sql, array(TABLE_PREFIX, $test_id, $mid));
    $result_id = at_insert_id();
    
    if ($is_random) {
        // Retrieve 'num_questions' question_id randomly from those who are related to this test_id

        $non_required_questions = array();
        $required_questions     = array();

        $sql    = "SELECT question_id, required FROM %stests_questions_assoc WHERE test_id=%d";
        $rows_questions    = queryDB($sql, array(TABLE_PREFIX, $test_id));
           
        foreach($rows_questions as $row){
            if ($row['required'] == 1) {
                $required_questions[] = $row['question_id'];
            } else {
                $non_required_questions[] = $row['question_id'];
            }
        }
    
        $num_required = count($required_questions);
        if ($num_required < max(1, $num_questions)) {
            shuffle($non_required_questions);
            $required_questions = array_merge($required_questions, array_slice($non_required_questions, 0, $num_questions - $num_required));
        }

        $random_id_string = implode(',', $required_questions);

        $sql = "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=%d AND TQA.test_id=%d AND TQA.question_id IN (%s) ORDER BY TQ.question_id";
        $rows_questions = queryDB($sql, array(TABLE_PREFIX,TABLE_PREFIX, $_SESSION['course_id'], $test_id, $random_id_string));
        
    } else {

        $sql = "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=%d AND TQA.test_id=%d ORDER BY TQA.ordering, TQA.question_id";
        $rows_questions = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $test_id, ));
    
    }

    // $sql either gets a random set of questions (if $test_row['random']) ordered by 'question_id'
    // or the set of all questions for this test (sorted by 'ordering').
    foreach($rows_questions as $row){
        $sql    = "INSERT INTO %stests_answers VALUES (%d, %d, %d, '', '', '')";
        $result = queryDB($sql, array(TABLE_PREFIX, $result_id, $row['question_id'], $_SESSION['member_id']));
    }

    return $result_id;
}

// $num_questions must be greater than or equal to $row_required['cnt'] + $row_optional['cnt']
function get_total_weight($tid, $num_questions = null) {

    $sql = "SELECT SUM(weight) AS weight, COUNT(*) AS cnt FROM %stests_questions_assoc WHERE test_id=%d AND required = '1' GROUP BY required";
    $row_required = queryDB($sql, array(TABLE_PREFIX, $tid));

    $sql = "SELECT SUM(weight) AS weight, COUNT(*) AS cnt FROM %stests_questions_assoc WHERE test_id=%d AND required = '0' GROUP BY required";
    $row_optional = queryDB($sql, array(TABLE_PREFIX, $tid));
    
    $total_weight = 0;

    if ($num_questions == null) {
        $total_weight = $row_required['weight'] + $row_optional['weight'];
    } else if ($row_optional['cnt'] > 0) {
        $total_weight = $row_required['weight'] + ($row_optional['weight'] / $row_optional['cnt']) * min($num_questions - $row_required['cnt'], $row_optional['cnt']);
    }

    return $total_weight;
}

// returns T/F whether or not this member can view this test:
function authenticate_test($tid) {
    if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
        return TRUE;
    }
    if (!$_SESSION['enroll']) {
        return FALSE;
    }

    $sql    = "SELECT approved FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d AND approved='y'";
    $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $_SESSION['course_id']), TRUE);
    if(count($result) == 0){
        return FALSE;
    }

    $sql    = "SELECT group_id FROM %stests_groups WHERE test_id=%d";
    $rows_groups = queryDB($sql, array(TABLE_PREFIX,  $tid));
    
    if(count($rows_groups) == 0){
        // not limited to any group; everyone has access:
        return TRUE;
    }
    
    foreach($rows_groups as $row){
        $sql     = "SELECT * FROM %sgroups_members WHERE group_id=%d AND member_id=%d";
        $rows_members = queryDB($sql, array(TABLE_PREFIX, $row['group_id'], $_SESSION['member_id']));
        if(count($rows_members) > 0){
            return TRUE;
        }
    }

    //Check assistants privileges

    $sql = "SELECT privileges FROM %scourse_enrollment a WHERE member_id=%d AND course_id=%d";
    $row_privileges = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $_SESSION['course_id']), TRUE);
    
    if(count($row_privileges) > 0){

        list($privileges) = $row_privileges;

        if (query_bit($privileges, AT_PRIV_GROUPS) && query_bit($privileges, AT_PRIV_TESTS)){
            return TRUE;
        }

    }

    return FALSE;
}

function print_question_cats($cat_id = 0) {    

    echo '<option value="0"';
    if ($cat_id == 0) {
        echo ' selected="selected"';
    }
    echo '>'._AT('cats_uncategorized').'</option>' . "\n";

    $sql    = 'SELECT * FROM %stests_questions_categories WHERE course_id=%d ORDER BY title';
    $rows_categories    = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
    
    foreach($rows_categories as $row){
        echo '<option value="'.$row['category_id'].'"';
        if ($row['category_id'] == $cat_id) {
            echo ' selected="selected"';
        }
        echo '>'.$row['title'].'</option>'."\n";
    }
}

function print_VE ($area) {
?>
    <script type="text/javascript">
        document.writeln('<a href="#" onclick="javascript:window.open(\'<?php echo AT_BASE_HREF; ?>mods/_standard/tests/form_editor.php?area=<?php echo $area; ?>\',\'newWin1\',\'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480\'); return false;" style="cursor: pointer; text-decoration: none" ><?php echo _AT('use_visual_editor'); ?></a>');
    </script>

<?php
    //possibley add a <noscript> link to filemanager with target="_blank"
}

function get_random_outof($test_id, $result_id) {    
    $total = 0;
  
    $sql    = 'SELECT SUM(Q.weight) AS weight FROM %stests_questions_assoc Q, %stests_answers A WHERE Q.test_id=%d AND Q.question_id=A.question_id AND A.result_id=%d';
    $row    = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $test_id, $result_id), TRUE);
    
    if(count($row) > 0){
        return $row['weight'];
    }

    return 0;
}

// return the next guest id
function get_next_guest_id() {

    $sql = "SELECT max(cast(substring(guest_id,3) as unsigned))+1 next_guest_id FROM %sguests";
    $row = queryDB($sql, array(TABLE_PREFIX), TRUE);
    
    if ($row["next_guest_id"] == "")  // first guest id
        return "G_0";
    else
        return "G_". $row["next_guest_id"];
}

/**
* Function to build and return all Remedial content for the specific test and a specific student.
* Note: SQL query returns rows which represent remedial content sorted ASC by the test attempt timestamp
* @param    student ID
* @param    test ID
* @return    array of Remedial Content for every failed question
* @author    Alexey Novak
*/
function assemble_remedial_content($student_id, $test_id) {
    global $msg;
    
    $resultArray = array();
    $separator = "<BR />";
    
    if ($student_id < 0 || $test_id < 0) {
        return false;
    }
    
    $sqlQuery = 'SELECT TEMP.remedial_content FROM 
        ( 
        SELECT TA.question_id, TQ.remedial_content 
        FROM '.TABLE_PREFIX.'tests_results TR 
            JOIN '.TABLE_PREFIX.'tests_answers TA ON TA.result_id = TR.result_id 
            JOIN '.TABLE_PREFIX.'tests_questions_assoc TQA ON (TQA.question_id = TA.question_id AND TR.test_id = TQA.test_id) 
            JOIN '.TABLE_PREFIX.'tests_questions TQ ON (TA.question_id = TQ.question_id) 
        WHERE (TR.member_id = %d AND TR.test_id = %d) 
            AND (TQA.weight != TA.score AND TA.score IS NOT NULL AND TA.score <> "" AND TQ.remedial_content <> "") 
        ) AS TEMP 
        GROUP BY TEMP.question_id ';
    
    $sql_params = array($student_id, $test_id);
    
    $sql = vsprintf($sqlQuery, $sql_params);
    try {
    
        $rows_remedial    = queryDB($sql, array());

    } catch (Exception $e) {
        $msg->addError('AT_ERROR_UNKNOWN');
        return false;
    }

    foreach($rows_remedial as $row){
        array_push($resultArray, $row['remedial_content']);
    }
    
    return $resultArray;
}

/**
* Function which allows to check if test shows Remedial Content upon test failure for students
* @param    test ID
* @return    an integer value associated with remedial content for the particular test (currently 0 or 1)
* @author    Alexey Novak
*/
function can_show_remedial_content($test_id) {
    global $msg;
    
    $sql = sprintf('SELECT remedial_content FROM '.TABLE_PREFIX.'tests WHERE test_id=%d', $test_id);
    
    try {
        $row    = queryDB($sql, array(), TRUE);
    } catch (Exception $e) {
        $msg->addError('AT_ERROR_UNKNOWN');
        return 0;
    }
    if(count($row) > 0){
        return intval($row['remedial_content']);
    }
    
    return 0;
}

/**
* Function to generate HTML markup for the remedial contenti if it is available
* @param    student ID
* @param    test ID
* @return    a string HTML with remedial content
* @author    Alexey Novak
*/
function render_remedial_content($student_id, $test_id) {
    // First check if the test allows to show Remedial Content
    if (can_show_remedial_content($test_id) == 0) {
        return;
    }
    
    $remedial_content = assemble_remedial_content($student_id, $test_id);
    
    // If there is no content then just simply exit
    if (count($remedial_content) == 0) {
        return "";
    }
    
    // Wrap every remedial content into a specified HTML markup
/*
    This Anonymous Function code can be supported only with PHP > 5.3.0 Commenting it out until we update php version
    $remedial_content = array_map(function($content) {
        return sprintf('<fieldset class="group_form"><div class="row">%s</div></fieldset>', $content);
    }, $remedial_content);
*/
    // This code is here only because ^ code is not supported yet by the php version we use
    for ($i = 0, $l = count($remedial_content); $i < $l; $i++) {
        $remedial_content[$i] = sprintf('<fieldset class="group_form"><div class="row">%s</div></fieldset>', $remedial_content[$i]);
    }
    
    return sprintf('<h2>%s</h2><div class="input-form remedialContent">%s%s<div class="onRight"><a href="#" onclick="ATutor.mods.tests.focusBegin(); return false;">%s</a></div></div>', _AT('remedial_content'), '<a title="Remedial Content" name="remedial_content"></a>', implode(' ', $remedial_content), _AT('remedial_back_to_the_test'));
}

/**
* Function to generate two radio buttons with Yes and No options.
* @param    array consist of the following parameters:
*        section_name                - Name/Caption which would be rendered above the radio buttons
*        radio_name                    - Name of the variable which would be posted to the server AND also read to determine what options is selected
*        radio_label_N                - Label which is used to describe No option
*        radio_label_Y                - Label which is used to describe Yes option
*        (optional) disabled            - If True it would disable radio buttons and also add (Disabled) to the radio labels
*        (optional) disable_elements    - If present it will generate onfocus="disable_elements('value', false/true);" code for the radiobuttons
* @return    HTML markup for the radio buttons
* @author    Alexey Novak
*/
function generate_radio_button_options($options) {
    $options = ($options) ? $options : array();
    
    if ($options['disabled']) {
        $disabled = 'disabled';
        $disabled_label = '('._AT('disabled').')';
    } else {
        $disabled = $disabled_label = '';
    }
    
    $disabled = ($options['disabled']) ? 'disabled' : '';
    $disable_elements = $options['disable_elements'];
    
    $name = $options['radio_name'];
    if ($options['section_name']) {
        $label = $options['section_name'];
    } else {
        $label = ($name) ? $name : '';
    }
    
    if ($_POST[$name] == 1) {
        $y = 'checked="checked"';
        $n = '';
    } else {
        $y = '';
        $n = 'checked="checked"';
    }
    
    if ($disable_elements) {
        $disable_elements_n = sprintf('ATutor.mods.tests.disable_elements(\'%s\', true);', $disable_elements);
        $disable_elements_y = sprintf('ATutor.mods.tests.disable_elements(\'%s\', false);', $disable_elements);
    } else {
        $disable_elements_n = $disable_elements_y = '';
    }
    
/*
    This Anonymous Function code can be supported only with PHP > 5.3.0 Commenting it out until we update php version
    $generate_radio_button_markup = function($isHide, $name, $checked, $label_name, $disabled, $disable_elements) {
        if ($isHide) {
            $postfix = 'N';
            $value = 0;
        } else {
            $postfix = 'Y';
            $value = 1;
        }
        
        $id = $name.$postfix;
        
        return sprintf('<input type="radio" name="%s" id="%s" value="%d" onclick="this.focus();" %s %s %s/><label for="%s">%s</label>', $name, $id, $value, $disable_elements, $checked, $disabled, $id, $label_name);
    };
*/
    
    return implode(' ', array(
        '<div class="row">',
            _AT($label), '<br />',
/*
            This Anonymous Function code can be supported only with PHP > 5.3.0 Commenting it out until we update php version
            $generate_radio_button_markup(TRUE, $name, $n, $options['radio_label_N'], $disabled, $disable_elements_n),
            $generate_radio_button_markup(FALSE, $name, $y, $options['radio_label_Y'], $disabled, $disable_elements_y),
*/
            generate_radio_button_markup(TRUE, $name, $n, $options['radio_label_N'], $disabled, $disable_elements_n),
            generate_radio_button_markup(FALSE, $name, $y, $options['radio_label_Y'], $disabled, $disable_elements_y),
        $disabled_label,
        '</div>'
    ));
}

/**
* Function to generate HTML markup for the remedial contenti if it is available
* (NOTE) This function is here only because ^ code is not supported yet in the currently running php version
* @param    True if it is a radio button for NO option
* @param    Name attribute of the HTML radio button markup
* @param    Set as True if the radio button should be checked
* @param    Label Text
* @param    True if a radio button should appear disabled
* @param    onFocus JavaScript function for a radio button
* @return    HTML markup for the radio button
* @author    Alexey Novak
*/
function generate_radio_button_markup($isNO, $name, $checked, $label_name, $disabled, $on_focus) {
    if ($isNO) {
        $postfix = 'N';
        $value = 0;
    } else {
        $postfix = 'Y';
        $value = 1;
    }
    
    $id = $name.$postfix;
    
    return sprintf('<input type="radio" name="%s" id="%s" value="%d" onclick="this.focus();" onfocus="%s" %s %s/><label for="%s">%s</label>', $name, $id, $value, $on_focus, $checked, $disabled, $id, $label_name);
}
?>