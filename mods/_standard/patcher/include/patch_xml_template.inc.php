<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

$patch_xml =
'<?xml version="1.0" encoding="ISO-8859-1"?> 
<patch>
	<atutor_patch_id>{ATUTOR_PATCH_ID}</atutor_patch_id>
	<applied_version>{APPLIED_VERSION}</applied_version>
	<description>{DESCRIPTION}</description>
	<dependent_patches>
{DEPENDENT_PATCHES}
	</dependent_patches>

	<sql>
{SQL}
	</sql>

	<files>
{FILES}
	</files>
</patch>';

$dependent_patch_xml = 
'		<dependent_patch>{DEPENDENT_PATCH}</dependent_patch>
';	

$patch_file_xml = 
'		<file>
			<action>{ACTION}</action>
			<name>{NAME}</name>
			<location>{LOCATION}</location>
{ACTION_DETAILS}
		</file>

';	

$patch_action_detail_xml = 
'			<action_detail>
				<type>{TYPE}</type>
				<code_from>{CODE_FROM}</code_from>
				<code_to>{CODE_TO}</code_to>
			</action_detail>

';
?>
