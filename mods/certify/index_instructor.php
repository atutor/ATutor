<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFY);

if (isset($_POST['delete'], $_POST['certify_id'])) 
{
    // DELETE

	header('Location: certify_delete.php?certify_id='.$_POST['certify_id']);
	exit;

} 
else if (isset($_POST['edit'], $_POST['certify_id'])) 
{
    // EDIT

	header('Location: certify_certificate.php?certify_id='.$_POST['certify_id']);
	exit;
} 
else if (isset($_POST['tests'], $_POST['certify_id'])) 
{
    // TESTS
	header('Location: certify_tests.php?certify_id='.$_POST['certify_id']);
	exit;
} 
else if (isset($_POST['students'], $_POST['certify_id'])) 
{
    // STUDENTS
	header('Location: certify_student_status.php?certify_id='.$_POST['certify_id']);
	exit;
} 
else if (!empty($_POST) && !isset($_POST['certify_id'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

// Fetch all tests for course
$sql =  "SELECT certify_id, title, description FROM ".TABLE_PREFIX."certify WHERE course_id=".$_SESSION['course_id'];
$result = mysql_query($sql, $db) or die(mysql_error());

$certificates = array();

while( $row = mysql_fetch_assoc($result) ) {
    $certificates[] = $row;
}

// if there are no certificates, print meldingen under, else ikke
//$msg->printInfos('CERTIFY_NO_CERTIFICATES'); 

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="cols">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('certify_title'); ?></a></th>
	<th scope="col"><?php echo _AT('certify_description'); ?></a></th>
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="3">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		<input type="submit" name="tests" value="<?php echo _AT('certify_edit_tests'); ?>" />
		<input type="submit" name="students" value="<?php echo _AT('certify_student_status'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
	<?php foreach ($certificates as $certificate) { ?>
		<tr>
			<td><input type="radio" name="certify_id" value="<?php echo $certificate['certify_id']; ?>" id="" /></td>
			
			<td><label for=""><?php echo $certificate['title']; ?></label></td>			
			
			<td><?php echo $certificate['description']; ?></td>
		</tr>
	
	<?php } ?>
	<?php if (count($certificates)==0) { ?>

		<tr>
			<td colspan="3"><?php echo _AT('none_found'); ?></td>
		</tr>
	
	<?php } ?>

</tbody>
</table>
</form>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>