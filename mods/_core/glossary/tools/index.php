<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);


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
$sql	= "SELECT * FROM %sglossary WHERE course_id=%d ORDER BY word";			
$rows_terms= queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

$gloss_results = array();
foreach($rows_terms as $row){
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

if($num_pages > 1){
	?>
	<div class="paging">
		<ul>
		<?php for ($i=1; $i<=$num_pages; $i++): ?>
			<li>
				<?php if ($i == $page) : ?>
					<a class="current" href="<?php echo 'mods/_core/glossary/tools/index.php?p='.$i.'#list'; ?>"><strong><?php echo $i; ?></strong></a>
				<?php else: ?>
					<a href="<?php echo 'mods/_core/glossary/tools/index.php?p='.$i.'#list'; ?>"><?php echo $i; ?></a>
				<?php endif; ?>
			</li>
		<?php endfor; ?>
		</ul>
	</div>
	<?php } 

if(!empty($gloss_results)) {
	foreach ($gloss_results as $row) {	
		//get related term name
		$related_word = '';
		if ($row['related_word_id']) {
			$sql	= "SELECT word FROM %sglossary WHERE word_id=%d AND course_id=%d";
			$row_related = queryDB($sql, array(TABLE_PREFIX, $row['related_word_id'], $_SESSION['course_id']), TRUE);
			if(count($row_related) != 0){
				$row['related_word'] = $row_related['word'];			
			}
		}

		$def_trunc = validate_length($row['definition'], 70, VALIDATE_LENGTH_FOR_DISPLAY);
		$gloss_results_row[] = $row;
	}
}
$savant->assign('gloss_results_row', $gloss_results_row);
$savant->assign('related_word', $related_word);
$savant->assign('def_trunc', $def_trunc);	

$savant->display('instructor/glossary/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>