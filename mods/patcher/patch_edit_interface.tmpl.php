<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: patch_edit_interface.tmpl.php 7208 2008-03-13 16:07:24Z cindy $

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form enctype="multipart/form-data" action='<?php echo $url; ?>' method="post" name="form" target="messageIFrame">

<div class="input-form">

<iframe id="messageIFrame" name="messageIFrame" src='' style='width:1px;height:1px;border:0' onload="show_message()"></iframe>
<div id="messageDIV"></div>
	
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="atutor_patch_id"><?php echo _AT('atutor_patch_id'); ?></label><br />
		<input id="atutor_patch_id" name="atutor_patch_id" type="text" maxlength="100" size="30" value="<?php echo $row_patches['atutor_patch_id']; ?>" /><br />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="atutor_version_to_apply"><?php echo _AT('atutor_version_to_apply'); ?></label><br />
		<input id="atutor_version_to_apply" name="atutor_version_to_apply" type="text" maxlength="100" size="30" value="<?php echo $row_patches['applied_version']; ?>" /><br />
	</div>

	<div class="row">
		<label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea id="description" name="description" cols="40" rows="4"><?php echo $row_patches['description']; ?></textarea><br />
	</div>

	<div class="row">
		<label for="sql_statement"><?php echo _AT('sql_statement'); ?></label><br />
		<textarea id="sql_statement" name="sql_statement" cols="40" rows="8"><?php echo $row_patches['sql_statement']; ?></textarea><br />
	</div>

	<div class="row">
		<label for="dependent_patches"><?php echo _AT('dependent_patches'); ?></label><br />
	</div>

	<div class="row">
		<table id="dependent_patches" class="data" rules="cols" align="left" style="width: 50%;">
		<thead>
		<tr>
			<th scope="col"><?php echo _AT('dependent_patch_id'); ?></th>
		</tr>
		</thead>

		<tbody>
<?php
// when edit existing patch
if ($result_patch_dependent)  
{
	$num_of_dependents = mysql_num_rows($result_patch_dependent);
	while ($row_patch_dependent = mysql_fetch_assoc($result_patch_dependent))
	{
	?>
			<tr>
				<td><input id="dependent_patch" name="dependent_patch[]" value="<?php echo $row_patch_dependent['dependent_patch_id']; ?>" type="text" maxlength="100" size="100" style="max-width:100%; display:block" /></td>
			</tr>
	<?php
	}
}

// when creating new patch
if ($num_of_dependents == 0)
{
?>
		<tr>
			<td><input id="dependent_patch" name="dependent_patch[]" type="text" maxlength="100" size="100" style="max-width:100%; display:block" /></td>
		</tr>
		</tbody>
<?php
}
?>
	
		<tfoot>
		<tr>
			<td colspan="4">
				<div class="buttons"  style="float:left">
					<input type="button" name="add_dependent_patch" value="<?php echo _AT('add_dependent_patch'); ?>" onclick="add_dependent()" />
				</div>
			</td>
		</tr>
		</tfoot>

		</table>
	</div>
	
	<br /><br /><br /><br /><br /><br />
	<div class="row">
		<label for="filesDiv"><?php echo _AT('files'); ?></label><br />
		<small>&middot; <?php echo _AT('relative_directory'); ?></small>
	</div>

	<div id="filesDiv" class="row">
<?php
// when edit existing patch
$num_of_files = 0;

