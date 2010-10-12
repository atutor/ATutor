CREATE TABLE `atopencaps_mod` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(100) character set utf8 NOT NULL,
  `courseId` int(10) unsigned NOT NULL,
  `name` varchar(255) character set utf8 NOT NULL,
  `mediaFile` varchar(255) character set utf8 NOT NULL,
  `captionFile` varchar(255) character set utf8 default NULL,
  `timeStamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `width` varchar(255) default NULL,
  `height` varchar(255) default NULL,
  `sessionId` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

# Module Language - EN
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_moduleName','Captioning',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_notActiveCourseError','Note: In order to use this module you MUST login as an instructor. <br/> This Module only works when you are working on a particular course.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_projectDeleted','Project Deleted',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_projectUpdated','Project Updated',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_projectCreated',' New Project Created',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_debugModeActive','******** Debug Mode is Active ******** ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_backToContentLinkAlt','Back To: Content',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_myCaptionProjectsLink','My Caption Projects',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_uploadMediaLink','Upload Media',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_addProjectLink','Add Captioning Project',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_captionEditorLink','Caption Editor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_previewLink','Preview',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_helpLink','Help',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_noIframeSupportedError','This option will not work correctly.<br/>Unfortunately, your browser does not support inline frames.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_noMediaFileFound','There are NO media files in the course content Directory',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_uploadMediaMsg','Upload Media in order to start a captioning Project.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_projectName','Project Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_mediaName','Media File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_captionName','Caption File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_mediaWidth','Width',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_mediaHeight','Height',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_newCaptionFileMsg','Create a new Caption File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_existCaptionFileMsg','Select An existing Caption',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_addProjectButtonLabel','Add Captioning Project',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_projectsNotFoundError','No Projects found',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_saveProjectButtonLabel','Save',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_deleteProjectButtonLabel','Delete',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_editProjectLink','Edit',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_previewProjectLink','Preview',NOW(),'');
INSERT INTO `language_text` VALUES ('en', 'AtOpenCaps','atoc_htmlCode','HTML Code',NOW(),'');

# Module Language - ES
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_moduleName','Subt&iacute;tulos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_notActiveCourseError','Nota: Para poder usar este m&oacute;dulo Ud. debe ser in instructor/Profesor.<br/> Este m&oacute;dulo solo est&aacute; activo cuando el se est&aacute; tramajando en un curso',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_projectDeleted','Se ha borrado el Proyecto Seleccionado',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_projectUpdated','El projecto ha sido Actualizado',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_projectCreated',' Se ha Creado un nuevo Proyecto',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_debugModeActive','******** El modo Debug/depuraci&oacute;n está activo  ******** ',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_backToContentLinkAlt','Ir a Contenido',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_myCaptionProjectsLink','Mis Proyectos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_uploadMediaLink','Adicionar Archivos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_addProjectLink','Crear Nuevo Proyecto',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_captionEditorLink','Editar Subt&iacute;tulos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_previewLink','Vista Previa',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_helpLink','Ayuda',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_noIframeSupportedError','Su Navegador no soporta iframes.<br/>Se recomienda actualizar el navegador de Internet',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_noMediaFileFound','NO se han encontrado archivos de video/audio en el directorio del curso',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_uploadMediaMsg','Upload archivos para iniciar un proyecto de Subt&iacute;tulos.',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_projectName','Nombre del Proyecto',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_mediaName','Archivo (video/audio)',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_captionName','Archivo de Subt&iacute;tulos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_mediaWidth','Ancho',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_mediaHeight','Alto',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_newCaptionFileMsg','Crear un nuevo archivo de Subt&iacute;tulos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_existCaptionFileMsg','Usar un archivo de Subt&iacute;tulos existente',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_addProjectButtonLabel','Crear un nuevo Proyecto de Subt&iacute;tulos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_projectsNotFoundError','No se han encontrado Proyectos',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_saveProjectButtonLabel','Guardar',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_deleteProjectButtonLabel','Borrar',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_editProjectLink','Editar',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_previewProjectLink','Vista Previa',NOW(),'');
INSERT INTO `language_text` VALUES ('es-es', 'AtOpenCaps','atoc_htmlCode','C&oacute;digo HTML',NOW(),'');
