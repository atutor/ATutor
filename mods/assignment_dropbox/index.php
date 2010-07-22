<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php'); // for get_human_size()
require_once(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php'); // for get_human_size()
require('assignment_dropbox.inc.php');

$owner_type = WORKSPACE_ASSIGNMENT;

if (isset($_REQUEST['owner_id']) && !($has_priv = ad_authenticate($_REQUEST['owner_id']))) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

// action - Upload
if (isset($_POST['upload']) && isset($_POST['owner_id'])) {
	// handle the file upload
	$_POST['comments'] = trim($_POST['comments']);

	$parent_folder_id = abs($_POST['folder']);
	
	if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
		$msg->addError(array('FILE_TOO_BIG', get_human_size(megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1)))));

	} else if (!isset($_FILES['file']['name']) || ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) || ($_FILES['file']['size'] == 0)) {
		$msg->addError('FILE_NOT_SELECTED');

	} else if ($_FILES['file']['error'] || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		$msg->addError('FILE_NOT_SAVED');
	}

	// check that we own this folder
//	if ($parent_folder_id) {
//		$sql = "SELECT folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id=$parent_folder_id AND owner_type=$owner_type AND owner_id=$owner_id";
//		$result = mysql_query($sql, $db);
//		if (!$row = mysql_fetch_assoc($result)) {
//			$msg->addError('ACCESS_DENIED');
//			header('Location: '.AT_BASE_HREF.'mods/_standard/file_storage/index.php');
//			exit;
//		}
//	}

	if (!$msg->containsErrors()) {
		$_POST['description'] = $addslashes(trim($_POST['description']));
		$_FILES['file']['name'] = addslashes($_FILES['file']['name']);

		if ($_POST['comments']) {
			$num_comments = 1;
		} else {
			$num_comments = 0;
		}

		$sql = "INSERT INTO ".TABLE_PREFIX."files
		               (owner_type, owner_id, member_id, folder_id, 
		                parent_file_id, date, num_comments, num_revisions, file_name,
		                file_size, description) 
		        VALUES ($owner_type, $_POST[owner_id], $_SESSION[member_id], $_POST[folder_id], 
		                0, NOW(), $num_comments, 0, '{$_FILES['file']['name']}', 
		                {$_FILES['file']['size']}, '$_POST[description]')";
		$result = mysql_query($sql, $db);

		if ($result && ($file_id = mysql_insert_id($db))) {
			$path = fs_get_file_path($file_id);
			move_uploaded_file($_FILES['file']['tmp_name'], $path . $file_id);

			// check if this file name already exists
//			$sql = "SELECT file_id, num_revisions FROM ".TABLE_PREFIX."files WHERE owner_type=$owner_type AND owner_id=$owner_id AND folder_id=$parent_folder_id AND file_id<>$file_id AND file_name='{$_FILES['file']['name']}' AND parent_file_id=0 ORDER BY file_id DESC LIMIT 1";
//			$result = mysql_query($sql, $db);
//			if ($row = mysql_fetch_assoc($result)) {
//				if ($_config['fs_versioning']) {
//					$sql = "UPDATE ".TABLE_PREFIX."files SET parent_file_id=$file_id, date=date WHERE file_id=$row[file_id]";
//					$result = mysql_query($sql, $db);
//
//					$sql = "UPDATE ".TABLE_PREFIX."files SET num_revisions=$row[num_revisions]+1, date=date WHERE file_id=$file_id";
//					$result = mysql_query($sql, $db);
//				} else {
//					fs_delete_file($row['file_id'], $owner_type, $owner_id);
//				}
//			}

			$msg->addFeedback('ASSIGNMENT_HANDED_IN');
			header('Location: index.php');
			exit;
		} else {
			$msg->addError('FILE_NOT_SAVED');
		}
	}
	header('Location: index.php');
	exit;
}