if ($result_patch_files)  
{
	while ($row_patch_files = mysql_fetch_assoc($result_patch_files))
	{
?>
<div style="border-width:thin; border-style:solid; padding: 5px 5px 5px 5px; margin:5px 5px 5px 5px">
	<div style="float:left">Action: 
		<input type="radio" name="rb_action[<?php echo $num_of_files; ?>]" value="add" <?php if ($row_patch_files["action"] == "add") echo "checked" ?> onclick="show_content(event);" /><label for="add"><?php echo _AT("add"); ?></label>
		<input type="radio" name="rb_action[<?php echo $num_of_files; ?>]" value="alter" <?php if ($row_patch_files["action"] == "alter") echo "checked" ?> onclick="show_content(event);" /><label for="alter"><?php echo _AT("alter"); ?></label>
		<input type="radio" name="rb_action[<?php echo $num_of_files; ?>]" value="delete" <?php if ($row_patch_files["action"] == "delete") echo "checked" ?> onclick="show_content(event);" /><label for="delete"><?php echo _AT("delete"); ?></label>
		<input type="radio" name="rb_action[<?php echo $num_of_files; ?>]" value="overwrite" <?php if ($row_patch_files["action"] == "overwrite") echo "checked" ?> onclick="show_content(event);" /><label for="overwrite"><?php echo _AT("overwrite"); ?></label>
	</div>
	<br /><br />

	<div>
<?php 
		if ($row_patch_files["action"] == "add")
		{
?>
	<table width="100%">
		<tr>
			<td width="150px"><?php echo _AT("file_name"); ?></td>
			<td><input name="add_filename[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["name"]; ?>" type="text"  /></td>
		</tr>
		<tr>
			<td><?php echo _AT("directory"); ?></td>
			<td><input name="add_dir[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["location"]; ?>" type="text"  /></td>
		</tr>
		<tr>
			<td><?php echo _AT("upload_file"); ?></td>
			<td><INPUT TYPE="file" NAME="add_upload_file[<?php echo $num_of_files; ?>]" SIZE="40" style="max-width:100%" /></td>
		</tr>
	</table>
<?php 
		}
		
		if ($row_patch_files["action"] == "alter")
		{
?>
	<table width="100%">
		<tr>
			<td width="150px"><?php echo _AT("file_name"); ?></td>
			<td><input name="alter_filename[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["name"]; ?>" type="text" maxlength="100" size="100" /></td>
		</tr>
		<tr>
			<td><?php echo _AT("directory"); ?></td>
			<td><input name="alter_dir[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["location"]; ?>" type="text" maxlength="100" size="100" style="max-width:100%" /></td>
		</tr>
		<tr>
			<td><?php echo _AT("code_to_replace_from"); ?></td>
			<td><textarea name="alter_code_from[<?php echo $num_of_files; ?>]" rows="5" cols="120" style="max-width:100%"><?php echo $row_patch_files["code_from"]; ?></textarea></td>
		</tr>
		<tr>
			<td><?php echo _AT("code_to_replace_to"); ?></td>
			<td><textarea name="alter_code_to[<?php echo $num_of_files; ?>]" rows="5" cols="120" style="max-width:100%"><?php echo $row_patch_files["code_to"]; ?></textarea></td>
		</tr>
	</table>
<?php 
		}
		
		if ($row_patch_files["action"] == "delete")
		{
?>
 	<table width="100%">
 		<tr>
 			<td width="150px"><?php echo _AT("file_name"); ?></td>
 			<td><input name="delete_filename[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["name"]; ?>" type="text" maxlength="100" size="100" /></td>
 		</tr>
 		<tr>
 			<td><?php echo _AT("directory"); ?></td>
 			<td><input name="delete_dir[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["location"]; ?>" type="text" maxlength="100" size="100" /></td>
 		</tr>
 	</table>
<?php 
		}
		
		if ($row_patch_files["action"] == "overwrite")
		{
?>
	<table width="100%">
		<tr>
			<td width="150px"><?php echo _AT("file_name"); ?></td>
			<td><input name="overwrite_filename[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["name"]; ?>" type="text" /></td>
		</tr>
		<tr>
			<td><?php echo _AT("directory"); ?></td>
			<td><input name="overwrite_dir[<?php echo $num_of_files; ?>]" value="<?php echo $row_patch_files["location"]; ?>" type="text" maxlength="100" size="100" /></td>
		</tr>
		<tr>
			<td><?php echo _AT("upload_file"); ?></td>
			<td><INPUT TYPE="file" NAME="overwrite_upload_file[<?php echo $num_of_files; ?>]" SIZE="40" style="max-width:100%" /></td>
		</tr>
	</table>
<?php 
		}
?>
	</div>
	<div class="row buttons" style="float:left">
		<input type="button" value="<?php echo _AT("delete_this_file"); ?>" onClick="del_file(event)" />
	</div>
	<br /><br />
</div>
<?php
		
		$num_of_files++;
	}
}
?>

	</div>

	<div class="row buttons"  style="float:left">
		<input type="button" name="add_a_file" value="<?php echo _AT('add_a_file'); ?>" onClick="add_file()" />
	</div>
	
	<br /><br />
	
	<div class="row buttons">
		<input type="submit" name="submit" value=" <?php echo _AT('create_patch'); ?> " accesskey="c" />
	</div>

</div>
</form>

<script language="JavaScript" type="text/javascript">
//<!--

function show_message()
{
	var messageDIV = document.getElementById("messageDIV"); 
	var i = document.getElementById("messageIFrame"); 
	
  if (i.contentDocument) {
      var d = i.contentDocument;
  } else if (i.contentWindow) {
      var d = i.contentWindow.document;
  } else {
      var d = window.frames[id].document;
  }	
	messageDIV.innerHTML = d.body.innerHTML;
}

function add_dependent() {
  var dependentPatchesTable = document.getElementById("dependent_patches").tBodies[0];
  var dependentPatch = dependentPatchesTable.rows[dependentPatchesTable.rows.length - 1].cloneNode(true);
  dependentPatchesTable.appendChild(dependentPatch);
  dependentPatch.cells[0].firstChild.value='';
  
  var dependents = document.form["dependent_patch[]"];
  dependents[dependents.length - 1].focus();
    //	document.form['dependent_patch['+ pos +']'].focus();
}

