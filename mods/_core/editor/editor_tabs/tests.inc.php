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
if (!defined('AT_INCLUDE_PATH')) { exit; }
?>

<?php
/* Get the list of associated tests with this content on page load */

$_REQUEST['cid'] = intval($_REQUEST['cid']);	//uses request 'cause after 'saved', the cid will become $_GET.

$sql = "SELECT * FROM %scontent_tests_assoc WHERE content_id=%d";
$rows_test_assoc = queryDB($sql, array(TABLE_PREFIX, $_REQUEST['cid']));

foreach($rows_test_assoc as $row){
	$_POST['tid'][] = $row['test_id'];
}


/* get a list of all the tests we have, and links to create, edit, delete, preview */

$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue 
             FROM %stests 
            WHERE course_id=%d 
            ORDER BY start_date DESC";
$rows_tests	= queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
$num_tests = count($rows_tests);

//If there are no tests, don't display anything except a message
if ($num_tests == 0){
	$msg->addInfo('NO_TESTS');
	$msg->printInfos();
	return;
}

$i = 0;
foreach($rows_tests as $row){
	$results[$i]['test_id'] = $row['test_id'];
	$results[$i]['title'] = $row['title'];
	
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		$results[$i]['status'] = '<strong>'._AT('ongoing').'</strong>';
	} else if ($row['ue'] < time() ) {
		$results[$i]['status'] = '<strong>'._AT('expired').'</strong>';
	} else if ($row['us'] > time() ) {
		$results[$i]['status'] = '<strong>'._AT('pending').'</strong>';
	} 

	$startend_date_format=_AT('startend_date_format'); 

	$results[$i]['availability'] = AT_date($startend_date_format, $row['start_date'], AT_DATE_MYSQL_DATETIME). ' ' ._AT('to_2').' ';
	$results[$i]['availability'] .= AT_date($startend_date_format, $row['end_date'], AT_DATE_MYSQL_DATETIME);
	
	// get result release
	if ($row['result_release'] == AT_RELEASE_IMMEDIATE)
		$results[$i]['result_release'] = _AT('release_immediate');
	else if ($row['result_release'] == AT_RELEASE_MARKED)
		$results[$i]['result_release'] = _AT('release_marked');
	else if ($row['result_release'] == AT_RELEASE_NEVER)
		$results[$i]['result_release'] = _AT('release_never');
		
	//get # marked submissions
	$sql_sub = "SELECT COUNT(*) AS sub_cnt FROM %stests_results WHERE status=1 AND test_id=%d";
	$row_sub	= queryDB($sql_sub, array(TABLE_PREFIX, $row['test_id']), TRUE);	
	
	$results[$i]['submissions'] = $row_sub['sub_cnt'].' '._AT('submissions').', ';

	//get # submissions
	$sql_sub = "SELECT COUNT(*) AS marked_cnt FROM %stests_results WHERE status=1 AND test_id=%d AND final_score=''";
	$row_sub	= queryDB($sql_sub, array(TABLE_PREFIX, $row['test_id']), TRUE);

	$results[$i]['submissions'] .= $row_sub['marked_cnt'].' '._AT('unmarked');

	//get assigned groups
	$sql_sub = "SELECT G.title FROM %sgroups G INNER JOIN %stests_groups T USING (group_id) WHERE T.test_id=%d";
	$rows_group_tests = queryDB($sql_sub, array(TABLE_PREFIX, TABLE_PREFIX, $row['test_id']));
	
    if(count($rows_group_tests) == 0){
		$results[$i]['assign_to'] = _AT('everyone');
	} else {
		$results[$i]['assign_to'] = $row_sub['title'];
		foreach($rows_group_tests as $row_sub){
		    if($g == 0){
		        $results[$i]['assign_to'] .= $row_sub['title'];
		    }else{
		    	$results[$i]['assign_to'] .= ', '.$row_sub['title'];
		    }
		    $g++;
		}
	}
	
	if ($row['passscore'] == 0 && $row['passpercent'] == 0)
		$results[$i]['pass_score'] = _AT('no_pass_score');
	else if ($row['passscore'] <> 0)
		$results[$i]['pass_score'] = $row['passscore'];
	else if ($row['passpercent'] <> 0)
		$results[$i]['pass_score'] = $row['passpercent'].'%';
		
	$i++;
}
?>