// action - Delete Files/Folders (pre-confirmation)
$files = array();
foreach ($_POST as $name => $val) {
	if (substr($name, 0, 5) == 'files') $files = $val;
}
if ($has_priv && isset($_POST['delete']) && is_array($files)) {
	$hidden_vars = array();
	$hidden_vars['owner_id'] = $_REQUEST['owner_id'];
	$file_list_to_print = '';
	$files = implode(',', $files);
	$hidden_vars['files'] = $files;
	$sql = "SELECT file_name FROM ".TABLE_PREFIX."files WHERE file_id IN ($files) AND owner_type=$owner_type AND owner_id=$_REQUEST[owner_id] ORDER BY file_name";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$file_list_to_print .= '<li style="list-style: none; margin: 0px; padding: 0px 10px;"><img src="images/file_types/'.fs_get_file_type_icon($row['file_name']).'.gif" height="16" width="16" alt="" title="" /> '.htmlspecialchars($row['file_name']).'</li>';
	}
	$msg->addConfirm(array('FILE_DELETE', $file_list_to_print), $hidden_vars);
		
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printConfirm();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;

}
// action - Confirm Delete Files/Folders
else if ($has_priv && isset($_POST['submit_yes'])) {

	// handle the delete
	if (isset($_POST['files'])) {
		$files = explode(',', $_POST['files']);
	}
	if (isset($files)) {
		foreach ($files as $file) {
			fs_delete_file($file, $owner_type, $_REQUEST['owner_id']);
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: index.php');
	exit;
}
// action - Cancel Delete
else if ($has_priv && isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

// display
$groups_list = implode(',',$_SESSION['groups']);

$sql = '';
if (authenticate(AT_PRIV_ASSIGNMENTS, AT_PRIV_RETURN)) { // instructor
	$sql = "SELECT * FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] ORDER BY title";
} else { // students
	if ($groups_list <> '') {
		$sql = "(SELECT assignment_id, a.title, date_due, g.group_id
	           FROM ".TABLE_PREFIX."groups_types gt, ".TABLE_PREFIX."groups g, ".TABLE_PREFIX."assignments a
	          WHERE g.group_id in (".$groups_list.")
	            AND g.group_id in (SELECT group_id FROM ".TABLE_PREFIX."file_storage_groups)
	            AND g.type_id = gt.type_id
	            AND gt.course_id = $_SESSION[course_id]
	            AND gt.type_id = a.assign_to
	            AND (a.date_cutoff=0 OR UNIX_TIMESTAMP(a.date_cutoff) > ".time()."))
	        UNION
	        ";
	}
	$sql .= "(SELECT assignment_id, title, date_due, 0
	           FROM ".TABLE_PREFIX."assignments 
	          WHERE assign_to=0 
	            AND course_id=$_SESSION[course_id] 
	            AND (date_cutoff=0 OR UNIX_TIMESTAMP(date_cutoff) > ".time()."))
	        ORDER BY title";
}
$assignment_list_result = mysql_query($sql, $db);

$_custom_css = $_base_path . 'mods/assignment_dropbox/module.css'; // use a custom stylesheet

require (AT_INCLUDE_PATH.'header.inc.php');

?>
<div class="input-form">
<?php
if (mysql_num_rows($assignment_list_result) == 0) {
	echo _AT('none_found');
}
else {
	echo _AT('flag_text', '<img src="'.AT_BASE_HREF.'mods/assignment_dropbox/flag.png" border="0" />');
	while ($assignment_row = mysql_fetch_assoc($assignment_list_result)) {
		$owner_id = $assignment_row['assignment_id'];
		
		if ($assignment_row['group_id'] == 0) {
			$folder_id = $_SESSION['member_id'];
		} else {
			$folder_id = $assignment_row['group_id'];
		}
		
		// default sql for instructor: find all submitted assignments
		$sql = "SELECT * FROM ".TABLE_PREFIX."files 
		         WHERE owner_type=$owner_type 
		           AND owner_id=$owner_id 
		           AND parent_file_id=0";
		// students: find his own submitted assignments
		if (!authenticate(AT_PRIV_ASSIGNMENTS, AT_PRIV_RETURN)) {
			$sql .= " AND folder_id=$folder_id 
		           ORDER BY date DESC, file_name, file_size";
		}
		$result = mysql_query($sql, $db);
?>
  <div id="assignment_desc">
    <h4>
      <?php if (authenticate(AT_PRIV_ASSIGNMENTS, AT_PRIV_RETURN)) { // instructor ?>
      <a href="javascript:window.location='<?php echo AT_BASE_HREF. url_rewrite("mods/_standard/file_storage/index.php?ot=". $owner_type.SEP."oid=". $assignment_row['assignment_id'].SEP."folder=0"); ?>';" class="floatleft">
      
      <?php } else { // students ?>
      <a href="javascript:ATutor.mods.assignment_dropbox.toggleDiv(<?php echo $assignment_row['assignment_id']; ?>)" class="floatleft">
      <?php } ?>
      <img id="toggleImg<?php echo $assignment_row['assignment_id']; ?>" src="<?php echo AT_BASE_HREF; ?>images/mswitch_plus.gif" border="0" />
      <?php echo $assignment_row['title']; ?>
      </a>
      <div id="flag<?php echo $assignment_row['assignment_id']; ?>" class="flagdiv">
      <?php if (mysql_num_rows($result) > 0) { ?>
        <img src="<?php echo AT_BASE_HREF; ?>mods/assignment_dropbox/flag.png" border="0" />
      <?php }?>    
      </div>
    </h4><br />
    <strong><?php echo _AT('due_date');?>: <?php if ($assignment_row['date_due'] == '0000-00-00 00:00:00') echo _AT('no'); else echo $assignment_row['date_due']; ?></strong>
  </div>
  
  <div id="assignment_detail<?php echo $assignment_row['assignment_id']; ?>" class="assignment-detail" style="display:none">
    <?php echo '<small>'._AT('delete_text').'</small>';?><br /><br />
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" name="form<?php echo $assignment_row['assignment_id']; ?>">
    <input type="hidden" id="assignment_detail<?php echo $assignment_row['assignment_id']; ?>_toggled" value="0" />
    <table class="data">
    <thead>
    <tr>
      <th scope="col" width="10"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" id="selectall<?php echo $assignment_row['assignment_id']; ?>" name="selectall<?php echo $assignment_row['assignment_id']; ?>" onclick="CheckAll(<?php echo $assignment_row['assignment_id']; ?>);" /></th>
      <th scope="col"><?php echo _AT('file'); ?></th>
      <th scope="col"><?php echo _AT('size'); ?></th>
      <th scope="col"><?php echo _AT('date'); ?></th>
      <th scope="col"><?php echo _AT('comments');  ?></th>
    </tr>
    </thead>

    <tfoot>
    <tr>
      <td colspan="5">
        <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" 
          <?php 
          if ($assignment_row['date_due'] <> '0000-00-00 00:00:00' && strtotime("now") > strtotime($assignment_row['date_due'])) 
          	echo 'disabled="disabled"'; ?> />
      </td>
    </tr>
    </tfoot>
  
    <tbody>
  <?php if (mysql_num_rows($result) == 0) { ?>
      <tr>
        <td colspan="5"><?php echo _AT('none_found'); ?></td>
      </tr>
  <?php } else { 
  while ($file_info = mysql_fetch_assoc($result)) {?> 
      <tr onmousedown="document.form<?php echo $assignment_row['assignment_id']; ?>['r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>'].checked = !document.form<?php echo $assignment_row['assignment_id']; ?>['r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>'].checked; togglerowhighlight(this, 'r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>');" id="r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>_0">
        <td valign="top" width="10">
          <input type="checkbox" name="files<?php echo $assignment_row['assignment_id']; ?>[]" value="<?php echo $file_info['file_id']; ?>" id="r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>" onmouseup="this.checked=!this.checked" />
        </td>
        <td valign="top">
          <img src="images/file_types/<?php echo fs_get_file_type_icon($file_info['file_name']); ?>.gif" height="16" width="16" alt="" title="" /> <label for="r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>" onmousedown="document.form<?php echo $assignment_row['assignment_id']; ?>['r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>'].checked = !document.form<?php echo $assignment_row['assignment_id']; ?>['r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>'].checked; togglerowhighlight(this, 'r<?php echo $assignment_row['assignment_id']; ?>_<?php echo $file_info['file_id']; ?>');"><?php echo htmlspecialchars($file_info['file_name']); ?></label>
		  <?php if ($file_info['description']): ?>
          <p class="fm-desc"><?php echo htmlspecialchars($file_info['description']); ?></p>
		  <?php endif; ?>
        </td>
		<!-- <td valign="top">
			<?php if ($_config['fs_versioning']): ?>
				<?php if ($file_info['num_revisions']): 
					if ($file_info['num_revisions'] == 1) {
						$lang_var = 'fs_revision';
					} else {
						$lang_var = 'fs_revisions';
					}
					?>
					
          <a href="<?php echo url_rewrite('mods/_standard/file_storage/revisions.php'.$owner_arg_prefix.'id='.$file_info['file_id']); ?>"><?php echo _AT($lang_var, $file_info['num_revisions']); ?></a>
				<?php else: ?>
					-
				<?php endif; ?>
			<?php endif; ?>
        </td> -->
        <td align="right" valign="top"><?php echo get_human_size($file_info['file_size']); ?></td>
        <td align="right" valign="top"><?php echo AT_date(_AT('filemanager_date_format'), $file_info['date'], AT_DATE_MYSQL_DATETIME); ?></td>
        <td valign="top">
		<?php 
		if ($file_info['num_comments'] == 1) {
			$lang_var = 'fs_comment';
		} else {
			$lang_var = 'fs_comments';
		}
		?>
        <a href="<?php echo url_rewrite('mods/_standard/file_storage/comments.php?ot='.$owner_type.SEP.'oid='. $assignment_row['assignment_id'].SEP.'id='.$file_info['file_id']); ?>"><?php echo _AT($lang_var, $file_info['num_comments']); ?></a></td>
	  </tr>
  <?php }?>
  <?php } // end of while ($file_info) ?>
    </tbody>
  
    </table>
  
    <input type="hidden" name="owner_id" value="<?php echo $owner_id; ?>" />
    <input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
    <div class="row">
      <span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="file"><?php echo _AT('upload_file'); ?></label><br />
      <input type="file" name="file" id="file" />&nbsp;
      <input type="submit" name="upload" value="<?php echo _AT('upload'); ?>"  class="button"/>
    </div>
    <div class="row">
      <label for="description"><?php echo _AT('description'); ?></label><br />
      <textarea name="description" id="description" rows="1" cols="20"></textarea>
    </div>
    </form>
  </div><!-- end of assignment_detail -->
<?php 		
	} // end of while (assignment list)
}
?>
</div>

<script type="text/javascript">
//<![CDATA[

var ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.assignment_dropbox = ATutor.mods.assignment_dropbox || {};

(function () {
    // Toggle div of assignment details
    // param: assignment_id: used to compose div id
    // param: set_to_state: Optional. 
    //        When provided, is the open/close state for the div
    //        When not given, find the current open/close state on the div and reverse the state. 
    ATutor.mods.assignment_dropbox.toggleDiv = function (assignment_id, set_to_state){
    		flag = typeof(set_to_state) != 'undefined' ? set_to_state : jQuery("#assignment_detail"+assignment_id+"_toggled").val();

    		if (flag==1){
    			jQuery("#toggleImg"+assignment_id).attr("src", "<?php echo AT_BASE_HREF; ?>images/mswitch_plus.gif");
    			jQuery('#assignment_detail'+assignment_id+'_toggled').val(0);
    			ATutor.setcookie('ad'+assignment_id+'_'+<?php echo $_SESSION['member_id'];?>, '1', 1);
    		} else {
    			jQuery('#toggleImg'+assignment_id).attr('src', '<?php echo AT_BASE_HREF; ?>images/mswitch_minus.gif');
    			jQuery('#assignment_detail'+assignment_id+'_toggled').val(1);
    			ATutor.setcookie('ad'+assignment_id+'_'+<?php echo $_SESSION['member_id'];?>, '0', 1);
    		}
    		jQuery('#assignment_detail'+assignment_id).toggle();		
    };

    //set up the open/close state of each assignment div
    var initialize = function () {
        <?php 
        if (mysql_num_rows($assignment_list_result) > 0) {
        	mysql_data_seek($assignment_list_result, 0);
        	while ($assignment_row = mysql_fetch_assoc($assignment_list_result)) {
        ?>
    			if (ATutor.getcookie("ad<?php echo $assignment_row['assignment_id'].'_'.$_SESSION['member_id']; ?>") == "0") {
		    		ATutor.mods.assignment_dropbox.toggleDiv(<?php echo $assignment_row['assignment_id']; ?>, 0);
		    	}
        <?php } // end of while
        } // end of if?>
    };
    
    jQuery(document).ready(initialize);
})();

function CheckAll(assignmentID) {
	len = eval("document.form"+assignmentID+".elements.length");
	for (var i=0;i<len;i++)	{
		var e = eval("document.form"+assignmentID+".elements[i]");
		if ((e.name == 'files'+assignmentID+'[]') && (e.type=='checkbox')) {
			e.checked = eval("document.form"+assignmentID+".selectall"+assignmentID+".checked");
			togglerowhighlight(document.getElementById(e.id +"_0"), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}

//]]>
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>