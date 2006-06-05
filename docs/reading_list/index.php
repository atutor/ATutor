<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);

require (AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] ORDER BY date_start";
$result = mysql_query($sql, $db);
?>

<table class="data" style="width: 90%;">
<thead>
<tr>
	<th><?php echo _AT('rl_start');    ?></th>
	<th><?php echo _AT('rl_end');      ?></th>
	<th><?php echo _AT('title');       ?></th>
	<th><?php echo _AT('rl_required'); ?></th>
	<th><?php echo _AT('rl_comment');  ?></th>
</tr>
</thead>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)): ?>

		<?php do { ?>
	<tr>
			<?php // get the external resource using the resource ID from the reading
			$id = $row['resource_id'];
			$sql = "SELECT title, type, url FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$id";
			$resource_result = mysql_query($sql, $db);
			if ($resource_row = mysql_fetch_assoc($resource_result)){ 
			?>
				<tr>
				<td>
				<?php  if ($row['date_start'] == '0000-00-00'){
					echo _AT ('none');
				} else {
					echo AT_Date(_AT('rl_date_format'), $row['date_start'], AT_DATE_MYSQL_DATETIME);
				}?>
				</td>

				<td>
				<?php  if ($row['date_end'] == '0000-00-00'){
					echo _AT ('none');
				} else {
					echo AT_Date(_AT('rl_date_format'), $row['date_end'], AT_DATE_MYSQL_DATETIME);
				}?>
				</td>

				<td><a href="reading_list/display_resource.php?id=<?php echo $id ?>" title="<?php echo _AT('rl_view_resource_details')?>" ><?php echo $resource_row['title']; ?></a>		
				</td>
				<td><?php echo _AT ($row['required']); ?></td>
				<td><?php echo $row['comment']; ?></td>
				</tr>

			<?php } ?>
	</tr>
		<?php } while($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="3"><em><?php echo _AT('none_found'); ?></em></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>