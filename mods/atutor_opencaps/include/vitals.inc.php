<?php
/****************************************************************/
/* Atutor-OpenCaps Module														*/
/****************************************************************/
/* Copyright (c) 2010                                           */
/* Written by Antonio Gamba								        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

// load AtOpenCaps config File
include_once('include/config.inc.php');

// load AtOpenCaps Classes
	//include_once('classes/ATOCSecurityManager_class.php');
include_once('include/classes/ATOC_Debug.php');
include_once('include/classes/ATOC_ProjectManager.php');
include_once('include/classes/ATOC_ServerFiles.php');
include_once('include/classes/ATOC_Json.php');

// errors 
$ocAtSettings['messages'] = array();
$ocAtSettings['lang'] = array();

/*
$ocAtSettings['lang']['atoc_notActiveCourseError'] = '"Note: In order to use this module you MUST login as an instructor. <br/> This Module only works when you are working on a particular course."';
$ocAtSettings['lang']['atoc_projectDeleted'] = 'Project Deleted';
$ocAtSettings['lang']['atoc_projectUpdated'] = 'Project Updated';
$ocAtSettings['lang']['atoc_projectCreated'] = ' New Project Created';
$ocAtSettings['lang']['atoc_debugModeActive'] = '******** Debug Mode is Active ******** ';
$ocAtSettings['lang']['atoc_backToContentLinkAlt'] = 'Back To: Content';
$ocAtSettings['lang']['atoc_myCaptionProjectsLink'] = 'My Caption Projects';
$ocAtSettings['lang']['atoc_uploadMediaLink'] = 'Upload Media';
$ocAtSettings['lang']['atoc_addProjectLink'] = 'Add Captioning Project';
$ocAtSettings['lang']['atoc_captionEditorLink'] = 'Caption Editor';
$ocAtSettings['lang']['atoc_previewLink'] = 'Preview';
$ocAtSettings['lang']['atoc_helpLink'] = 'Help';
$ocAtSettings['lang']['atoc_noIframeSupportedError'] = 'This option will not work correctly.<br/>Unfortunately, your browser does not support inline frames.';
$ocAtSettings['lang']['atoc_noMediaFileFound'] = 'There are NO media files in the course content Directory';
$ocAtSettings['lang']['atoc_uploadMediaMsg'] = 'Upload Media in order to start a captioning Project.';
$ocAtSettings['lang']['atoc_projectName'] = 'Project Name';
$ocAtSettings['lang']['atoc_mediaName'] = 'Media File';
$ocAtSettings['lang']['atoc_mediaWidth'] = 'Width';
$ocAtSettings['lang']['atoc_mediaHeight'] = 'Height';
$ocAtSettings['lang']['atoc_newCaptionFileMsg'] = 'Create a new Caption File';
$ocAtSettings['lang']['atoc_existCaptionFileMsg'] = 'Select An existing Caption';
$ocAtSettings['lang']['atoc_addProjectButtonLabel'] = 'Add Captioning Project';
$ocAtSettings['lang']['atoc_projectsNotFoundError'] = 'No Projects found';
$ocAtSettings['lang']['atoc_saveProjectButtonLabel'] = 'Save';
$ocAtSettings['lang']['atoc_deleteProjectButtonLabel'] = 'Delete';
$ocAtSettings['lang']['atoc_editProjectLink'] = 'Edit';
$ocAtSettings['lang']['atoc_previewProjectLink'] = 'Preview';
$ocAtSettings['lang']['atoc_htmlCode'] = 'HTML Code';
*/


