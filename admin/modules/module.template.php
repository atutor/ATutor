<?php

$module_xml = '<?xml version="1.0" encoding="ISO-8859-1"?> 
<module version="0.1"> 
	<name>{NAME}</name> 
	<description>{DESCRIPTION}</description> 
	<maintainers>{MAINTAINERS}	</maintainers> 
	<url>{URL}</url> 
	<license>{LICENSE}</license> 
	<release> 
		<version>{VERSION}</version> 
		<use_privilege>{USER_PRIVILEGE}</use_privilege> 
		<date>{DATE}</date> 
		<state>{STATE}</state> 
		<notes>{NOTES}</notes> 
	</release> 
</module>';

$maintainer_xml = "\n\t\t".'<maintainer> 
			<name>{NAME}</name> 
			<email>{EMAIL}</email> 
		</maintainer>';

?>