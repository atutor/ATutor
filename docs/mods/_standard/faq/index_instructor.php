<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index_instructor.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_FAQ);

if (isset($_GET['edit'], $_GET['item'])) {
	$item = intval($_GET['item']);
	if (substr($_GET['item'], -1) == 'q') {
		header('Location: edit_question.php?id=' . $item);
	} else {
		header('Location: edit_topic.php?id=' . $item);
	}
	exit;
} else if (isset($_GET['delete'], $_GET['item'])) {
	$item = intval($_GET['item']);

	if (substr($_GET['item'], -1) == 'q') {
		header('Location: delete_question.php?id=' . $item);
	} else {
		header('Location: delete_topic.php?id=' . $item);
	}
	exit;
} else if (!empty($_GET)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$counter = 1;
$sql	 = "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
$result  = mysql_query($sql, $db);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;">
<thead>
<tr>
	<th>&nbsp;</th>
	<th style="width: 100%;"><?php echo _AT('name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
				    <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<?php if ($row = mysql_fetch_assoc($result)) : ?>
<tbody>
		<?php do { ?>
			<tr onmousedown="document.form['t<?php echo $row['topic_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['topic_id']; ?>_0">
				<th style="border-top:1pt solid #e0e0e0;"><input type="radio" name="item" id="t<?php echo $row['topic_id']; ?>" value="<?php echo $row['topic_id']; ?>" /></th>
				<th style="border-top:1pt solid #e0e0e0;"><?php echo htmlentities_utf8($row['name']); ?></th>
			</tr>
			<?php 
				$entry_sql = "SELECT * FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$row[topic_id] ORDER BY question";
				$entry_result = mysql_query($entry_sql, $db);
			?>

			<?php if ($entry_row = mysql_fetch_assoc($entry_result)) : do { ?>
				<tr onmousedown="document.form['q<?php echo $entry_row['entry_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['topic_id']; ?>_<?php echo $entry_row['entry_id']; ?>">
					<td><input type="radio" name="item" id="q<?php echo $entry_row['entry_id']; ?>" value="<?php echo $entry_row['entry_id']; ?>q" /></td>
					<td><?php echo htmlentities_utf8($entry_row['question']); ?></td>
				</tr>
			<?php } while ($entry_row = mysql_fetch_assoc($entry_result)); else: ?>
				<tr>
					<td>&nbsp;</td>
					<td><?php echo _AT('no_questions'); ?></td>
				</tr>
			<?php endif; ?>
		<?php } while($row = mysql_fetch_assoc($result)); ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="2"><em><?php echo _AT('none_found'); ?></em></td>
	</tr>
<?php endif; ?>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');  ?>