<?php

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFY);

$certify_id = '';
if (isset($_POST['certify_id'])) {
    $certify_id = $addslashes($_POST['certify_id']);
} else if (isset($_GET['certify_id'])) {
    $certify_id = $addslashes($_GET['certify_id']);
}

if (isset($_POST['edit'])) { // Commit edit

	// STORE EDITS

	$certify_selected = $_POST['selected'];

	$update_remove = array();
	$update_add = array();

	$certify_tests = fetchTestList($certify_id);
	foreach ($certify_tests as $test_id => $test) {
		if ($test['selected'] and !isset($certify_selected[$test_id])) {
			$update_remove[] = $test_id;
		} else if (!$test['selected'] and isset($certify_selected[$test_id])) {
			$update_add[] = $test_id;
		}

	}

	if (count($update_add) > 0) {
		$sqlrows = array();
		foreach ($update_add as $testid) {
			$sqlrows[] = '('.$certify_id.",".$testid.')';
		}
		$sql = "INSERT INTO ".TABLE_PREFIX."certify_tests
				(certify_id, 
				 test_id) 
					VALUES ".implode(',',$sqlrows);
							
		$result = mysql_query($sql, $db) or die(mysql_error());
		write_to_log(AT_ADMIN_LOG_INSERT, 'certify', mysql_affected_rows($db), $sql);
	
	}

	if (count($update_remove) > 0) {
		$sql = "DELETE FROM ".TABLE_PREFIX."certify_tests
				WHERE certify_id = $certify_id
				AND test_id IN (".implode(",",$update_remove).")";
							
		$result = mysql_query($sql, $db) or die(mysql_error());
		write_to_log(AT_ADMIN_LOG_DELETE, 'certify', mysql_affected_rows($db), $sql);
	
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');


} else if (isset($_POST['cancel']) || strlen($certify_id) == 0) {
	
	// CANCEL
	
	// FIXME: Is really a "return" function - need a new text for the button?

	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;

}


// FETCH INFO FOR VIEW	

$certify_tests = fetchTestList($certify_id);

require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

?>

<fieldset>
<legend>
For instructor to edit certificate
</legend>
<form name="certifydetails" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="certify_id" value="<?php echo $certify_id; ?>">

<table class="data" summary="" rules="cols">

<thead>
<tr>
	<th scope="col"></th>
	<th scope="col"><?php echo _AT('certify_title'); ?></a></th>
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="2">
		<input type="submit" name="edit" value="<?php echo _AT('save'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
	<?php foreach ($certify_tests as $id => $test) { ?>
		<tr>
			<td><input type="checkbox" <?php if ($test['selected']) { echo 'checked="checked" '; } ?> name="selected[<?= $id ?>]" value="1"></td>
			
			<td><label for=""><?php echo $test['title']; ?></label></td>
			
		</tr>
	
	<?php } ?>
	<?php if (count($certify_tests)==0) { ?>
	
		<tr>
			<td colspan="2"><?php echo _AT('none_found'); ?></td>
		</tr>
	
	<?php } ?>

</tbody>

</table>

</form>
</fieldset>


<?php

require (AT_INCLUDE_PATH.'footer.inc.php');

function fetchTestList($certify_id) {
	global $db, $_SESSION;

	// Fetch all tests for course
	// FIXME: Need to filter out tests that doesn't have a pass criteria
	$sql =  "SELECT test_id, title FROM ".TABLE_PREFIX."tests WHERE course_id=".$_SESSION['course_id'];
	$result = mysql_query($sql, $db) or die(mysql_error() . $sql);
	
	$certify_tests = array();
	
	while( $row = mysql_fetch_assoc($result) ) {
		$this_test = array();
		$this_test['title'] = $row['title'];
		$this_test['selected'] = false;
		$certify_tests[$row['test_id']] = $this_test;
	}
	
	// Fetch associated tests
	$sql =  "SELECT test_id FROM ".TABLE_PREFIX."certify_tests ";
	$sql .= "WHERE ".TABLE_PREFIX."certify_tests.certify_id=".$certify_id;
	$result = mysql_query($sql, $db) or die(mysql_error() . $sql);
	
	while( $row = mysql_fetch_assoc($result) ) {
		$certify_tests[$row['test_id']]['selected'] = true;
	}
	return $certify_tests;

}

?>