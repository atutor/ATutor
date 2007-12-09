<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT M.member_id, M.login, CONCAT(M.first_name, ' ', M.second_name, ' ', M.last_name) AS full_name
			FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."course_enrollment C 
			WHERE M.member_id=C.member_id AND C.course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);

$_GET['id'] = intval($_GET['id']);

?>
<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<div class="input-form">
	<div class="row">
		<label for="id"><?php echo _AT('select_member'); ?></label><br />
		<select name="id" id="id">
			<?php
				while ($row = mysql_fetch_assoc($result)) {
					$sender = get_display_name($row['member_id']);
					echo '<option value="'.$row['member_id'].'"';
					if ($row['member_id'] == $_GET['id']) {
						echo ' selected="selected"';
					}
					echo '>'.$sender.'</option>';
				}
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" />
	</div>
</div>
</form>

<?php if ($_GET['id']) : ?>
<?php
	$sql = "SELECT counter, content_id, SEC_TO_TIME(duration) AS total FROM ".TABLE_PREFIX."member_track WHERE member_id=$_GET[id] AND course_id=$_SESSION[course_id] ORDER BY counter DESC";
	$result = mysql_query($sql, $db);
?>
	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('page'); ?></th>
		<th scope="col"><?php echo _AT('visits'); ?></th>
		<th scope="col"><?php echo _AT('duration'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if ($row = mysql_fetch_assoc($result)): ?>
		<?php do { ?>
			<tr>
				<td><?php echo $contentManager->_menu_info[$row['content_id']]['title']; ?></td>
				<td><?php echo $row['counter']; ?></td>
				<td><?php echo $row['total']; ?></td>
			</tr>
		<?php } while ($row = mysql_fetch_assoc($result)); ?>
	<?php else: ?>
		<tr>
			<td colspan="3"><?php echo _AT('none_found'); ?></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>