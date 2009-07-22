<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: alternatives.inc.php 7208 2008-07-04 16:07:24Z silvia $

if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'lib/alternatives_functions.inc.php');

global $db;
/*
Edited by Greg Oct 19
Commented out radios for full page and file alternatives
for now
*/

/* Edited by Silvia Oct 24 
Added a control in order to force 
*/
if ($cid == 0){
	echo '<div class="row_alternatives" id="radio_alt">';
	echo '<p>';
	echo $msg->printInfos(SAVE_CONTENT);
	echo '</p>';
	echo '</div>';
}
else{

?>

<div class="row_alternatives" id="radio_alt">
	<input type="hidden" name="alternatives" value="1" id="single_resources" onclick="openIt(1)" />


<!--	<input type="radio" name="alternatives" value="1" id="single_resources" onclick="openIt(1)" <?php if (($_POST['alternatives'] != 2) || ($_GET['alternatives'] != 2)) { echo 'checked="checked"';} ?> />
	<label for="single_resources"><?php echo _AT('define_alternatives_to_single_resources');  ?></label>
	<br/>
	<input type="radio" name="alternatives" value="2" id="whole_page" onclick="openIt(2)" <?php if (($_POST['alternatives'] == 2) || ($_GET['alternatives'] == 2)) { echo 'checked="checked"'; } ?> />
	<label for="whole_page"><?php echo _AT('define_alternatives_to_the_whole_page');  ?></label>
<br/><br/> -->
<?php echo '<input class="button" type="submit" name="save_types_and_language" value="'._AT('save_types_and_language').'" class="button"/>'; ?>

<div class="row_alternatives" id="nontextual_div" style="display: <?php if (($_POST['alternatives'] == 2) || ($_GET['alternatives'] == 2)) echo 'none'; else echo 'block';?>;">
  <div class="column_primary">
		
<?php 
	require(AT_INCLUDE_PATH.'html/resources_parser.inc.php');

	$n=count($resources);
				
	if ($n==0)
	{
		echo '<p>'. _AT('No_resources'). '</p>';
	}
	else 
	{
		$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." order by primary_resource_id";
		$result	= mysql_query($sql, $db);
		
		if (mysql_num_rows($result) > 0) 
		{
			$j=0;
			while ($row = mysql_fetch_assoc($result)) 
			{
				$whole_resource = $stripslashes(htmlspecialchars($row['resource']));
				$body = $stripslashes(htmlspecialchars($_POST['body_text']));
				if (trim($whole_resource) == trim($body))
					continue;
				else 
				{
					$resources_db[$j]=$row['resource'];
					$j++;
				}
			}
		}

		$m=count($resources_db);
		for ($i=0; $i < $n; $i++)
		{
			for($j=0; $j < $m; $j++)
			{
				if (trim($resources[$i])==trim($resources_db[$j]))
					$present[$i]=true;
			}
			
			if ($present[$i]==false) 
			{
				$sql_sel= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." and resource='$resources[$i]'";
				$sel	= mysql_query($sql_sel, $db);
				if (mysql_num_rows($sel) > 0)
					continue;
				else
				{
					$sql_ins= "INSERT INTO ".TABLE_PREFIX."primary_resources VALUES (NULL, $cid, '$resources[$i]', NULL)";
					$r 		= mysql_query($sql_ins, $db);
				}
			}
		}
		
		$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." order by primary_resource_id";
		$result	= mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) 
		{
			$present=false;	
			
			for ($i=0; $i < $n; $i++)
			{
				$cid_wholepage = $cid.'_wholepage';
				if ($row['resource'] ==$cid_wholepage)
				{
					$present=true;
					continue;
				}
				else 
				{
					if (trim($resources[$i])==trim($row['resource'])) 
					{
						$present=true;	
?>
  <div class="resource_box">
    <p>
      <input type="radio" name="resources" value="<?php echo $row['primary_resource_id']?>" id="<?php echo 'primary_'.$row['primary_resource_id']?>"/>
      <label class="primary" for="<?php echo 'primary_'.$row['primary_resource_id']?>"><?php link_name_resource($row['resource']);?></label>
    </p>
<?php 
						checkbox_types($row[primary_resource_id], 'primary', 'non_textual');
								
						$languages = $languageManager->getAvailableLanguages();
						echo '<label for="lang_'.$row[primary_resource_id].'_primary">'._AT('primary_resource_language').'</label><br />';
						echo '<select name="lang_'.$row[primary_resource_id].'_primary" id="lang_'.$row[primary_resource_id].'_primary">';
							
						foreach ($languages as $codes)
						{
							$language = current($codes);
							$lang_code = $language->getCode();
							$lang_native_name = $language->getNativeName();
							$lang_english_name = $language->getEnglishName()
?>
      <option value="<?php echo $lang_code ?>"
    <?php if($lang_code == $row[language_code]) echo ' selected="selected"'?>><?php echo $lang_english_name . ' - '. $lang_native_name ?></option>
<?php
						}
?>
    </select>
<?php
						$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=".$row[primary_resource_id]." order by secondary_resource_id";
						$result_alt	= mysql_query($sql_alt, $db);
						
						if (mysql_num_rows($result_alt) > 0) 
						{
?>
    <h2 class="alternatives_to"><?php echo _AT('alternatives_to').' '.$row['resource'];?></h2>
<?php
							while ($alternative = mysql_fetch_assoc($result_alt))
							{
?>
    <div class="alternative_box">
      <p><?php link_name_resource($alternative['secondary_resource']);?></p>
<?php 
								checkbox_types($alternative['secondary_resource_id'], 'secondary', 'non_textual');
								$languages = $languageManager->getAvailableLanguages();
								echo '<label for="lang_'.$alternative['secondary_resource_id'].'">'._AT('secondary_resource_language').'</label><br />';
								echo '<select name="lang_'.$alternative['secondary_resource_id'].'_secondary" id="lang_'.$alternative['secondary_resource_id'].'">';
								
								foreach ($languages as $codes)
								{
									$language = current($codes);
									$lang_code = $language->getCode();
									$lang_native_name = $language->getNativeName();
									$lang_english_name = $language->getEnglishName();
									
									echo '<option value="'.$lang_code.'"';
									if ($lang_code == $alternative['language_code']) echo ' selected="selected"';
									echo '>';
									echo $lang_english_name . ' - '. $lang_native_name; 
									echo '</option>';
								} // end of foreach
?>
      </select>
	    <p><?php delete_alternative($alternative, $cid, $current_tab); ?></p>
    </div>
<?php
							} // end of while
						} // end of if
?>
	</div>
<?php	
					} // end of if
				} // end of else
			} // end of for
			
			if ($present==false)
			{
				$res=addslashes($row['resource']);
				$sql_sel 	= "SELECT primary_resource_id FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." and resource='".$res."'";
				$result_sel = mysql_query($sql_sel, $db);

				while ($id = mysql_fetch_assoc($result_sel))
				{
					$sql_del 	= "DELETE FROM ".TABLE_PREFIX."primary_resources WHERE primary_resource_id='".$id[primary_resource_id]."'";
					$result_del = mysql_query($sql_del, $db);
					$sql_del 	= "DELETE FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id=".$id."'";
					$result_del = mysql_query($sql_del, $db);
				}
			} // end of if ($present == false)
		} // end of while
	} // end of else
