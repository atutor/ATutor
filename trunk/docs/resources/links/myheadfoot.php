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

function breadcrumbs($CatID="")
{
	global $_section;
	global $db2;
	global $SITE_URL;
	global $lang, $cl;

	$start = count($_section);

	if(empty($CatID)) { 
		return;
	}
	$db2->get_ParentsInt($CatID);
	$path = $db2->TRAIL;
	if(!empty($path))
	{
		$path = array_reverse($path);
		while ( list ( $key,$val ) = each ($path))
		{
			$CatID		= stripslashes($val["CatID"]);
			$CatName	= stripslashes($val["CatName"]);
			$_section[$start][0] = $CatName;
			$_section[$start][1] = $SITE_URL.'?viewCat='.$CatID;
			$start++;
		}
	}

}

// Print the header of every page
// Modify to match style of the site
function print_header($CatID="",$title="",$msg="")
{
	global $SITE_URL;
	global $currentID;
	global $_my_uri;

	return;
}

?>