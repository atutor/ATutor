<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
 
?><br />
	<table border="0" summary="Copyright notice" width="100%">
	<tr>
		<td colspan="2" valign="middle" align="center"><small><?php
	
		$getcopyright_sql="select copyright from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";	
		$result2 = @mysql_query($getcopyright_sql, $db);
		$row = @mysql_fetch_row($result2);
		$show_edit_copyright = $row[0];
		if(strlen($show_edit_copyright)>0){
			echo $show_edit_copyright;
		} 

		?></small></td>
	</tr>
	<tr>
	<?php
	/* VERY IMPORTANT
	   IN KEEPING WITH THE TERMS OF THE ATUTOR LICENCE AGREEMENT (GNU GPL), THE FOLLOWING
	   COPYRIGHT LINES MAY NOT BE ALTERED IN ANY WAY.
	*/
	
	 
	?>
		<td valign="middle" width="84"><a href="http://www.atutor.ca"><img src="<?php echo $_base_path; ?>images/logo.gif" border="0" alt="ATutor.ca" style="height:1.81em; width:5.25em;" width="84" height="29" align="left" /></a><small>&#174;</small></td>
		<td><small class="copy"><?php  echo _AT('version'); ?> <?php echo VERSION; ?> | <?php
		echo _AT('copyright').' &copy; 2001-2003 <a href="http://www.atutor.ca">ATutor.ca</a>.';
		?>
		<br />
		<span id="howto"><?php  echo _AT('general_help'); ?></span></small></td>
	</tr>
	</table>