?>
    </div>
	
    <div class="column_equivalent">
<?php 
	require(AT_INCLUDE_PATH.'html/filemanager_display_alternatives.inc.php');
?>
	  </div>
	</div>
	
	<div class="row_alternatives" id="textual_div" style="display: <?php if (($_POST['alternatives'] == 2) || ($_GET['alternatives'] == 2)) echo 'block'; else echo 'none';?>;">
		<div class="row">
<?php
	$alternatives=2;
	if ($changes_made)
		$body_ins = $_POST['body_text'];
	else 
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."content WHERE content_id='$cid'";
		$result = mysql_query($sql, $db);

		while ($row = mysql_fetch_assoc($result)) 
			$body_ins = addslashes($row['text']);
	}
	
	$cid_wholepage = $cid.'_wholepage';
	$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and resource='$cid_wholepage'";
  $result	= mysql_query($sql, $db);
  			    
	if (mysql_num_rows($result) > 0) 
	{
		// Modified by Cindy Li on Oct 2, 2008, replaced while loop with single fetch.
		// while ($row = mysql_fetch_assoc($result)) {
		// $content_id = $row[primary_resource_id];
		// }
		$row = mysql_fetch_assoc($result);
		$content_id = $row[primary_resource_id];
	}
	else 
	{
		$sql_ins = "INSERT INTO ".TABLE_PREFIX."primary_resources VALUES (NULL, $cid, '$cid_wholepage', 'en')";
		$r 		 = mysql_query($sql_ins, $db);
		$sql_sel = "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and resource='$cid_wholepage'";
  	$result	 = mysql_query($sql_sel, $db);
  	
  	while ($row = mysql_fetch_assoc($result))
			$content_id = $row[primary_resource_id];

		//Modified by Silvia on Oct 10, 2008
		//The whole resource page is inserted in the DB always and only as a textual resource
		$sql_sel = "SELECT type_id FROM ".TABLE_PREFIX."resource_types WHERE type='textual'";
		$result  = mysql_query($sql_sel, $db);	
		$row     = mysql_fetch_assoc($result);
		$sql_ins = "INSERT INTO ".TABLE_PREFIX."primary_resources_types VALUES ($content_id, $row[type_id])"; 
		$result  = mysql_query($sql_ins, $db);
	}
	
	//Modified by Silvia on Oct 10, 2008
	//In order to remove the checkboxes to declare whole page types 
	//(it is recorded only and always as a textual resource)
	//checkbox_types($content_id, 'primary', 'textual');
		
	$languages = $languageManager->getAvailableLanguages();
	echo '<label for="lang_'.$content_id.'">'._AT('primary_resource_language').'</label><br />';
	// Modified by Cindy Li on Oct 2, 2008
	// Variable name is defined as "lang_1" here, but "editor/edit_content.php" saves on var "lang_1_primary" for 
	// primary resource language. 
	// echo '<select name="lang_'.$content_id.'" id="lang_'.$content_id.'">';
	echo '<select name="lang_'.$content_id.'_primary" id="lang_'.$content_id.'">';

	foreach ($languages as $codes)
	{
		$language = current($codes);
		$lang_code = $language->getCode();
		$lang_native_name = $language->getNativeName();
		$lang_english_name = $language->getEnglishName()
?>
			<option value="<?php echo $lang_code ?>"
<?php if($lang_code == $row[language_code]) echo ' selected="selected"'?>><?php echo $lang_english_name . ' - '. $lang_native_name ?></option>
<?php
	}
