<?php
/****************************************************************/
/* ATalker														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay 				        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: reader.html.php 5123 2005-07-12 14:59:03Z greg

// This file contains Text Reader and SABLE Reader forms

?>

<?php
$select = ' selected="selected"';
?>
<div style="width: 95%; margin-right: auto; margin-left: auto;">
<ul id="navlist">
	<?php for ($i = 0; $i<$num_tabs ; $i++): ?>
		<?php if ($tab == $i): ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i; ?><?php if ($popup_win){echo SEP.$popup_win ;}  ?>" class="active"><strong><?php echo _AT($tabs[$i]); ?></strong></a></li>
		<?php else: ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i; ?><?php if ($popup_win){echo SEP.$popup_win ;}  ?>"><?php echo _AT($tabs[$i]); ?></a></li>
		<?php endif; ?>
	<?php endfor; ?>
</ul>
</div>






<?php if($tab == "0" || $tab ==""){ ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?" method="post">
<input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>" />
<input type="hidden" name="type" value="text" />
<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
<input type="hidden" name="popup" value="<?php echo $_REQUEST['popup']; ?>" />
<table class="data" style="width:95%;" summary="" rules="cols">
<tfoot>
<tr>
	<td colspan="5">
		<input type="submit" class="submit" name="read" value="<?php echo _AT('play'); ?>" />
		<input type="submit" class="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<?php if($_SESSION['is_admin']  || authenticate(AT_PRIV_ATALKER, AT_PRIV_RETURN) || $_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){?>
			<input type="submit" class="submit" name="save" value="<?php echo _AT('save'); ?>" /><input type="text" name="filename" value="<?php echo stripslashes($_REQUEST['filename']); ?>"  title="<?php echo _AT('tts_file_names'); ?>" />
		<?php } ?>
</td>
</tr>
</tfoot>
<tbody>
<tr><td>
	<div class="input-form">
		<label for="ttsdemo"><?php echo _AT('enter_some_text'); ?></label><br />
		<textarea name="textin" cols="55" rows="4" id="ttsdemo" class="input"><?php echo stripslashes($_REQUEST['textin']); ?></textarea>
		<br />
		<label for="file_type"><?php echo _AT('file_out_type'); ?></label>
		<select name="file_type" id="file_type">
		<?php
			get_encoders();
		?>
		<option value="wav" <?php if($_POST['file_type'] == 'wav'){ echo $select; } ?>>WAV</option>
		</select>
		<label for="voice"><?php echo _AT('voice'); ?></label>
		<select name="voice" id="voice">
		<option value="voice_kal_diphone" <?php if($_POST['voice'] == 'voice_kal_diphone'){ echo $select; } ?>>US English 1 (kal)</option>
		<option value="voice_rab_diphone" <?php if($_POST['voice'] == 'voice_rab_diphone'){ echo $select; } ?>>British English 2 (rab)</option>
		<option value="voice_ked_diphone" <?php if($_POST['voice'] == 'voice_ked_diphone'){ echo $select; } ?>>US English2 (ked)</option>
		<option value="voice_don_diphone" <?php if($_POST['voice'] == 'voice_don_diphone'){ echo $select; } ?>>British English3 (don)</option>
		<option value="voice_us1_mbrola" <?php if($_POST['voice'] == 'voice_us1_mbrola'){ echo $select; } ?>>US English (us1 female)</option>
		<option value="voice_us2_mbrola" <?php if($_POST['voice'] == 'voice_us2_mbrola'){ echo $select; } ?>>US English  (us2)</option>
		<option value="voice_us3_mbrola" <?php if($_POST['voice'] == 'voice_ked_diphone'){ echo $select; } ?>>US English  (us3)</option>
		<option value="voice_el_diphone" <?php if($_POST['voice'] == 'voice_el_diphone'){ echo $select; } ?>>Spanish</option>
		<!-- modified by Eura Ercolani - 2005-11-28 - Aggiunta voci in italiano - BEGIN -->
		<option value="voice_lp_diphone" <?php if($_POST['voice'] == 'voice_lp_diphone'){ echo $select; } ?>>Italiano (femminile - festival)</option>
		<option value="voice_pc_diphone" <?php if($_POST['voice'] == 'voice_pc_diphone'){ echo $select; } ?>>Italiano (maschile - festival)</option>
		<option value="voice_lp_mbrola" <?php if($_POST['voice'] == 'voice_lp_mbrola'){ echo $select; } ?>>Italiano (femminile - mbrola)</option>
		<option value="voice_pc_mbrola" <?php if($_POST['voice'] == 'voice_pc_mbrola'){ echo $select; } ?>>Italiano (maschile - mbrola)</option>
		<!-- modified by Eura Ercolani - 2005-11-28 - END -->
		</select>
		<label for="volumn"><?php echo _AT('volumn'); ?></label><select name="volumn" id="volumn">
		<option value="1" <?php if($_POST['volumn'] == '1'){ echo $select; } ?>>1</option>
		<option value="2" <?php if($_POST['volumn'] == '2'){ echo $select; } ?>>2</option>
		<option value="3" <?php if($_POST['volumn'] == '3'){ echo $select; } ?>>3</option>
		<option value="4" <?php if($_POST['volumn'] == '4'){ echo $select; } ?>>4</option>
		<option value="5" <?php if($_POST['volumn'] == '5'){ echo $select; } ?>>5</option>
		<option value="6" <?php if($_POST['volumn'] == '6'){ echo $select; } ?>>6</option>
		<option value="7" <?php if($_POST['volumn'] == '7'){ echo $select; } ?>>7</option>
		<option value="8" <?php if($_POST['volumn'] == '8'){ echo $select; } ?>>8</option>
		<option value="9" <?php if($_POST['volumn'] == '9'){ echo $select; } ?>>9</option>
		<option value="10" <?php if($_POST['volumn'] == '10'){ echo $select; } ?>>10</option>
		</select>
		<label for="duration"><?php echo _AT('speed'); ?></label>
		<select name="duration" id="duration">
		<option value="2.0" <?php if($_POST['duration'] == '2.0'){ echo $select; } ?>><?php echo _AT('very_slow'); ?></option>
		<option value="1.5" <?php if($_POST['duration'] == '1.5'){ echo $select; } ?>><?php echo _AT('slow'); ?></option>
		<option value="1.0" <?php if($_POST['duration'] == '1.0' || !$_POST['duration'] ){ echo $select; } ?>><?php echo _AT('medium'); ?></option>
		<option value=".8" <?php if($_POST['duration'] == '.8'){ echo $select; } ?>><?php echo _AT('fast'); ?></option>
		<option value=".6" <?php if($_POST['duration'] == '.6'){ echo $select; } ?>><?php echo _AT('very_fast'); ?></option>
		</select>
		<br />
	</div>

</td></tr>
</tbody>

</table>
<?php 

if($_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){
	require_once(AT_INCLUDE_PATH.'../mods/atalker/admin/admin_voice_html.php'); 
}
?>
</form>
<?php
 //
 // The SABLE Reader Form
 //

} else if($tab == '1' || $_POST['type'] == "sable") { ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?" method="post">
<input type="hidden" name="tab" value="<?php echo $tab; ?>" />
<input type="hidden" name="type" value="sable" />
<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
<input type="hidden" name="popup" value="<?php echo $_REQUEST['popup']; ?>" />
<table class="data" style="width:95%;" summary="" rules="cols">
<tfoot>
	<tr>
		<td colspan="5">
				<input type="submit" class="submit" name="read" value="<?php echo _AT('play'); ?>" />
				<input type="submit" class="submit" name="download" value="<?php echo _AT('download'); ?>" />
				<input type="submit" class="submit" name="export" value="<?php echo _AT('export_sable'); ?>" />
			<?php if($_SESSION['is_admin']  || authenticate(AT_PRIV_ATALKER, AT_PRIV_RETURN) || $_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){?>
				<input type="submit" class="submit" name="save" value="<?php echo _AT('save'); ?>" /><input type="text" name="filename" value="<?php echo stripslashes($_REQUEST['filename']); ?>" title="<?php echo _AT('tts_file_names'); ?>" />
			<?php } ?>
		</td>
	</tr>
</tfoot>	

<tbody>
<tr><td>
	
	<div class="input-form">
	<table width="80%">
	<tr valign="top">
		<td><label for="language1"><?php echo _AT('language'); ?></label></td>
		<td>	
			<select name="language" id="language1">
				<option value="english" <?php if($_POST['language'] == 'english' || !$_POST['language']){ echo $select; } ?>><?php echo _AT('english'); ?></option>
				<option value="spanish" <?php if($_POST['language'] == 'spanish'){ echo $select; } ?>><?php echo _AT('spanish'); ?></option>
				<option value="en" <?php if($_POST['language'] == 'in'){ echo $select; } ?>><?php echo _AT('english2'); ?></option>								
			</select>
			<label for="speaker"><?php echo _AT('speaker'); ?></label>
			<select name="speaker" id="speaker">
				<option value="male1" <?php if($_POST['speaker'] == 'male1' || !$_POST['speaker']){ echo $select; } ?>><?php echo _AT('male1'); ?></option>
				<option value="male2" <?php if($_POST['speaker'] == 'male2'){ echo $select; } ?>><?php echo _AT('male2'); ?></option>
				<option value="male3" <?php if($_POST['speaker'] == 'male3'){ echo $select; } ?>><?php echo _AT('male3'); ?></option>
				<option value="male4" <?php if($_POST['speaker'] == 'male4'){ echo $select; } ?>><?php echo _AT('male4'); ?></option>
				<option value="female1" <?php if($_POST['speaker'] == 'female1'){ echo $select; } ?>><?php echo _AT('female1'); ?></option>
			</select>		
		</td>
	</tr>
	<tr valign="top">
		<td><?php echo _AT('pitch'); ?></td>
		<td>
		<label for="base"><?php echo _AT('base'); ?></label>
			<select name="base" id="base">
				<option value="default" <?php if($_POST['base'] == 'default' || !$_POST['base']){ echo $select; } ?>><?php echo _AT('default'); ?></option>
				<option value="highest" <?php if($_POST['base'] == 'highest'){ echo $select; } ?>><?php echo _AT('highest'); ?></option>
				<option value="high" <?php if($_POST['base'] == 'high'){ echo $select; } ?>><?php echo _AT('high'); ?></option>
				<option value="medium" <?php if($_POST['base'] == 'medium'){ echo $select; } ?>><?php echo _AT('medium'); ?></option>
				<option value="low" <?php if($_POST['base'] == 'low'){ echo $select; } ?>><?php echo _AT('low'); ?></option>
				<option value="lowest" <?php if($_POST['base'] == 'lowest'){ echo $select; } ?>><?php echo _AT('lowest'); ?></option>

			</select>		
			<label for="middle"><?php echo _AT('middle'); ?></label>
			<select name="middle" id="middle">
				<option value="default" <?php if($_POST['middle'] == 'default' || !$_POST['middle']){ echo $select; } ?>><?php echo _AT('default'); ?></option>
				<option value="highest" <?php if($_POST['middle'] == 'highest'){ echo $select; } ?>><?php echo _AT('highest'); ?></option>
				<option value="high" <?php if($_POST['middle'] == 'high'){ echo $select; } ?>><?php echo _AT('high'); ?></option>
				<option value="medium" <?php if($_POST['middle'] == 'medium'){ echo $select; } ?>><?php echo _AT('medium'); ?></option>
				<option value="low" <?php if($_POST['middle'] == 'low'){ echo $select; } ?>><?php echo _AT('low'); ?></option>
				<option value="lowest" <?php if($_POST['middle'] == 'lowest'){ echo $select; } ?>><?php echo _AT('lowest'); ?></option>

			</select>		
			<label for="range"><?php echo _AT('range'); ?></label>
			<select name="range" id="range">
				<option value="default" <?php if($_POST['range'] == 'default' || !$_POST['range']){ echo $select; } ?>><?php echo _AT('default'); ?></option>
				<option value="largest" <?php if($_POST['range'] == 'largest'){ echo $select; } ?>><?php echo _AT('largest'); ?></option>
				<option value="large" <?php if($_POST['range'] == 'large'){ echo $select; } ?>><?php echo _AT('large'); ?></option>
				<option value="medium" <?php if($_POST['range'] == 'medium'){ echo $select; } ?>><?php echo _AT('medium'); ?></option>
				<option value="small" <?php if($_POST['range'] == 'small'){ echo $select; } ?>><?php echo _AT('small'); ?></option>
				<option value="smallest" <?php if($_POST['range'] == 'smallest'){ echo $select; } ?>><?php echo _AT('smallest'); ?></option>

			</select>		
		</td>
	</tr>
	<tr valign="top">
		<td><label for="rate"><?php echo _AT('rate'); ?></label></td>
		<td>
			<select name="rate" id="rate">
				<option value="fastest" <?php if($_POST['rate'] == 'fastest'){ echo $select; } ?>><?php echo _AT('fastest'); ?></option>
				<option value="fast" <?php if($_POST['rate'] == 'fast'){ echo $select; } ?>><?php echo _AT('fast'); ?></option>
				<option value="medium" <?php if($_POST['rate'] == 'medium' || !$_POST['rate']){ echo $select; } ?>><?php echo _AT('medium'); ?></option>
				<option value="slow" <?php if($_POST['rate'] == 'slow'){ echo $select; } ?>><?php echo _AT('slow'); ?></option>
				<option value="slowest" <?php if($_POST['rate'] == 'slowest'){ echo $select; } ?>><?php echo _AT('slowest'); ?></option>
			</select>		
			<label for="volumn"><?php echo _AT('volumn'); ?></label>
			<select name="volumn" id="volumn">
				<option value="loudest" <?php if($_POST['volumn'] == 'loudest'){ echo $select; } ?>><?php echo _AT('loudest'); ?></option>
				<option value="loud" <?php if($_POST['volumn'] == 'loud'){ echo $select; } ?>><?php echo _AT('loud'); ?></option>
				<option value="medium" <?php if($_POST['volumn'] == 'medium' || !$_POST['volumn']){ echo $select; } ?>><?php echo _AT('medium'); ?></option>
				<option value="quiet" <?php if($_POST['volumn'] == 'quiet'){ echo $select; } ?>><?php echo _AT('quiet'); ?></option>
			</select>		
		</td>
	</tr>
</table>
</div>

<div class="input-form">
		<label for="ttsdemo"><?php echo _AT('enter_text_sable_ssml'); ?></label><br />
			<textarea name="textin" cols="55" rows="4" id="ttsdemo" class="input"><?php echo stripslashes($_REQUEST['textin']); ?></textarea>
			<br />
		<label for="file_type"><?php echo _AT('file_out_type'); ?></label>
			<select name="file_type" id="file_type">
			<?php
				get_encoders();
			?>
			<option value="wav" <?php if($_POST['file_type'] == 'wav'){ echo $select; } ?>>WAV</option>
			</select>
	</div>
</td></tr>
</tbody>
</table>

<div style="width:95%">
<?php 

if($_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){
	require_once(AT_INCLUDE_PATH.'../mods/atalker/admin/admin_voice_html.php'); 

}
?>	
</div>
</form>
<?php } else if($tab == '2' ) {?>
<div style="width:95%">

<?php	require_once(AT_INCLUDE_PATH.'../mods/atalker/admin/admin_voice_files.php');  ?>
</div>

<?php
}

?>