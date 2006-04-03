<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	require (AT_INCLUDE_PATH.'html/frameset/header.inc.php'); 
	$msg->printErrors('TLE_ACCESS_DENIED');
	require (AT_INCLUDE_PATH.'html/frameset/footer.inc.php'); 
	exit;
}

if (isset($_POST['submit'])) {
	$cid = intval($_POST['cid']);
	$_POST['name'] = stripslashes($addslashes($_POST['name']));
	$_POST['summary'] = stripslashes($addslashes($_POST['summary']));
	$_POST['reference'] = stripslashes($addslashes($_POST['reference']));

	$body = '<p>'.nl2br($_POST['summary']).'</p>';
	$body .= '<p><a href="'.$_POST['reference'].'" target="_new">Open in New Window</a></p>';

	$title = addslashes($_POST['name']);
	$body = addslashes($body);

	$sql	= "SELECT MAX(ordering) AS ordering FROM ".TABLE_PREFIX."content WHERE course_id=$_SESSION[course_id] AND content_parent_id=$cid";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);
	$order  = intval($row['ordering']) + 1;

	$sql= 'INSERT INTO '.TABLE_PREFIX.'content VALUES 
				(0,	'
				.$_SESSION['course_id'].','															
				.$cid.','		
				.$order.', NOW(),0,1,NOW(),"","", "'.$title.'", "'.$body.'", 0)';

	$result = mysql_query($sql, $db);
	header('Location: '.$_base_href.'index.php');
	exit;
}


$_REQUEST['framed'] = TRUE;
require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_main_menu)) {
	$_main_menu = $contentManager->getContent();
}

function print_menu_sections(&$menu, $parent_content_id = 0, $depth = 0, $ordering = '') {
	$my_children = $menu[$parent_content_id];
	$cid = $_GET['cid'];

	if (!is_array($my_children)) {
		return;
	}
	foreach ($my_children as $children) {
		echo '<option value="'.$children['content_id'].'"';
		if ($cid == $children['content_id']) {
			echo ' selected="selected"';
		}
		echo '>';
		echo str_pad('', $depth, '-') . ' ';
		if ($parent_content_id == 0) {
			$new_ordering = $children['ordering'];
			echo $children['ordering'];
		} else {
			$new_ordering = $ordering.'.'.$children['ordering'];
			echo $ordering . '.'. $children['ordering'];
		}
		echo ' '.$children['title'].'</option>';

		print_menu_sections($menu, $children['content_id'], $depth+1, $new_ordering);
	}
}


?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="_top">
	<input type="hidden" name="name" value="<?php echo htmlentities(stripslashes($addslashes($_GET['tlename']))); ?>" />
	<input type="hidden" name="summary" value="<?php echo htmlentities(stripslashes($addslashes($_GET['tledescription']))); ?>" />
	<input type="hidden" name="reference" value="<?php echo htmlentities(stripslashes($addslashes($_GET['tleurl']))); ?>?preview=true" />

	<div class="input-form">
		<div class="row">
			<?php echo _AT('tile_import_content_package_about'); ?>
		</div>

		<div class="row">
		<strong><?php echo _AT('import_content_package_where'); ?>:</strong> <select name="cid">
								<option value="0"><?php echo _AT('import_content_package_bottom_subcontent'); ?></option>
								<option>--------------------------</option>
								<?php
									print_menu_sections($_main_menu);
								?>
								</select>
		</div>

		<div class="row">
			<strong><?php echo _AT('import_content_package'); ?>:</strong> <?php echo htmlentities(stripslashes($addslashes($_GET['tlename']))); ?>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" />
		</div>
	</div>
</form>

</body>
</html>