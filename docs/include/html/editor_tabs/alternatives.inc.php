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

?>

<div class="row_alternatives" id="radio_alt">
	<input type="radio" name="alternatives" value="1" id="single_resources" checked="checked" onClick="openIt(1)" <?php if (($_POST['alternatives'] == 1) || ($_GET['alternatives'] == 1)) { echo 'checked="checked"';} ?> />
	<label for="single_resources"><?php echo _AT('define_alternatives_to_non_textual_resources');  ?>.</label>
	<br/>
	<input type="radio" name="alternatives" value="2" id="whole_page" onClick="openIt(2)" <?php if (($_POST['alternatives'] == 2) || ($_GET['alternatives'] == 2)) { echo 'checked="checked"'; } ?> />
	<label for="whole_page"><?php echo _AT('define_alternatives_to_textual_resources');  ?></label>
</div>

<div class="row_alternatives" id="1" style="display:'none';">
	<div class="column_primary">
		<?php 
	//	$alternatives=1;		
		require(AT_INCLUDE_PATH.'html/resources_parser.inc.php');

		$n=count($resources);
				
		if ($n==0){
			echo '<p>';
			echo _AT('No_non_textual_resources');
			echo '!</p>';
			}
		else {
			$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." order by primary_resource_id";
	      	$result	= mysql_query($sql, $db);
	      	if (mysql_num_rows($result) > 0) {
	      		$j=0;
				while ($row = mysql_fetch_assoc($result)) {
					$whole_resource = $stripslashes(htmlspecialchars($row['resource']));
					$body = $stripslashes(htmlspecialchars($_POST['body_text']));
					if (trim($whole_resource) == trim($body)){
						continue;
						}
					else {
						$resources_db[$j]=$row['resource'];
						$j++;
					}
				}
			}
			$m=count($resources_db);
			for ($i=0; $i < $n; $i++){
				for($j=0; $j < $m; $j++){
					if (trim($resources[$i])==trim($resources_db[$j])){
						$present[$i]=true;
					}
				}
				if ($present[$i]==false) {
					$sql_ins= "INSERT INTO ".TABLE_PREFIX."primary_resources VALUES (NULL, $cid, '$resources[$i]', NULL)";
					$r 		= mysql_query($sql_ins, $db);
				}
			}
			$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." order by primary_resource_id";
	      	$result	= mysql_query($sql, $db);
	      	while ($row = mysql_fetch_assoc($result)) {
	      		$present=false;	
	      		for ($i=0; $i < $n; $i++){
	      			$whole_resource = $stripslashes(htmlspecialchars($row['resource']));
					$body = $stripslashes(htmlspecialchars($_POST['body_text']));
					if (trim($whole_resource) == trim($body)){
	      				$present=true;
	      				continue;
	      			}
	      			else {
	      				if (trim($resources[$i])==trim($row['resource'])) {
	      					$present=true;	
	      					?>
	      					<div class="resource_box">
	      						<p>
	      							<input type="radio" name="resources" value="<?php echo $row['resource']?>" id="<?php echo $row['resource']?>"/>
									<label class="primary" for="<?php echo $row['resource']?>"><?php link_name_resource($row['resource']);?></label>
								</p>
								<?php checkbox_types($row[primary_resource_id], 'primary', 'non_textual');
								
							$languages = $languageManager->getAvailableLanguages();
							echo '<label for="lang_'.$row[primary_resource_id].'">'._AT('resource_language').'</label><br />';
							echo '<select name="lang_'.$row[primary_resource_id].'" id="lang_'.$row[primary_resource_id].'">';
							foreach ($languages as $codes)
							{
								$language = current($codes);
								$lang_code = $language->getCode();
								$lang_native_name = $language->getNativeName();
								$lang_english_name = $language->getEnglishName()
								?>
									<option value="<?php echo $lang_code ?>"
									<?php if($lang_code == $row[language_code]) echo 'selected'?>><?php echo $lang_english_name . ' - '. $lang_native_name ?></option>
								<?php
							}
							?>
							</select>
							<?php
							$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=".$row[primary_resource_id]." order by secondary_resource_id";
	      					$result_alt	= mysql_query($sql_alt, $db);
		      				if (mysql_num_rows($result_alt) > 0) {
		      					?>
									<h2 class="alternatives_to"><?php echo _AT('alternatives_to').' '.$row['resource'];?></h2>
									<?php
								while ($alternative = mysql_fetch_assoc($result_alt)){
									?>
									<div class="alternative_box">
	      								<p><?php link_name_resource($alternative['secondary_resource']);?></p>
		      							<?php 
		      							checkbox_types($alternative['secondary_resource_id'], 'secondary', 'non_textual');
			      						$languages = $languageManager->getAvailableLanguages();
										echo '<label for="lang_'.$alternative['secondary_resource_id'].'">'._AT('resource_language').'</label><br />';
										echo '<select name="lang_'.$alternative['secondary_resource_id'].'" id="lang_'.$alternative['secondary_resource_id'].'">';
										foreach ($languages as $codes){
											$language = current($codes);
											$lang_code = $language->getCode();
											$lang_native_name = $language->getNativeName();
											$lang_english_name = $language->getEnglishName();
											echo '<option value="'.$lang_code.'"';
											if ($lang_code == $alternative['language_code']) 
												echo 'selected';
											echo '>';
											echo $lang_english_name . ' - '. $lang_native_name; 
											echo '</option>';
											}
										?>
										</select>
										<p><?php delete_alternative($alternative, $cid, $current_tab); ?></p>
									</div>
									<?php
									}
								}
							?>
						</div>
						<?php			
						}
					}
				}
				if ($present==false){
					$res=addslashes($row['resource']);
					$sql_sel 	= "SELECT primary_resource_id FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." and resource='".$res."'";
					$result_sel = mysql_query($sql_sel, $db);
					while ($id = mysql_fetch_assoc($result_sel)){
						$sql_del 	= "DELETE FROM ".TABLE_PREFIX."primary_resources WHERE primary_resource_id='".$id[primary_resource_id]."'";
						$result_del = mysql_query($sql_del, $db);
						$sql_del 	= "DELETE FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id=".$id."'";
						$result_del = mysql_query($sql_del, $db);
					}
				}
			}
		}		
		?>
		</div>
	
		<div class="column_equivalent">
		<?php 
			require(AT_INCLUDE_PATH.'html/filemanager_display_alternatives.inc.php');
		?>
		</div>
	</div>
	
	
	<div class="row_alternatives" id="2" style="display: none;">
		<div class="row">
			<?php
				
				if ($changes_made)
					$body_ins = $_POST['body_text'];
				else {
					$sql = "SELECT * FROM AT_content WHERE content_id='$cid'";
					$result = mysql_query($sql, $db);
					 //echo $sql;
					while ($row = mysql_fetch_assoc($result)) {
						$body_ins = addslashes($row['text']);
					}
				}
				
				$sql	= "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and resource='".$body_ins."'";
	      		$result	= mysql_query($sql, $db);
	      			    
	      		if (mysql_num_rows($result) > 0) {
	      			while ($row = mysql_fetch_assoc($result)) {
	      				$whole_resource = $stripslashes(htmlspecialchars($row['resource']));
						$body = $stripslashes(htmlspecialchars($_POST['body_text']));
						if (trim($whole_resource) == trim($body))
							$content_id = $row[primary_resource_id];
					}
				}
				else {
					$sql_ins = "INSERT INTO ".TABLE_PREFIX."primary_resources VALUES (NULL, $cid, '$body_ins', 'en')";
					$r 		 = mysql_query($sql_ins, $db);
					$sql_sel = "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and resource='$body_ins'";
	      			$result	 = mysql_query($sql_sel, $db);
	      			while ($row = mysql_fetch_assoc($result)) {
						$whole_resource = $stripslashes(htmlspecialchars($row['resource']));
						$body = $stripslashes(htmlspecialchars($_POST['body_text']));
						if (trim($whole_resource) == trim($body))
							$content_id = $row[primary_resource_id];
						}
	     			}
	    		checkbox_types($content_id, 'primary', 'textual');
	    		
	    		$languages = $languageManager->getAvailableLanguages();
				echo '<label for="lang_'.$content_id.'">'._AT('resource_language').'</label><br />';
				echo '<select name="lang_'.$content_id.'" id="lang_'.$content_id.'">';
				foreach ($languages as $codes)
						{
							$language = current($codes);
							$lang_code = $language->getCode();
							$lang_native_name = $language->getNativeName();
							$lang_english_name = $language->getEnglishName()
							?>
								<option value="<?php echo $lang_code ?>"
								<?php if($lang_code == $row[language_code]) echo 'selected'?>><?php echo $lang_english_name . ' - '. $lang_native_name ?></option>
							<?php
						}
						?>
					</select>
		</div>
				
	
		<div class="row">
			<?php echo _AT('formatting'); ?><br />
			
			

			<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=true;" />
			<label for="text"><?php echo _AT('plain_text'); ?></label>
	
			<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisualbutton.disabled=false;"/>
			<label for="html"><?php echo _AT('html'); ?></label>

			<input type="hidden" name="setvisual" value="<?php if ($_POST['setvisual']==1 || $_REQUEST['setvisual']==1 || $_GET['setvisual']==1) echo '1'; else echo '0'; ?>" />
			<input type="hidden" name="settext" value="<?php if ($_POST['settext']==1 || $_REQUEST['settext']==1 || $_GET['settext']==1) echo '1'; else echo '0'; ?>" />
			<input type="button" name="setvisualbutton" value="<?php echo _AT('switch_visual'); ?>" onClick="switch_body_editor()" />

		<script type="text/javascript" language="javascript">
		//<!--
			document.write(" <a href=\"#\" onclick=\"window.open('<?php echo AT_BASE_HREF; ?>tools/filemanager/index.php?framed=1<?php echo SEP; ?>popup=1<?php echo SEP; ?>cp=<?php echo $content_row['content_path']; ?>','newWin1','menubar=0,scrollbars=1,resizable=1,width=640,height=490'); return false;\"><?php echo _AT('open_file_manager'); ?> </a>");
		//-->
		</script>
		<noscript>
			<a href="<?php echo AT_BASE_HREF; ?>tools/filemanager/index.php?framed=1"><?php echo _AT('open_file_manager'); ?></a>
		</noscript>	
	</div>
	<div class="row">
		<label for="body_text"><?php echo _AT('body');  ?></label><br />

