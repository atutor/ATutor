<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index_instructor.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php'); 

$include_javascript=true;
require("common.inc.php");

$sql	= "SELECT T.*, R.*, C.certificate_id, C.enable_download
				   FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."certificate C 
				  WHERE R.status=1 
				    AND R.member_id=$_SESSION[member_id] 
				    AND R.test_id=T.test_id 
				    AND T.course_id=$_SESSION[course_id] 
				    AND T.test_id = C.test_id
				    AND C.enable_download=1
				    ORDER BY R.date_taken DESC";
				    
$result	= mysql_query($sql, $db) or die(mysql_error());

while ($row = mysql_fetch_assoc($result))
{
	// if pass score is defined in table certificate, set passscore, passpercent
	if (!is_pass_score_defined_in_base_table())
	{
		$sql = "select passscore, passpercent from ".TABLE_PREFIX."certificate where test_id=".$row["test_id"];
		$result_certificate	= mysql_query($sql, $db) or die(mysql_error());
		$row_certificate = mysql_fetch_assoc($result_certificate);
		
		$row["passscore"] = $row_certificate["passscore"];
		$row["passpercent"] = $row_certificate["passpercent"];
	}

	// if pass score/percentage is not defined for issuing certificate, don't show rows for this test on the page
	if (($row["passscore"]==0 || $row["passscore"]=="") & ($row["passpercent"]==0 || $row["passpercent"]==""))
		continue;
	
	// if final score or out of is empty, don't show this row on the page
	if ($row['out_of'] == 0 || $row['final_score'] == '' || $row['result_release']==AT_RELEASE_NEVER)
		continue;
	else 
	{
		if ($row['random'])
			$out_of = get_random_outof($row['test_id'], $row['result_id']);
		else
			$out_of = $row['out_of'];
	}
	
	$pass_score = 0;
	
	if ($row["passpercent"] <> 0 & ($row["final_score"]/$out_of*100) >= $row["passpercent"])
		$pass_score = ($row["final_score"]/$out_of*100) . '%';
	if ($row["passscore"] <> 0 & $row["final_score"] >= $row["passscore"])
		$pass_score = $row["final_score"] . " / " . $out_of;

	if ($pass_score <> 0)
	{
		$rows[] = array("result_id"=>$row["result_id"],
										"certificate_id"=>$row["certificate_id"],
										"title"=>$row["title"],
										"mark"=>$pass_score,
										"date_taken"=>$row["date_taken"]);
	}
}

?>
&middot; <?php echo _AT("require_acrobat", "download"); ?><br><br>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 70%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('mark'); ?></th>
	<th scope="col"><?php echo _AT('date_taken'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<div class="row buttons">
		<input type="button" name="download" value="<?php echo _AT('download'); ?>" onClick="open_certificate_win('<?php echo dirname($_SERVER["PHP_SELF"])?>/open_certificate.php?result_id={radio_value}&certificate_id={hidden_value}', 'result_id', 'certificate_id')" />
		</div>
	</td>
</tr>
</tfoot>
<tbody>
<?php
if (!is_array($rows))
{
?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	foreach ($rows as $row)
	{
	?>
		<tr onmousedown="document.form['m<?php echo $row['result_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['result_id']; ?>">
			<td width="10"><input type="radio" name="result_id" value="<?php echo $row['result_id']; ?>" id="m<?php echo $row['result_id']; ?>" <?php if ($row['result_id']==$_POST['result_id']) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $row['certificate_id']; ?>"><?php echo $row['title']; ?></label></td>
			<td><?php echo $row['mark']; ?></td>
			<td><?php echo $row['date_taken']; ?></td>
			<input type="hidden" name="certificate_id" value="<?php echo $row['certificate_id']; ?>">
		</tr>
<?php 
	}
}
?>

</tbody>
</table>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>