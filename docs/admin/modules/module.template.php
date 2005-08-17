<?php

$module_xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
<module version="0.1"> 
	<name>{NAME}</name> 
	<description>{DESCRIPTION}</description> 
	<maintainers>{MAINTAINERS}
	</maintainers> 
	<url>{URL}</url>
	<release> 
		<version>{VERSION}</version> 
		<use_privilege>{USER_PRIVILEGE}</use_privilege>
		<date>{DATE}</date> 
		<license>{LICENSE}</license> 
		<state>{STATE}</state> 
		<notes>{NOTES}</notes> 
		<filelist> 
			{FILELIST}
			<file role="php" baseinstalldir="." md5sum="DHEI38DH3DF4ERf4DF5EFF" name="index.php" />
			<file role="php" baseinstalldir="." md5sum="ABCDEFG123456789789" name="module.php" />
		</filelist> 
	</release> 
</module>';

$maintainer_xml = "\n\t\t".'<maintainer> 
			<name>{NAME}</name> 
			<email>{EMAIL}</email> 
		</maintainer>';

$file = '<file role="{TYPE}" baseinstalldir="{DIR}" md5sum="{MD5}" name="{FILENAME}" />';
?>