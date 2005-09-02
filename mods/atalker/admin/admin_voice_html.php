<?php
/****************************************************************/
/* ATalker													*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay 				        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: admin_voice_html.php 5123 2005-07-12 14:59:03Z greg

// Generate the HTML for the Administrator's  Voice Manager

?>


<table class="data" style="width:95%;" summary="" rules="cols" >
<tfoot><tr><td colspan="3">		
<input type="submit" class="submit" name="create" value="<?php echo _AT('create_voice'); ?>" />
<input type="submit" class="submit" name="remove" value="<?php echo _AT('remove_voice'); ?>" />
</td></tr>
</tfoot>
<tbody>
<tr>
<td colspan="3">

	<script type="text/javascript">
	<!--
	function Checkall(form){ 
	for (var i = 0; i < form.elements.length; i++){    
	eval("form.elements[" + i + "].checked = form.checkall.checked");  
	} 
	}
	function selectCat(catID, cat) {
		for (var i=0;i<document.form.elements.length;i++) {
			var e = document.form.elements[i];
			if ((e.name == 'add_questions[' + catID + '][]') && (e.type=='checkbox'))
				e.checked = cat.checked;
		}
	}
	-->
	</script>
	<br /><br />
	<!-- Note that validation will fail here when language variable names begin with something other than a letter
		e.g _AT(404_blurb) fails
	-->
	
	<h3><?php echo _AT('manage_atutor_voice'); ?></h3>

<?php

	
	$sql = "SELECT * from ".TABLE_PREFIX."language_text WHERE language_code = '".$_SESSION['lang']."' AND variable = '_template'";
	$result = mysql_query($sql, $db);
	$num_rows = mysql_num_rows($result);
	$num_pages = ($num_rows/20);

	if(!$_REQUEST['page'] || $_GET['page'] == '1'){
		$sql .= " LIMIT 20";
	}else if($_REQUEST['page'] =="all"){
	
	}else{
		$start_limit = ((intval($_REQUEST['page'])-2)*20);
		$end_limit = ($start_limit +20);
		$sql .= " LIMIT ".$end_limit.', 20';
	}
?>
	<table width="100%">
		<tr>
			<td colspan="3"><?php echo _AT('pages'); ?>:
	
	<?php
		// create the paginator
		$p = '1';
		for ($i=1; $i < $num_pages; $i++){
			if($i == $_REQUEST['page']){
				echo ' '.$p.' |';
			}else{
				echo '<a href="'.$_base_url.'mods/atalker/admin/admin_index.php?page='.$p.SEP.'tab='.$tab.SEP.'postdata='.urlencode($postdata).'">'.$p.'</a> |'."\n";
			}
			$p++;
		}
		
	?>
		<a href="<?php echo $base_url;?>mods/atalker/admin/admin_index.php?<?php echo 'page=all'.SEP.'tab='.$tab.SEP.'postdata='.urlencode($postdata); ?>"><?php echo _AT('all'); ?></a>
		
			</td>
		</tr>
		<tr>
			<th><input type="checkbox" name="checkall" onclick="Checkall(form);" id="selectall" title="<?php echo _AT('select_unselect'); ?>" />&nbsp;</th>
			<th scope="col"><?php echo _AT('variable'); ?></th><th scope="col"><?php echo _AT('text'); ?></th>
		</tr>
	
	<?php		
	
		$result = mysql_query($sql, $db);
		while($row = mysql_fetch_array($result)){
			if(strlen($row['text']) > 100){
				$chars = '('._AT(characters_total).' '.strlen($row['text']).')';
				$truncate = "...";
			}
		echo '<tr><td valign="top"><input type="checkbox" id="'.$row['term'].'" name="check[]" value="'.$row['term'].'" /></td><td valign="top"><label for="'.$row['term'].'">';
		
		// check if template speech file exits, and create a link to the file if it does
		if(file_exists(AT_SPEECH_TEMPLATE_DIR.$row['term'].'.mp3')){
		
			//echo '<a href="'.AT_SPEECH_TEMPLATE_URL.$row['term'].'.mp3">'.$row['term'].' (mp3)</a>';
			echo '<a href="'.$base_url.'mods/atalker/admin/play_voice.php?play_voice='.$row['term'].'.mp3">'.$row['term'].' (mp3)</a>';
		
		}else if(file_exists(AT_SPEECH_TEMPLATE_DIR.$row['term'].'.ogg')){
		
			echo '<a href="'.$base_url.'mods/atalker/admin/play_voice.php?play_voice='.$row['term'].'.ogg">'.$row['term'].' (ogg)</a>';
			//echo '<a href="'.AT_SPEECH_TEMPLATE_URL.$row['term'].'.ogg">'.$row['term'].' (ogg)</a>';
		
		}else{
		echo $row['term'];
		}
	
		echo '</label></td><td>'.stripslashes(htmlspecialchars($row['text'])).'</td></tr>'."\n";
			$truncate = "";
			$chars = '';
		}
		echo '</table>';
	?>
	
			</td>
		</tr>
	</tbody>
	</table>
