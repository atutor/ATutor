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

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/_standard/tests/js/tests.js"></script>';

authenticate(AT_PRIV_TESTS);
tool_origin();
$test_type = 'normal';

if (isset($_POST['cancel'])) {
    $msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
} else if (isset($_POST['submit'])) {
    $missing_fields                = array();
    $_POST['title']                = trim($_POST['title']);
    $_POST['description']        = trim($_POST['description']);
    $_POST['num_questions']        = intval($_POST['num_questions']);
    $_POST['num_takes']            = intval($_POST['num_takes']);
    $_POST['content_id']        = intval($_POST['content_id']);
    $_POST['passpercent']        = intval($_POST['passpercent']);
    $_POST['passscore']            = intval($_POST['passscore']);
    $_POST['passfeedback']        = trim($_POST['passfeedback']);
    $_POST['failfeedback']        = trim($_POST['failfeedback']);
    $_POST['num_takes']            = intval($_POST['num_takes']);
    $_POST['anonymous']            = intval($_POST['anonymous']);
    $_POST['allow_guests']        = $_POST['allow_guests'] ? 1 : 0;
    $_POST['show_guest_form']    = $_POST['show_guest_form'] ? 1 : 0;
    $_POST['instructions']        = $_POST['instructions'];
    $_POST['display']            = intval($_POST['display']);
    $_POST['remedial_content']    = intval($_POST['remedial_content']);

    // currently these options are ignored for tests:
    $_POST['result_release'] = intval($_POST['result_release']); 
    $_POST['format']       = intval($_POST['format']);
    $_POST['order']           = 1;  //intval($_POST['order']);
    $_POST['difficulty']   = 0;  //intval($_POST['difficulty']);     /* avman */
        
    if ($_POST['title'] == '') {
        $missing_fields[] = _AT('title');
    }

    if ($_POST['random'] && !$_POST['num_questions']) {
        $missing_fields[] = _AT('num_questions_per_test');
    }

    if ($_POST['pass_score']==1 && !$_POST['passpercent']) {
        $missing_fields[] = _AT('percentage_score');
    }

    if ($_POST['pass_score']==2 && !$_POST['passscore']) {
        $missing_fields[] = _AT('points_score');
    }

    if ($missing_fields) {
        $missing_fields = implode(', ', $missing_fields);
        $msg->addError(array('EMPTY_FIELDS', $missing_fields));
    }

    $day_start    = intval($_POST['day_start']);
    $month_start= intval($_POST['month_start']);
    $year_start    = intval($_POST['year_start']);
    $hour_start    = intval($_POST['hour_start']);
    $min_start    = intval($_POST['min_start']);

    $day_end    = intval($_POST['day_end']);
    $month_end    = intval($_POST['month_end']);
    $year_end    = intval($_POST['year_end']);
    $hour_end    = intval($_POST['hour_end']);
    $min_end    = intval($_POST['min_end']);

    if (!checkdate($month_start, $day_start, $year_start)) {
        $msg->addError('START_DATE_INVALID');
    }

    if (!checkdate($month_end, $day_end, $year_end)) {
        $msg->addError('END_DATE_INVALID');
    }

    if (mktime($hour_end,   $min_end,   0, $month_end,   $day_end,   $year_end) < 
        mktime($hour_start, $min_start, 0, $month_start, $day_start, $year_start)) {
            $msg->addError('END_DATE_INVALID');
    }

    if (!$msg->containsErrors()) {
        if (strlen($month_start) == 1){
            $month_start = "0$month_start";
        }
        if (strlen($day_start) == 1){
            $day_start = "0$day_start";
        }
        if (strlen($hour_start) == 1){
            $hour_start = "0$hour_start";
        }
        if (strlen($min_start) == 1){
            $min_start = "0$min_start";
        }

        if (strlen($month_end) == 1){
            $month_end = "0$month_end";
        }
        if (strlen($day_end) == 1){
            $day_end = "0$day_end";
        }
        if (strlen($hour_end) == 1){
            $hour_end = "0$hour_end";
        }
        if (strlen($min_end) == 1){
            $min_end = "0$min_end";
        }

        $start_date = "$year_start-$month_start-$day_start $hour_start:$min_start:00";
        $end_date    = "$year_end-$month_end-$day_end $hour_end:$min_end:00";

        //If title exceeded database defined length, truncate it.
        $_POST['title'] = validate_length($_POST['title'], 100);

        $sql = "INSERT INTO %stests " .
               "(test_id,
             course_id,
             title,
             description,
             format,
             start_date,
             end_date,
             randomize_order,
             num_questions,
             instructions,
             content_id,
             passscore,
             passpercent,
             passfeedback,
             failfeedback,
             result_release,
             random,
             difficulty,
             num_takes,
             anonymous,
             out_of,
             guests,
             display,
             show_guest_form,
             remedial_content)" .
        "VALUES 
            (NULL, %d, '%s', '%s', %d, '%s', '%s', %d, %d, '%s', %d, %d, %d, '%s', '%s', %d, %d, %d, %d, %d, '', %d, %d, %d, %d)";

        $result = queryDB($sql, array(
                    TABLE_PREFIX,
                    $_SESSION[course_id],
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['format'],
                    $start_date,
                    $end_date,
                    $_POST['order'],
                    $_POST['num_questions'],
                    $_POST['instructions'],
                    $_POST['content_id'],
                    $_POST['passscore'],
                    $_POST['passpercent'],
                    $_POST['passfeedback'],
                    $_POST['failfeedback'],
                    $_POST['result_release'],
                    $_POST['random'],
                    $_POST['difficulty'],
                    $_POST['num_takes'],
                    $_POST['anonymous'],
                    $_POST['allow_guests'],
                    $_POST['display'],
                    $_POST['show_guest_form'],
                    $_POST['remedial_content']));
        $tid = at_insert_id();
        
        if (isset($_POST['groups']) && $tid) {

            $sql = "INSERT INTO %stests_groups VALUES ";
            
            foreach ($_POST['groups'] as $group) {
                $group = intval($group);
                $sql .= "($tid, $group),";
            }
            $sql = substr($sql, 0, -1);

            $result = queryDB($sql, array(TABLE_PREFIX));
        }

        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
    }
}

