<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: patch_edit_interface.tmpl.php 7208 2008-03-13 16:07:24Z cindy $

require ('include/json.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form enctype="multipart/form-data" action='<?php echo $url; ?>' method="post" name="form" target="messageIFrame">

<div class="input-form">

<iframe id="messageIFrame" name="messageIFrame" src='' style='width:1px;height:1px;border:0' onload="show_message()"></iframe>
<div id="messageDIV"></div>
	
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="atutor_patch_id"><?php echo _AT('atutor_patch_id'); ?></label><br />
		<small>&middot; <?php echo _AT('contain_only'); ?></small><br />
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
	</div>

	<div class="row buttons"  style="float:left">
		<input type="button" name="add_a_file" value="<?php echo _AT('add_a_file'); ?>" onclick="add_file()" />
	</div>
	
	<br /><br />
	
	<div class="row buttons">
		<input type="submit" name="create" value=" <?php echo _AT('create_patch'); ?> " accesskey="c" />
		<input type="submit" name="save" value=" <?php echo _AT('save'); ?> " accesskey="s" onclick="document.form.target=''; "/>
		<input type="button" name="cancel" value=" <?php echo _AT('cancel'); ?> " onclick="location.href='mods/_standard/patcher/myown_patches.php'" />
	</div>

</div>
</form>

<script language="JavaScript" type="text/javascript">
//<!--

myescape = function(/*string*/ str) {
    return str.replace(/(['"\.*+?^${}()|[\]\/\\])/g, "\\$1").replace(/\n/g, '\\n');
}

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

var num_of_files = 0;
function add_file(filedata) {
	var newDiv = document.createElement("div");
	
	newDiv.innerHTML = ACTION_HTML_TEMPLATE.replace(/\{1\}/g, num_of_files);
	document.getElementById("filesDiv").appendChild(newDiv);
	
	document.form['rb_action[' +num_of_files +']'][0].focus();
	
	if(filedata) {
		var srcElement = null;

		if(filedata.action=="add") {
			// set focus on radio button "add"
			document.form['rb_action[' +num_of_files +']'][0].checked = true;
			document.form['rb_action[' +num_of_files +']'][0].focus();

			// set value
			srcElement = document.form['rb_action[' +num_of_files +']'][0];
			document.form['add_filename[' +num_of_files +']'].value = filedata.name;
			document.form['add_dir[' +num_of_files +']'].value = filedata.location;
			
			// set uploaded file
			if (filedata.uploaded_file != "")
			{
				document.form['add_uploaded_file[' +num_of_files +']'].value = filedata.uploaded_file;
				var tables = newDiv.getElementsByTagName('TABLE');
				tables[0].rows[2].style.display='';    // display the row of uploaded file
				tables[0].rows[2].cells[1].innerHTML=filedata.uploaded_file;    // display uploaded file name
				tables[0].rows[3].cells[0].innerHTML='<?php echo _AT("replace_file"); ?>';    // change label from "upload file" to "replace file"
			}
			
			// set uploaded file
		} else if(filedata.action=="alter") {
			document.form['rb_action[' +num_of_files +']'][1].checked = true;
			document.form['rb_action[' +num_of_files +']'][1].focus();

			srcElement = document.form['rb_action[' +num_of_files +']'][1];
			document.form['alter_filename[' +num_of_files +']'].value = filedata.name;
			document.form['alter_dir[' +num_of_files +']'].value = filedata.location;
			document.form['alter_code_from[' +num_of_files +']'].value = filedata.code_from;
			document.form['alter_code_to[' +num_of_files +']'].value = filedata.code_to;
		} else if(filedata.action=="delete") {
			document.form['rb_action[' +num_of_files +']'][2].checked = true;
			document.form['rb_action[' +num_of_files +']'][2].focus();

			srcElement = document.form['rb_action[' +num_of_files +']'][2];
			document.form['delete_filename[' +num_of_files +']'].value = filedata.name;
			document.form['delete_dir[' +num_of_files +']'].value = filedata.location;
		} if(filedata.action=="overwrite") {
			document.form['rb_action[' +num_of_files +']'][3].checked = true;
			document.form['rb_action[' +num_of_files +']'][3].focus();

			srcElement = document.form['rb_action[' +num_of_files +']'][3];
			document.form['overwrite_filename[' +num_of_files +']'].value = filedata.name;
			document.form['overwrite_dir[' +num_of_files +']'].value = filedata.location;

			// set uploaded file
			if (filedata.uploaded_file != "")
			{
				document.form['overwrite_uploaded_file[' +num_of_files +']'].value = filedata.uploaded_file;
				var tables = newDiv.getElementsByTagName('TABLE');
				tables[3].rows[2].style.display='';    // display the row of uploaded file
				tables[3].rows[2].cells[1].innerHTML=filedata.uploaded_file;    // display uploaded file name
				tables[3].rows[3].cells[0].innerHTML='<?php echo _AT("replace_file"); ?>';    // change label from "upload file" to "replace file"
			}
			
		}
		
		show_content({srcElement:srcElement});
	}

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
		<input type="radio" name="rb_action[{1}]" value="add" id="add" checked onclick="show_content(event);" /><label for="add"><?php echo _AT("add"); ?></label> \
		<input type="radio" name="rb_action[{1}]" value="alter" id="alter" onclick="show_content(event);" /><label for="alter"><?php echo _AT("alter"); ?></label> \
		<input type="radio" name="rb_action[{1}]" value="delete" id="delete" onclick="show_content(event);" /><label for="delete"><?php echo _AT("delete"); ?></label> \
		<input type="radio" name="rb_action[{1}]" value="overwrite" id="overwrite" onclick="show_content(event);" /><label for="overwrite"><?php echo _AT("overwrite"); ?></label> \
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
		<tr style="display: none"> \
			<td><?php echo _AT("file"); ?></td> \
			<td></td> \
			<td><INPUT TYPE="hidden" NAME="add_uploaded_file[{1}]" SIZE="40" style="max-width:100%" /></td> \
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
		<tr id="overwrite_uploaded_file" style="display:none"> \
			<td><?php echo _AT("file"); ?></td> \
			<td></td> \
			<td><INPUT type="hidden" NAME="overwrite_uploaded_file[{1}]" SIZE="40" style="max-width:100%" /></td> \
		</tr> \
		<tr> \
			<td><?php echo _AT("upload_file"); ?></td> \
			<td><INPUT TYPE="file" NAME="overwrite_upload_file[{1}]" SIZE="40" style="max-width:100%" /></td> \
		</tr> \
	</table> \
	</div> \
	<div class="row buttons" style="float:left"> \
		<input type="button" value="<?php echo _AT("delete_this_file"); ?>" onclick="del_file(event)" /> \
	</div> \
	<br /><br /> \
</div> \
';

//-->
</script>


<script language="JavaScript" type="text/javascript">
	var patch_files = <?php echo json_encode_result($result_patch_files); ?>;
	
	window.onload = function() {
		for(var i=0; i<patch_files.length; i++) {
			add_file(patch_files[i]);
		}
	}
</script>
	
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
