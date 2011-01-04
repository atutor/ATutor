<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2009                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/*                                                                      */
/* Contributed by University of Bologna.                                */
/************************************************************************/

/* Il file viene richiamato per svolgere l'operazione di switch sui due modelli di visualizzazione della home-page.
* 1) Visualizzazione Classica: Per ogni modulo viene visualizzata semplicemente l'icona corrispondente. (home_type = 0).
* 2) Visualizzazione Aggiuntiva: i moduli saranno disposti su due colonne con scorciatoie di gestione sia per l'istruttore che per lo studente. (home_type =1).
*/

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if(isset($_GET['swid'])){							//si controlla se è stato settato swid tramite $_GET.
	$swid = !$_GET['swid'];							//viene negato il valore di "swid" in modo da passare alla visualizzazione complementare aggiornando in seguito il DB.
	
	if (stristr($_SERVER['HTTP_REFERER'], '/student_tools/index.php'))
		$sql    = "UPDATE ".TABLE_PREFIX."fha_student_tools SET home_view='$swid' WHERE course_id=$_SESSION[course_id]";
	else
		$sql    = "UPDATE ".TABLE_PREFIX."courses SET home_view='$swid' WHERE course_id=$_SESSION[course_id]";
	
	$result = mysql_query($sql, $db);
	header('Location:'.$_SERVER['HTTP_REFERER']);	//redirect alla home del corso per la visualizzazione immediata delle modifiche apportate.
}
?>