<?php 

// kludge #1548
if (trim($_POST['body_text']) == '<br />') {
	$_POST['body_text'] = '';
}
if ($do_check) {
	$_POST['body_text'] = $stripslashes($_POST['body_text']);
}

?>
		<textarea name="body_text_alt" id="body_text_alt" cols="" rows="20"><?php echo htmlspecialchars($_POST['body_text']); ?></textarea>	
	</div>
	
	<div class="row">
		<?php require(AT_INCLUDE_PATH.'html/editor_tabs/content_code_picker.inc.php'); ?>
	</div>

	<div class="row">
		<strong><?php echo _AT('or'); ?></strong> <?php echo _AT('paste_file'); ?><br />
		<input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>" /><br />
		<small class="spacer">&middot;<?php echo _AT('html_only'); ?><br />
		&middot;<?php echo _AT('edit_after_upload'); ?></small>
	</div>
	
	<div class="row">
	<?php  
		$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=".$content_id." order by secondary_resource_id";
	    $result_alt	= mysql_query($sql_alt, $db);
		if (mysql_num_rows($result_alt) > 0) {
			echo '<p class="alternatives_to">'. _AT('alternatives').'</p>';
			while ($alternative = mysql_fetch_assoc($result_alt)){
				$savant->assign('body', format_content($alternative['secondary_resource'], $content_row['formatting'], $glossary));
			    checkbox_types($alternative['secondary_resource_id'], 'secondary', 'non_textual');
			     						$languages = $languageManager->getAvailableLanguages();
										echo '<label for="lang_'.$alternative['secondary_resource_id'].'">Resource language</label><br />';
										echo '<select name="lang_'.$alternative['secondary_resource_id'].'" id="lang_'.$alternative['secondary_resource_id'].'">';
										foreach ($languages as $codes){
											$language = current($codes);
											$lang_code = $language->getCode();
											$lang_native_name = $language->getNativeName();
											$lang_english_name = $language->getEnglishName();
											echo '<option value="'.$lang_code.'"';
											if ($lang_code == $alternative['language_code']) 
												echo 'selected';
											echo '>';
											echo $lang_english_name . ' - '. $lang_native_name; 
											echo '</option>';
											}
										?>
										</select>
										<p><?php delete_alternative($alternative, $cid, $current_tab); ?></p>
									</div>
									<?php
									}
								}
							?>
	</div>
