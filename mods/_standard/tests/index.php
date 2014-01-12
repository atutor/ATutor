<?php
/************************************************************************/
/* ATutor																*/
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
authenticate(AT_PRIV_TESTS);
tool_origin();

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_test.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['preview'], $_GET['id'])) {
	header('Location: preview.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['questions'], $_GET['id'])) {
	header('Location: questions.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['submissions'], $_GET['id'])) {
	header('Location: results.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['statistics'], $_GET['id'])) {
	header('Location: results_all_quest.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: delete_test.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['export'], $_GET['id'])){
	header('Location: export_test.php?tid='.$_GET['id']);
} else if (isset($_GET['edit']) 
		|| isset($_GET['preview']) 
		|| isset($_GET['questions']) 
		|| isset($_GET['submissions']) 
		|| isset($_GET['statistics']) 
		|| isset($_GET['delete'])
		|| isset($_GET['export'])) {

	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

//Check if any test exists in this course, display help if none.
$sql = "SELECT count(*) as test_count FROM %stests WHERE course_id=%d";
$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);

if($row['test_count'] == 0){
    $msg->printInfos('CREATE_TESTS');
}
/* get a list of all the tests we have, and links to create, edit, delete, preview */
$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue FROM %stests WHERE course_id=%d ORDER BY start_date DESC";
$rows_tests	= queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'] ));
$num_tests = count($rows_tests);

$cols=6;
?>
<form method="post" action="mods/_standard/tests/import_test.php" enctype="multipart/form-data" >
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('import_test'); ?></legend>
	<div class="row">
		<label for="to_file"><?php echo _AT('upload_test'); ?></label><br />
		<input type="file" name="file" id="to_file" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit_import" value="<?php echo _AT('import'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title');          ?></th>
	<th scope="col" class="hidecol700"><?php echo _AT('status');         ?></th>
	<th scope="col"><?php echo _AT('availability');   ?></th>
	<th scope="col"class="hidecol700"><?php echo _AT('result_release'); ?></th>
	<th scope="col"><?php echo _AT('submissions');	  ?></th>
	<th scope="col"class="hidecol480"><?php echo _AT('assigned_to');	  ?></th>
</tr>
</thead>

<?php if ($num_tests > 0):
//if ($num_tests): ?>
	<tfoot>
	<tr>
		<td colspan="7">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
			<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
			<input type="submit" name="questions" value="<?php echo _AT('questions'); ?>" />
		</td>
	</tr>
	<tr>	
		<td colspan="7" style="padding-left:38px;">
			<input type="submit" name="submissions" value="<?php echo _AT('submissions'); ?>" />
			<input type="submit" name="statistics" value="<?php echo _AT('statistics'); ?>" />
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>

	<?php 
	foreach($rows_tests as $row){ ?>
		<tr onmousedown="document.form['t<?php echo $row['test_id']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['test_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $row['test_id']; ?>" id="t<?php echo $row['test_id']; ?>" /></td>
			<td><label for="t<?php echo $row['test_id']; ?>"><?php echo $row['title']; ?></label></td>
			<td  class="hidecol700"><?php
				if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
					echo '<strong>'._AT('ongoing').'</strong>';
				} else if ($row['ue'] < time() ) {
					echo '<strong>'._AT('expired').'</strong>';
				} else if ($row['us'] > time() ) {
					echo '<strong>'._AT('pending').'</strong>';
				} ?></td>
			<td><?php $startend_date_format=_AT('startend_date_format'); 

				echo AT_date($startend_date_format, $row['start_date'], AT_DATE_MYSQL_DATETIME). ' ' ._AT('to_2').' ';
				echo AT_date($startend_date_format, $row['end_date'], AT_DATE_MYSQL_DATETIME); ?></td>

			<td  class="hidecol700"><?php 
				if ($row['result_release'] == AT_RELEASE_IMMEDIATE) {
					echo _AT('release_immediate');
				} else if ($row['result_release'] == AT_RELEASE_MARKED) {
					echo _AT('release_marked');
				} else if ($row['result_release'] == AT_RELEASE_NEVER) {
					echo _AT('release_never');
				}
			?></td>
			<td><?php
				//get # marked submissions
				$sql_sub = "SELECT COUNT(*) AS sub_cnt FROM %stests_results WHERE status=1 AND test_id=%d";
				$row_sub = queryDB($sql_sub, array(TABLE_PREFIX, $row['test_id']), TRUE);

				echo $row_sub['sub_cnt'].' '._AT('submissions').', ';

				//get # submissions
				$sql_sub = "SELECT COUNT(*) AS marked_cnt FROM %stests_results WHERE status=1 AND test_id=%d AND final_score=''";
				$row_sub = queryDB($sql_sub, array(TABLE_PREFIX, $row['test_id']), TRUE);

				echo $row_sub['marked_cnt'].' '._AT('unmarked');
				?>
			</td>
			<td  class="hidecol480"><?php
				//get assigned groups
				$sql_sub = "SELECT G.title FROM %sgroups G INNER JOIN %stests_groups T USING (group_id) WHERE T.test_id=%d";
				$rows_groups	= queryDB($sql_sub, array(TABLE_PREFIX, TABLE_PREFIX, $row['test_id']));

                if(count($rows_groups) == 0){
					echo _AT('everyone');
				} else {
					$assigned_groups = '';
					foreach($rows_groups as $row_groups){
						$assigned_groups .= $row_groups['title'].', ';
					}
					echo substr($assigned_groups, 0, -2);
				}				
				?>
			</td>
		</tr>
	<?php } ?>
<?php else: ?>
	<tbody>
	<tr>
		<td colspan="7"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>