num_of_files = <?php echo $num_of_files; ?>;

function add_file() {
	var newDiv = document.createElement("div");
	
	newDiv.innerHTML = ACTION_HTML_TEMPLATE.replace(/\{1\}/g, num_of_files);
	document.getElementById("filesDiv").appendChild(newDiv);
	
	document.form['rb_action[' +num_of_files +']'][0].focus();

	num_of_files++;
}

function del_file(evt) {
	var target =(evt.srcElement)?evt.srcElement:evt.currentTarget;
	var div =  target.parentNode.parentNode ;
	div.parentNode.removeChild(div);
}

function show_content(evt) {
	var target =(evt.srcElement)?evt.srcElement:evt.currentTarget;
	var tables = target.parentNode.parentNode.getElementsByTagName('TABLE');
	tables[0].style.display='none';
	tables[1].style.display='none';
	tables[2].style.display='none';
	tables[3].style.display='none';
	if(target.value == 'add') tables[0].style.display='';
	if(target.value == 'alter') tables[1].style.display='';
	if(target.value == 'delete') tables[2].style.display='';
	if(target.value == 'overwrite') tables[3].style.display='';
}

var ACTION_HTML_TEMPLATE = ' \
<div style="border-width:thin; border-style:solid; padding: 5px 5px 5px 5px; margin:5px 5px 5px 5px"> \
	<div style="float:left">Action:  \
		<input type="radio" name="rb_action[{1}]" value="add" checked onclick="show_content(event);" /><label for="add"><?php echo _AT("add"); ?></label> \
		<input type="radio" name="rb_action[{1}]" value="alter" onclick="show_content(event);" /><label for="alter"><?php echo _AT("alter"); ?></label> \
		<input type="radio" name="rb_action[{1}]" value="delete" onclick="show_content(event);" /><label for="delete"><?php echo _AT("delete"); ?></label> \
		<input type="radio" name="rb_action[{1}]" value="overwrite" onclick="show_content(event);" /><label for="overwrite"><?php echo _AT("overwrite"); ?></label> \
	</div> \
	<br /><br /> \
	<div> \
	<table style="display:" width="100%"> \
		<tr> \
			<td width="150px"><?php echo _AT("file_name"); ?></td> \
			<td><input name="add_filename[{1}]" type="text"  /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("directory"); ?></td> \
			<td><input name="add_dir[{1}]" type="text"  /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("upload_file"); ?></td> \
			<td><INPUT TYPE="file" NAME="add_upload_file[{1}]" SIZE="40" style="max-width:100%" /></td> \
		</tr> \
	</table> \
	<table style="display: none" width="100%"> \
		<tr> \
			<td width="150px"><?php echo _AT("file_name"); ?></td> \
			<td><input name="alter_filename[{1}]" type="text" maxlength="100" size="100" /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("directory"); ?></td> \
			<td><input name="alter_dir[{1}]" type="text" maxlength="100" size="100" style="max-width:100%" /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("code_to_replace_from"); ?></td> \
			<td><textarea name="alter_code_from[{1}]" rows="5" cols="120" style="max-width:100%"></textarea></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("code_to_replace_to"); ?></td> \
			<td><textarea name="alter_code_to[{1}]" rows="5" cols="120" style="max-width:100%"></textarea></td> \
		</tr> \
	</table> \
 	<table style="display: none" width="100%"> \
 		<tr> \
 			<td width="150px"><?php echo _AT("file_name"); ?></td> \
 			<td><input name="delete_filename[{1}]" type="text" maxlength="100" size="100" /></td> \
 		</tr> \
 		<tr> \
 			<td><?php echo _AT("directory"); ?></td> \
 			<td><input name="delete_dir[{1}]" type="text" maxlength="100" size="100" /></td> \
 		</tr> \
 	</table> \
	<table style="display: none" width="100%"> \
		<tr> \
			<td width="150px"><?php echo _AT("file_name"); ?></td> \
			<td><input name="overwrite_filename[{1}]" type="text" /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("directory"); ?></td> \
			<td><input name="overwrite_dir[{1}]" type="text" maxlength="100" size="100" /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("upload_file"); ?></td> \
			<td><INPUT TYPE="file" NAME="overwrite_upload_file[{1}]" SIZE="40" style="max-width:100%" /></td> \
		</tr> \
	</table> \
	</div> \
	<div class="row buttons" style="float:left"> \
		<input type="button" value="<?php echo _AT("delete_this_file"); ?>" onClick="del_file(event)" /> \
	</div> \
	<br /><br /> \
</div> \
';

//-->
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