</div>	

	<script type="text/javascript" language="javascript">
	//<!--
	function on_load()
	{
		if (document.getElementById("text").checked)
			document.form.setvisualbutton.disabled = true;
			
		if (document.form.displayhead.value==1)
		{
			document.getElementById("headDiv").style.display = '';
			document.form.edithead.value = "<?php echo _AT('hide'); ?>"
		}
			
		if (document.form.setvisual.value==1)
		{
			tinyMCE.execCommand('mceAddControl', false, 'body_text');
			document.form.formatting[0].disabled = "disabled";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_text'); ?>";
		}
		else
		{
			document.form.setvisualbutton.value = "<?php echo _AT('switch_visual'); ?>";
		}
	}
	
	// show/hide "cusomized head" editor
	function switch_head_editor()
	{
		if (document.form.edithead.value=="<?php echo _AT('edit'); ?>")
		{
			document.form.edithead.value = "<?php echo _AT('hide'); ?>"
			document.getElementById("headDiv").style.display = "";
			document.form.displayhead.value=1;
		}
		else
		{
			document.form.edithead.value = "<?php echo _AT('edit'); ?>"
			document.getElementById("headDiv").style.display = "none";
			document.form.displayhead.value=0;
		}
	}
	
	// switch between text, visual editor for "body text"
	function switch_body_editor()
	{
		if (document.form.setvisualbutton.value=="<?php echo _AT('switch_visual'); ?>")
		{
			tinyMCE.execCommand('mceAddControl', false, 'body_text');
			document.form.setvisual.value=1;
			document.form.settext.value=0;
			document.form.formatting[0].disabled = "disabled";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_text'); ?>";
		}
		else
		{
			tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
			document.form.setvisual.value=0;
			document.form.settext.value=1;
			document.form.formatting[0].disabled = "";
			document.form.setvisualbutton.value = "<?php echo _AT('switch_visual'); ?>";
		}
	}
	
	
function openIt(x){ 
    if (x=='1'){
    	document.getElementById('2').style.display = "none";
     	document.getElementById('1').style.display = "block";
     
    }
    else {
		document.getElementById('1').style.display = "none";
  		document.getElementById('2').style.display = "block";
  		
  	}
}
//-->
</script>