<div class="row">
	<span style="font-weight:bold"><?php echo _AT('about_content_tests'); ?></span>
</div>

<input type="hidden" name="allow_test_export" id="disallow_test_export" value="0"/>

<!-- <div class="row">
This option has been commented out for the timebeing until the ability to export and view 
tests a part of a CP or CP is actually possible

	<?php
	//Need radio button 'cause one checkbox makes the states indeterministic
	//@harris
/*
	$test_export_y_checked = '';
	$test_export_n_checked = '';
	if ($_POST['allow_test_export'] == 1){
		$test_export_y_checked = ' checked="checked"';
	} else {
		$test_export_n_checked = ' checked="checked"';
	}
	
	echo _AT('allow_test_export');
*/
?>

	<input type="radio" name="allow_test_export" id="allow_test_export" value="1" <?php //echo $test_export_y_checked; ?>/>
	<label for="allow_test_export"><?php //echo _AT('yes'); ?></label>
	<input type="radio" name="allow_test_export" id="disallow_test_export" value="0" <?php //echo $test_export_n_checked; ?>/>
	<label for="disallow_test_export"><?php //echo _AT('no'); ?></label>
</div>
-->

<div class="row">
	<p><?php echo _AT('custom_test_message'); ?></p>
	<textarea name="test_message" cols="80" rows="4" class="test_message"><?php echo htmlspecialchars($_POST['test_message'], ENT_QUOTES); ?></textarea>
</div>

<?php print_test_table($results, $_POST['tid']);?>

<?php 
// display pre-tests

$sql = "SELECT * FROM %scontent_prerequisites WHERE content_id=%d AND type='%s'";
$rows_prereqs = queryDB($sql, array(TABLE_PREFIX, $_REQUEST['cid'], CONTENT_PRE_TEST));

foreach($rows_prereqs as $row){
	$_POST['pre_tid'][] = $row['item_id'];
}

?>
<div class="row">
	<p style="float:left;"><span style="font-weight:bold;"><?php echo _AT('define_pretest'); ?></span><br />
	<small>&middot; <?php echo _AT('about_pretest'); ?></small><br />
	<?php echo _AT('applies_to_all_sub_pages');?></p>
</div>

<?php print_test_table($results, $_POST['pre_tid'], 'pre_');?>

<?php function print_test_table($results, $post_tids, $id_prefix='') {?>
	<div>
	<table class="data" summary="" style="width: 90%">
	<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('title');          ?></th>
		<th scope="col"><?php echo _AT('status');         ?></th>
		<th scope="col"><?php echo _AT('availability');   ?></th>
		<th scope="col"><?php echo _AT('result_release'); ?></th>
		<th scope="col"><?php echo _AT('submissions');	  ?></th>
		<th scope="col"><?php echo _AT('pass_score');	  ?></th>
		<th scope="col"><?php echo _AT('assigned_to');	  ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($results as $row) { ?>
	<?php
		$checkMe = '';
		if (is_array($post_tids) && in_array($row['test_id'], $post_tids)){
			$checkMe = ' checked="checked"';
		} 
	?>
	<tr onmousedown="toggleTestSelect('<?php echo $id_prefix; ?>r_<?php echo $row['test_id']; ?>');rowselect(this);" id="<?php echo $id_prefix; ?>r_<?php echo $row['test_id']; ?>">
		<td><input type="checkbox" name="<?php echo $id_prefix; ?>tid[]" value="<?php echo $row['test_id']; ?>" id="<?php echo $id_prefix; ?>t<?php echo $row['test_id']; ?>" <?php echo $checkMe; ?> onmouseup="this.checked=!this.checked" /></td>
		<td><?php echo $row['title']; ?></td>
		<td><?php echo $row['status']; ?></td>
		<td><?php echo $row['availability']; ?></td>
		<td><?php echo $row['result_release']; ?></td>
		<td><?php echo $row['submissions']; ?></td>
		<td><?php echo $row['pass_score']; ?></td>
		<td><?php echo $row['assign_to']; ?></td>
	</tr>
	<?php } ?>
	</tbody>
	</table>
	</div>
	<br />
<?php }?>

<script type="text/javascript">
	function toggleTestSelect(r_id){
		var row = document.getElementById(r_id);
		var checkBox = row.cells[0].firstChild;

		if (checkBox.checked == true){
			checkBox.checked = false;
		} else {
			checkBox.checked = true;
		}
	}
</script>
