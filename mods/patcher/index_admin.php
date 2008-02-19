<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD);
require (AT_INCLUDE_PATH.'header.inc.php');

function printNoAvailablePatchMsg()
{
	print ("<font color='red'><b>No available patch need to be installed.</b></font>");
}

function print_patch_row($patch_row, $id, $enable_radiotton)
{
?>
	<tr>
		<td><input type="radio" name="id" value="<?php echo $id; ?>" id="<?php echo $id; ?>" <?php if ($enable_radiotton) echo 'checked="true"'; else echo 'disabled="true"'; ?> /></td>
		<td><?php echo $patch_row["atutor_patch_id"]; ?></td>
		<td><?php echo $patch_row["sequence"]; ?></td>
		<td><?php echo $patch_row["description"]; ?></td>
		<td><?php if (!isset($patch_row['status'])) echo "Uninstalled"; else echo $patch_row["status"]; ?></td>
		<td><?php echo $patch_row["available_to"]; ?></td>
	</tr>
<?php
}

$skipFilesModified = false;

if ($_POST['yes'])  $skipFilesModified = true;

require_once('PatchListParser.class.php');

if (trim($_POST['who']) != '') $who = trim($_POST['who']);
elseif (trim($_REQUEST['who']) != '') $who = trim($_REQUEST['who']);
else $who = "public";

$patch_folder = 'http://update.atutor.ca/patch/' . str_replace('.', '_', VERSION) . '/';

$patch_list_xml = @file_get_contents($patch_folder . 'patch_list.xml');

if ($patch_list_xml === FALSE) 
{
	echo _AT('none_found');
}
else
{
	$patchListParser =& new PatchListParser();
	$patchListParser->parse($patch_list_xml);
	$patch_list_array = $patchListParser->getMySortedParsedArrayForVersion($who, VERSION);
}
// Installation process
if ($_POST['install'] || $_POST['yes'])
{
	if ($_POST['install']) $id=$_POST['id'];
	else $id = $_REQUEST['id'];
	
	// Only displays the patches that are not installed
	$sql = "select count(*) num_of_installed from ".TABLE_PREFIX."patches " .
	       "where atutor_patch_id = " . $id .
	       " and status = 'Installed'";

	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	if ($row["num_of_installed"] == 1)
	{
		$msg->addError('PATCH_ALREADY_INSTALLED');
	}
	else
	{
		$patchURL = $patch_folder . $patch_list_array[$id][patch_folder] . "/";
		$patch_xml = @file_get_contents($patchURL . 'patch.xml');
		
		if ($patch_xml === FALSE) 
		{
			$msg->addError('PATCH_XML_NOT_FOUND');
		}
		else
		{
			require_once('PatchParser.class.php');
			require_once('Patch.class.php');
			
			$patchParser =& new PatchParser();
			$patchParser->parse($patch_xml);

			$patch = & new Patch($patchParser->getParsedArray(), $patch_list_array[$id], $skipFilesModified);
			
			$patch->applyPatch();
		}
	}
}

$msg->printErrors();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="get_patch_form">
<div class="input-form">
	<div class="row">
		<label for="who"><?php echo _AT('name'); ?></label>
		<input type="text" name="who" id="who" size="80" value="<?php echo $who; ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('get_my_patch'); ?>" name="get_my_patch" />
	</div>
</div>

<div class="input-form">

<?php 
?>
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('atutor_patch_id');?></th>
		<th scope="col"><?php echo _AT('sequence');?></th>
		<th scope="col"><?php echo _AT('description');?></th>
		<th scope="col"><?php echo _AT('status');?></th>
		<th scope="col"><?php echo _AT('available_to');?></th>
	</tr>
</thead>
	
<tbody>
<?php 
$is_first_uninstalled = true;

// display installed patches
$sql = "select * from ".TABLE_PREFIX."patches " .
       "where applied_version = '" . VERSION . "' ".
       "order by sequence";

$result = mysql_query($sql, $db);
$num_of_patches = mysql_num_rows($result) + count($patch_list_array);

if ($num_of_patches == 0)
{
	echo _AT('none_found');
}
else
{
	while ($row = mysql_fetch_assoc($result))
	{
			if ($row['status'] == 'Reverted')
			{
				print_patch_row($row, $row['patches_id'], true);
				
				$is_first_uninstalled = false;
			}
			else
				print_patch_row($row, $row['patches_id'], false);
	}
	
	// display un-installed patches
	foreach ($patch_list_array as $row_num => $new_patch)
	{
		// Only displays the patches that are not installed
		$sql = "select count(*) num_of_installed from ".TABLE_PREFIX."patches " .
		       "where atutor_patch_id = " . $new_patch['atutor_patch_id'] .
		       " and status = 'Installed'";
	
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		
		if ($row["num_of_installed"] == 0)
		{
			$array_id = 0;
			if ($is_first_uninstalled)
			{
				print_patch_row($new_patch, $array_id++, true);
				
				$is_first_uninstalled = false;
			}
			else
				print_patch_row($new_patch, $array_id++, false);
		}
	}

?>
</tbody>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
	</td>
</tr>
</tfoot>

<?php 
}
?>
</table>

</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
