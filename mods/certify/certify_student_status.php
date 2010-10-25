<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFY);

require 'certify_functions.php';


$_custom_css = $_base_path . 'mods/certify/certify.css'; // use a custom stylesheet

$GLOBALS['dout'] = '';

function dbug($out) {
	$GLOBALS['dout'] .= $out . "\n";
}

$certify_id = '';
if (isset($_POST['certify_id'])) {
    $certify_id = $addslashes($_POST['certify_id']);
} else if (isset($_GET['certify_id'])) {
    $certify_id = $addslashes($_GET['certify_id']);
}




$sql =  '
	SELECT '.TABLE_PREFIX.'members.*
	FROM '.TABLE_PREFIX.'course_enrollment
	INNER JOIN '.TABLE_PREFIX.'members ON '.TABLE_PREFIX.'members.member_id = '.TABLE_PREFIX.'course_enrollment.member_id
	WHERE '.TABLE_PREFIX.'course_enrollment.course_id = '.$_SESSION['course_id'].'
';


$result = mysql_query($sql, $db) or die(mysql_error());

dbug($sql);

$members = array();
while( $member = mysql_fetch_assoc($result) ) {
	$member['certificate'] = array();
	$member['certificate']['progress'] = getCertificateProgress($member['member_id'],$certify_id);
	$members[$member['member_id']] = $member;
}


function membercmp($a,$b) {

	if ($a['certificate']['progress'] == $b['certificate']['progress'])
		return 0;

	return ($a['certificate']['progress'] > $b['certificate']['progress']) ? -1 : 1;

}

usort($members, "membercmp");

// -- Certificate update code end --

dbug(var_export($members,true));


require (AT_INCLUDE_PATH.'header.inc.php');


?>



<code><pre>
<?php //echo $GLOBALS['dout']; ?>
</pre></code>








<table class="data" summary="" rules="cols">
<thead>
<tr>
	<!--th align="left">&nbsp;</th-->
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('full_name'); ?></th>
	<th scope="col"><?php echo _AT('mark'); ?></th>
</tr>
</thead>
<!--tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="Should there be a button here for viewing individual tests?" /></td>
</tr>
</tfoot-->
<tbody>
<?php if ($members): ?>
	<?php foreach ($members as &$member): ?>
		<tr>
			<!--td>&nbsp;</td-->
			<td><?php echo $member['login']; ?></td>
			<td><?php 
				if ($anonymous == 0 && $member['member_id']){
					echo AT_print(get_display_name($member['member_id']), 'members.full_name'); /*$member['full_name'] */ 
				} else {
					echo $guest_text; // no need in AT_print(): $guest_text is a trusted _AT() output
				}
				?></td>


			<td>				
				<div class="certify_bar-border">
					<div class="certify_bar-fill">
						<div class="certify_bar-bar" style="width: <?php echo floor($member['certificate']['progress']); ?>%;">
						<span class="test_<?php echo $member['certificate']['progress'] == 100 ? 'passed' : 'failed' ?>" >
						<?php echo floor($member['certificate']['progress']); ?>%
						</span>
						</div>
					</div>
				</div>
				
			</td>


		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>







<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>