if (isset($_POST['num_questions']) && ($_POST['num_questions'] === 0)) {
    $_POST['num_questions'] = '';
}

$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="test_type" value="<?php echo $test_type; ?>" />
<div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_test'); ?></legend>
    <div class="row">
        <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
        <input type="text" name="title" id="title" size="30" value="<?php echo $_POST['title']; ?>" />
    </div>

    <div class="row">
        <label for="description"><?php echo _AT('test_description'); ?></label><br />
        <textarea name="description" cols="35" rows="3" id="description"><?php echo htmlspecialchars($_POST['description']); ?></textarea>
    </div>

    <div class="row">
        <label for="num_t"><?php echo _AT('num_takes_test'); ?></label><br />
        <select name="num_takes" id="num_t">
            <option value="<?php echo AT_TESTS_TAKE_UNLIMITED; ?>" <?php if ($_POST['num_takes'] == AT_TESTS_TAKE_UNLIMITED) { echo ''; } ?>><?php echo _AT('unlimited'); ?></option>
            <?php 
            foreach(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20) as $e) {
                $selected = ($_POST['num_takes'] == $e) ? ' selected="selected"' : '';
                // Set to 1 attempt by default
                if(!isset($_POST['num_takes']) && $e == 1){
                    echo sprintf('<option value="%d" selected="selected">%d</option>', $e, $e);
                }else{
                    echo sprintf('<option value="%d" %s>%d</option>', $e, $selected, $e);
                }
            }
            ?>
        </select>
    </div>

    <?php
        echo generate_radio_button_options(array(    'section_name' => 'available_on_my_courses',
                                                    'radio_name' => 'format',
                                                    'radio_label_N' => _AT('no'),
                                                    'radio_label_Y' => _AT('yes')));

        echo generate_radio_button_options(array(    'section_name' => 'anonymous_test',
                                                    'radio_name' => 'anonymous',
                                                    'radio_label_N' => _AT('no'),
                                                    'radio_label_Y' => _AT('yes')));

        $allow_guests = generate_radio_button_options(array(    'radio_name' => 'allow_guests',
                                                                'radio_label_N' => _AT('no'),
                                                                'radio_label_Y' => _AT('yes'),
                                                                'disable_elements' => 'show_guest_form'));
        
        $y = ($_POST['show_guest_form'] == 1) ? 'checked="checked"' : '';
        $disable_show_guest_form = ($_POST['allow_guests'] == 1) ? '' : 'disabled="disabled"';
        $show_guest_form = sprintf('<br /><input type="checkbox" name="show_guest_form" id="show_guest_form" value="1" %s %s /><label for="show_guest_form">%s</label>', $y, $disable_show_guest_form, _AT('show_guest_form'));
        
        // Before rendering add a checkbox into the div after radio buttons
        echo str_replace("</div>", $show_guest_form."</div>", $allow_guests);

        echo generate_radio_button_options(array(    'radio_name' => 'display',
                                                    'radio_label_N' => _AT('all_questions_on_page'),
                                                    'radio_label_Y' => _AT('one_question_per_page')));
        
        echo generate_radio_button_options(array(    'radio_name' => 'remedial_content',
                                                    'radio_label_N' => _AT('remedial_content_hide'),
                                                    'radio_label_Y' => _AT('remedial_content_show')));
    ?>

    <div class="row">
        <?php echo _AT('pass_score'); ?><br />
        <input type="radio" name="pass_score" value="0" id="no" <?php if ($_POST['passpercent'] == 0 && $_POST['passscore'] == 0){echo 'checked="checked"';} ?> 
        onfocus="ATutor.mods.tests.disable_elements('both');" onclick="this.onfocus();" />

        <label for="no" title="<?php echo _AT('pass_score'). ': '. _AT('no_pass_score');  ?>"><?php echo _AT('no_pass_score'); ?></label><br />

        <input type="radio" name="pass_score" value="1" id="percentage"  <?php if ($_POST['passpercent'] <> 0){echo 'checked="checked"';} ?>
        onfocus="ATutor.mods.tests.disable_elements('points');" onclick="this.onfocus();" />

        <input type="text" name="passpercent" id="passpercent" size="2" value="<?php echo $_POST['passpercent']; ?>" 
         <?php if ($_POST['passpercent'] == 0){echo 'disabled="disabled"';} ?> /> 
        <label for="percentage" title="<?php echo _AT('pass_score'). ': '. _AT('percentage_score');  ?>"><?php  echo '% ' . _AT('percentage_score'); ?></label><br />

        <input type="radio" name="pass_score" value="2" id="points"  <?php if ($_POST['passscore'] <> 0){echo 'checked="checked"';} ?>
        onfocus="ATutor.mods.tests.disable_elements('percentage');" onclick="this.onfocus();" />

        <input type="text" name="passscore" id="passscore" size="2" value="<?php echo $_POST['passscore']; ?>" 
         <?php if ($_POST['passscore'] == 0){echo 'disabled="disabled"';} ?>/> 
        <label for="points" title="<?php echo _AT('pass_score'). ': '. _AT('points_score');  ?>"><?php  echo _AT('points_score'); ?></label>
    </div>

    <div class="row">
        <label for="passfeedback"><?php echo _AT('pass_feedback'); ?></label><br />
        <textarea name="passfeedback" cols="35" rows="1" id="passfeedback"><?php echo htmlspecialchars($_POST['passfeedback']); ?></textarea>
    </div>

    <div class="row">
        <label for="failfeedback"><?php echo _AT('fail_feedback'); ?></label><br />
        <textarea name="failfeedback" cols="35" rows="1" id="failfeedback"><?php echo htmlspecialchars($_POST['failfeedback']); ?></textarea>
    </div>

    <div class="row">
        <?php echo _AT('result_release'); ?><br />
        <?php 
            if ($_POST['result_release'] == AT_RELEASE_IMMEDIATE) {
                $check_marked = $check_never = '';
                $check_immediate = 'checked="checked"';
        
            } else if ($_POST['result_release'] == AT_RELEASE_MARKED) {
                $check_immediate = $check_never = '';
                $check_marked = 'checked="checked"';

            } else if ($_POST['result_release'] == AT_RELEASE_NEVER) {
                $check_immediate = $check_marked = '';
                $check_never = 'checked="checked"';
            }
        ?>
        <input type="radio" name="result_release" id="release1" value="<?php echo AT_RELEASE_IMMEDIATE; ?>" <?php echo $check_immediate; ?> /><label for="release1"><?php echo _AT('release_immediate'); ?></label><br />
        <input type="radio" name="result_release" id="release2" value="<?php echo AT_RELEASE_MARKED; ?>" <?php echo $check_marked; ?> /><label for="release2"><?php echo _AT('release_marked'); ?></label><br />
        <input type="radio" name="result_release" id="release3" value="<?php echo AT_RELEASE_NEVER; ?>" <?php echo $check_never; ?>/><label for="release3"><?php echo _AT('release_never'); ?></label>
    </div>

    <div class="row">
        <?php echo _AT('randomize_questions'); ?><br />
        <?php 
            if ($_POST['random'] == 1) {
                $y = 'checked="checked"';
                $n = $disabled = '';
            } else {
                $y = '';
                $n = 'checked="checked"';
                $disabled = 'disabled="disabled" ';
            }
        ?>
        <input type="radio" name="random" id="random" value="0" checked="checked" onfocus="ATutor.mods.tests.disable_elements('num_questions', true);" onclick="this.onfocus();" /><label for="random"><?php echo _AT('no'); ?></label>. <input type="radio" name="random" value="1" id="ry" onfocus="ATutor.mods.tests.disable_elements('num_questions', false);" onclick="this.onfocus();" <?php echo $y; ?> /><label for="ry"><?php echo _AT('yes'); ?></label>, <input type="text" name="num_questions" id="num_questions" size="2" value="<?php echo $_POST['num_questions']; ?>" <?php echo $disabled . $n; ?> /> <label for="num_questions"><?php echo _AT('num_questions_per_test'); ?></label>
    </div>


    <div class="row">
        <?php echo _AT('start_date');  ?><br />
        <?php
            if (!isset($_POST['submit'])) {
                $today_day  = date('d');
                $today_mon  = date('m');
                $today_year = date('Y');
                $today_hour = date('H');
                $today_min  = 0;
            } else {
                $today_day  = intval($day_start);
                $today_mon  = intval($month_start);
                $today_year = intval($year_start);
                $today_hour = intval($hour_start);
                $today_min  = intval($min_start);
            }

            $name = '_start';
            require(AT_INCLUDE_PATH.'html/release_date.inc.php');

        ?>
    </div>

    <div class="row">
        <?php echo _AT('end_date');  ?><br />
        <?php
            if (!isset($_POST['submit'])) {
                $today_day  = date('d');
                $today_mon  = date('m');
                $today_year = date('Y');
                $today_hour = date('H');
                $today_min  = 0;
            } else {
                $today_day  = intval($day_end);
                $today_mon  = intval($month_end);
                $today_year = intval($year_end);
                $today_hour = intval($hour_end);
                $today_min  = intval($min_end);
            }
                    
            $name = '_end';
            require(AT_INCLUDE_PATH.'html/release_date.inc.php');
        ?>
    </div>

    <div class="row">
        <?php echo _AT('limit_to_group'); ?><br />
        <?php
            //show groups
            $sql    = "SELECT * FROM %sgroups_types WHERE course_id=%d ORDER BY title";
            $rows_groups = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
            
            if(count($rows_groups) > 0){
                foreach($rows_groups as $row){
                    echo '<strong>'.$row['title'].'</strong><br />';

                    $sql    = "SELECT * FROM %sgroups WHERE type_id=%d ORDER BY title";
                    $g_result = queryDB($sql, array(TABLE_PREFIX, $row['type_id']));
                    
                    foreach($g_result as $grow){
                        echo '&nbsp;<label><input type="checkbox" value="'.$grow['group_id'].'" name="groups['.$grow['group_id'].']" '; 
                        if (is_array($current_groups) && in_array($grow['group_id'], $current_groups)) {
                            echo 'checked="checked"';
                        }
                        echo '/>'.$grow['title'].'</label><br />';
                    }
                }
            } else {
                echo _AT('none_found');
            }
        ?>
    </div>

    <div class="row">
        <label for="inst"><?php echo _AT('instructions'); ?></label><br />
        <textarea name="instructions" cols="35" rows="3" id="inst"><?php echo htmlspecialchars($_POST['instructions']); ?></textarea>
    </div>

    <div class="row buttons">
        <input type="submit" value="<?php echo _AT('save'); ?>" name="submit" accesskey="s" />
        <input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
    </div>
    </fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>