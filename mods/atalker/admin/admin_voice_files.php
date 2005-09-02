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
<tbody>
<tr>
<td colspan="3">	
	<h3><?php echo _AT('manage_atutor_voice'); ?></h3>

		<table width="100%">
			<tr>
				<td colspan="3">
		<?php
		
		if ($handle = opendir(AT_SPEECH_TEMPLATE_DIR)) {
		
			echo '<ul>';

			while (false !== ($file = readdir($handle))) {
				if($file != "." && $file !=".."){
					echo '<li><a href="'.AT_SPEECH_TEMPLATE_URL.$file.'">'.$file.'</a> (<a href="'.$_SERVER['PHP_SELF'].'?delete='.$file.SEP.'tab='.$tab.'">'._AT('delete').'</a>)</li>'."\n";
					$files++;
				}
			}
			if(!$files){
				echo _AT('no_files_found'); 
		
			}

			echo '</ul>';
		
			closedir($handle);

		}
		?>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</tbody>
</table>