?>
		</select>
	</div>
	
	<div class="row">
<?php
$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=".$content_id." order by secondary_resource_id";
$result_alt	= mysql_query($sql_alt, $db);

if (mysql_num_rows($result_alt) > 0) 
{
	while ($alternative = mysql_fetch_assoc($result_alt))
	{
  	checkbox_types($alternative['secondary_resource_id'], 'secondary', 'non_textual');
	  $languages = $languageManager->getAvailableLanguages();
		echo '<label for="lang_'.$alternative['secondary_resource_id'].'">'._AT('secondary_resource_language').'</label><br />';
		echo '<select name="lang_'.$alternative['secondary_resource_id'].'_secondary" id="lang_'.$alternative['secondary_resource_id'].'">';
		
		foreach ($languages as $codes)
		{
			$language = current($codes);
			$lang_code = $language->getCode();
			$lang_native_name = $language->getNativeName();
			$lang_english_name = $language->getEnglishName();
			echo '<option value="'.$lang_code.'"';
			if ($lang_code == $alternative['language_code']) 
				echo ' selected="selected"';
			echo '>';
			echo $lang_english_name . ' - '. $lang_native_name; 
			echo '</option>';
		}
?>
		</select>
		<p><?php //delete_alternative($alternative, $cid, $current_tab); ?></p>
<?php
	} // end of while
} // end of if
?>
	</div>
	
	<div class="row">
		<?php echo _AT('formatting'); ?><br />
		<input type="radio" name="formatting_alt" value="0" id="text_alt_radio" <?php if ($_POST['formatting_alt'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton_alt.disabled=true;" />
		<label for="text_alt_radio"><?php echo _AT('plain_text'); ?></label>
		<input type="radio" name="formatting_alt" value="1" id="html_alt_radio" <?php if ($_POST['formatting_alt'] == 1 || $_POST['setvisual_alt']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton_alt.disabled=false;"/>
		<label for="html_alt_radio"><?php echo _AT('html'); ?></label>
		<input type="hidden" name="setvisual_alt" value="<?php if ($_POST['setvisual_alt']==1 || $_REQUEST['setvisual_alt']==1 || $_GET['setvisual_alt']==1) echo '1'; else echo '0'; ?>" />
		<input type="hidden" name="settext_alt" value="<?php if ($_POST['settext_alt']==1 || $_REQUEST['settext_alt']==1 || $_GET['settext_alt']==1) echo '1'; else echo '0'; ?>" />
		<input type="button" name="setvisualbutton_alt" value="<?php echo _AT('switch_visual'); ?>" onclick="switch_body_editor()" class="button" />

		<script type="text/javascript" language="javascript">
		//<!--
			document.write(" <a href=\"#\" onclick=\"window.open('<?php echo AT_BASE_HREF; ?>tools/filemanager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>tab=5<?php echo SEP; ?>cp=<?php echo $content_row['content_path']; ?>','newWin1','menubar=0,scrollbars=1,resizable=1,width=640,height=490'); return false;\"><?php echo _AT('open_file_manager'); ?> </a>");
		//-->
		</script>
		<noscript>
			<a href="<?php echo AT_BASE_HREF; ?>tools/filemanager/index.php?framed=1"><?php echo _AT('open_file_manager'); ?></a>
		</noscript>			
	</div>
	<div class="row">
<?php 

// kludge #1548
if (trim($_POST['body_text']) == '<br />') 
{
	$_POST['body_text'] = '';
}
if ($do_check) 
{
	$_POST['body_text'] = $stripslashes($_POST['body_text']);
}

$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=".$content_id." order by secondary_resource_id";
$result_alt	= mysql_query($sql_alt, $db);
if (mysql_num_rows($result_alt) > 0) 
{
	while ($alternative = mysql_fetch_assoc($result_alt))
	{
		echo '<label for="body_text_alt">'._AT(secondary_resource_body).'</label><br />';
		echo '<textarea name="body_text_alt" id="body_text_alt" cols="" rows="20">'.$alternative['secondary_resource'].'</textarea>';
	}
}
else
{
	echo '<label for="body_text_alt">'._AT(secondary_resource_body).'</label><br />';
	echo '<textarea name="body_text_alt" id="body_text_alt" cols="" rows="20">'.htmlspecialchars($_POST['body_text']).'</textarea>';
}
?>

	</div>
	
	<div class="row">
		<?php require(AT_INCLUDE_PATH.'html/editor_tabs/content_code_picker.inc.php'); ?>
	</div>

	<div class="row">
		<strong><?php echo _AT('or'); ?></strong> <?php echo _AT('paste_file'); ?><br />
		<input type="file" name="uploadedfile_paste" class="formfield" size="20" /> <input type="submit" name="submit_file_alt" value="<?php echo _AT('upload'); ?>" /><br />
		<small class="spacer">&middot;<?php echo _AT('html_only'); ?><br />
		&middot;<?php echo _AT('edit_after_upload'); ?></small>
	</div>
	 
</div>	

<script type="text/javascript" language="javascript">
//<!--
function on_load()
{
	if (document.getElementById("text_alt_radio").checked)
		document.form.setvisualbutton_alt.disabled = true;
		
	if (document.form.setvisual_alt.value==1)
	{
		tinyMCE.execCommand('mceAddControl', false, 'body_text_alt');
		document.form.formatting_alt[0].disabled = "disabled";
		document.form.setvisualbutton_alt.value = "<?php echo _AT('switch_text'); ?>";
	}
	else
	{
		document.form.setvisualbutton_alt.value = "<?php echo _AT('switch_visual'); ?>";
	}
}
	
// switch between text, visual editor for "body text"
function switch_body_editor()
{
	if (document.form.setvisualbutton_alt.value=="<?php echo _AT('switch_visual'); ?>")
	{
		tinyMCE.execCommand('mceAddControl', false, 'body_text_alt');
		document.form.setvisual_alt.value=1;
		document.form.settext_alt.value=0;
		document.form.formatting_alt[0].disabled = "disabled";
		document.form.setvisualbutton_alt.value = "<?php echo _AT('switch_text'); ?>";
	}
	else
	{
		tinyMCE.execCommand('mceRemoveControl', false, 'body_text_alt');
		document.form.setvisual_alt.value=0;
		document.form.settext_alt.value=1;
		document.form.formatting_alt[0].disabled = "";
		document.form.setvisualbutton_alt.value = "<?php echo _AT('switch_visual'); ?>";
	}
}
	
function openIt(x)
{
	if (x=='1')
	{
		document.getElementById('textual_div').style.display = "none";
		document.getElementById('nontextual_div').style.display = "block";
	}
	else 
	{
		document.getElementById('nontextual_div').style.display = "none";
  	document.getElementById('textual_div').style.display = "block";
 	}
}
//-->
</script> 
<?php
}
?>