// Spanish
/*
$ocAtSettings['lang']['atoc_notActiveCourseError'] = '"Nota: Para poder usar este m&oacute;dulo Ud. debe ser in instructor/Profesor.<br/> Este m&oacute;dulo solo est&aacute; activo cuando el se est&aacute; tramajando en un curso"';
$ocAtSettings['lang']['atoc_projectDeleted'] = 'Se ha borrado el Proyecto Seleccionado';
$ocAtSettings['lang']['atoc_projectUpdated'] = 'El projecto ha sido Actualizado';
$ocAtSettings['lang']['atoc_projectCreated'] = ' Se ha Creado un nuevo Proyecto';
$ocAtSettings['lang']['atoc_debugModeActive'] = '******** El modo Debug/depuraci&oacute;n está activo  ******** ';
$ocAtSettings['lang']['atoc_backToContentLinkAlt'] = 'Ir a Contenido';
$ocAtSettings['lang']['atoc_myCaptionProjectsLink'] = 'Mis Proyectos';
$ocAtSettings['lang']['atoc_uploadMediaLink'] = 'Adicionar Archivos';
$ocAtSettings['lang']['atoc_addProjectLink'] = 'Crear Nuevo Proyecto';
$ocAtSettings['lang']['atoc_captionEditorLink'] = 'Editar Subt&iacute;tulos';
$ocAtSettings['lang']['atoc_previewLink'] = 'Vista Previa';
$ocAtSettings['lang']['atoc_helpLink'] = 'Ayuda';
$ocAtSettings['lang']['atoc_noIframeSupportedError'] = 'Su Navegador no soporta iframes.<br/>Se recomienda actualizar el navegador de Internet';
$ocAtSettings['lang']['atoc_noMediaFileFound'] = 'NO se han encontrado archivos de video/audio en el directorio del curso';
$ocAtSettings['lang']['atoc_uploadMediaMsg'] = 'Suba archivos para iniciar un proyecto de Subt&iacute;tulos.';
$ocAtSettings['lang']['atoc_projectName'] = 'Nombre del Proyecto';
$ocAtSettings['lang']['atoc_mediaName'] = 'Archivo (video/audio)';
$ocAtSettings['lang']['atoc_mediaWidth'] = 'Ancho';
$ocAtSettings['lang']['atoc_mediaHeight'] = 'Alto';
$ocAtSettings['lang']['atoc_newCaptionFileMsg'] = 'Crear un nuevo archivo de Subt&iacute;tulos';
$ocAtSettings['lang']['atoc_existCaptionFileMsg'] = 'Usar un archivo de Subt&iacute;tulos existente';
$ocAtSettings['lang']['atoc_addProjectButtonLabel'] = 'Crear un nuevo Proyecto de Subt&iacute;tulos';
$ocAtSettings['lang']['atoc_projectsNotFoundError'] = 'No se han encontrado Proyectos';
$ocAtSettings['lang']['atoc_saveProjectButtonLabel'] = 'Guardar';
$ocAtSettings['lang']['atoc_deleteProjectButtonLabel'] = 'Borrar';
$ocAtSettings['lang']['atoc_editProjectLink'] = 'Editar';
$ocAtSettings['lang']['atoc_previewProjectLink'] = 'Vista Previa';
$ocAtSettings['lang']['atoc_htmlCode'] = 'C&oacute;digo HTML';
*/

// look for Win OS file structure 
if (preg_match('/WIN/', PHP_OS))
{
	$ocAtSettings['serverOs']  = 'win'; 
}

if ($ocAtSettings['serverOs']=='win')
{
	$ocAtSettings['dirSep'] = "\\"; 
	
} else {
	$ocAtSettings['dirSep'] = "/"; 
}

// if project id active is isset
if (isset($_GET['id']) && $_GET['id']!='')
{
	$_SESSION['ATOC_activeProjectId']= $_GET['id'];
}

// set session active id
if (!isset($_SESSION['ATOC_activeProjectId']))
{
	$_SESSION['ATOC_activeProjectId'] = '';
}

// get actions variables
if(isset($_POST['action']))
{
	$ocAtAction = $_POST['action']; 
} else {

	// get actions variables
	if(isset($_GET['action']))
	{
		$ocAtAction = $_GET['action']; 
	} else {
	
		$ocAtAction = '';
	}
}
$ocAtSettings['atWebPath'] = '';
?>