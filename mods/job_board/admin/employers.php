<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: preferences.php 9991 2010-06-07 21:30:50Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Employer.class.php');
admin_authenticate(AT_ADMIN_PRIV_JOB_BOARD); 

//Get a list of employers
$sql = 'SELECT id FROM '.TABLE_PREFIX.'jb_employers';
$result = mysql_query($sql, $db);
if($result){
    while($row = mysql_fetch_assoc($result)){
        $employers[] = new Employer($row['id']);        
    }
}


include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('employers', $employers);
$savant->display('admin/jb_employers.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
