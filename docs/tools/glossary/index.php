<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);

require (AT_INCLUDE_PATH.'lib/links.inc.php');

if (isset($_POST['edit'], $_POST['word_id'])) {
	header('Location: edit.php?gid='.$_POST['word_id']);
	exit;
} else if (isset($_POST['delete'], $_POST['word_id'])) {
	header('Location: delete.php?gid='.$_POST['word_id']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

//get terms
$sql	= "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id=$_SESSION[course_id] ORDER BY word";			
$result= mysql_query($sql, $db);

//if(mysql_num_rows($result) > 0) {		

	$gloss_results = array();
	while ($row = mysql_fetch_assoc($result)) {
		$gloss_results[] = $row;
	}
	$num_results = count($gloss_results);
	$results_per_page = 25;
	$num_pages = ceil($num_results / $results_per_page);
	$page = intval($_GET['p']);
	if (!$page) {
		$page = 1;
	}
	
	$count = (($page-1) * $results_per_page) + 1;
	$gloss_results = array_slice($gloss_results, ($page-1)*$results_per_page, $results_per_page);
	
	if($num_pages > 1) {
		echo _AT('page').': ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<strong>'.$i.'</strong>';
			} else {
				echo ' | <a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'#list">'.$i.'</a>';
			}
		}
	}
?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols" style="width: 90%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('glossary_term'); ?></th>
	<th scope="col"><?php echo _AT('glossary_definition'); ?></th>
	<th scope="col"><?php echo _AT('glossary_related'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php
if(!empty($gloss_results)) {
	foreach ($gloss_results as $row) {	
		//get related term name
		$related_word = '';
		if ($row['related_word_id']) {
			$sql	= "SELECT word FROM ".TABLE_PREFIX."glossary WHERE word_id=".$row['related_word_id']." AND course_id=".$_SESSION['course_id'];
			$result = mysql_query($sql, $db);
			if ($row_related = mysql_fetch_array($result)) {
				$related_word = $row_related['word'];			
			}
		}

		$def_trunc = substr($row['definition'], 0, 70);
		if (strlen($def_trunc) < strlen($row['definition'])) {
			$def_trunc .= ' &#8230;';
		}
	?>
			<tr onmousedown="document.form['m<?php echo $row['word_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['word_id']; ?>">
				<td valign="top" width="10"><input type="radio" name="word_id" value="<?php echo $row['word_id']; ?>" id="m<?php echo $row['word_id']; ?>" /></td>
				<td valign="top"><label for="m<?php echo $row['word_id']; ?>"><?php echo AT_print($row['word'],	'glossary.word'); ?></label></td>
				<td style="whitespace:nowrap;"><?php echo AT_print($def_trunc,		'glossary.definition'); ?></td>
				<td valign="top"><?php echo AT_print($related_word,	'glossary.word'); ?></td>
			</tr>
<?php 
	} 				
} else {
?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php
}					
?>

</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>