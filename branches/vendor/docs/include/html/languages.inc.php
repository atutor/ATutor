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

if(count($available_languages) < 2){
	return;
	}
?>


<div align="center" class="hide"><small><?php

	echo _AT('translate_to');
	echo ' ';

	foreach ($available_languages as $temp_key => $val) {
		$border = 0;
		if ($temp_key == $_SESSION['lang']) {
			$border = 2;
		}
		
		//echo '<a href="'.$_my_uri.'lang='.$temp_key.'"><img src="images/flags/'.$temp_key.'.gif" alt="'.$val[3].'" height="16" width="24" border="'.$border.'" class="menuimage" /></a>';
		if(!$l){
			echo '<a href="'.$_my_uri.'lang='.$temp_key.'">'.$val[3].'</a> ';
		}else{
			echo '| <a href="'.$_my_uri.'lang='.$temp_key.'">'.$val[3].'</a> ';
		}
		$l++;
	}
?></small></div>