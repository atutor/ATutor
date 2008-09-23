<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>
<manifest xmlns="http://www.imsglobal.org/xsd/imscp_v1p2" xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:imsqti="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="MANIFEST-<?php echo md5(time()); ?>" xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p2 http://www.imsglobal.org/xsd/imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd http://www.imsglobal.org/xsd/ims_qtilitev1p2p1.xsd">
	<metadata>
		<schema>IMS Content</schema>
		<schemaversion>1.2</schemaversion>
	</metadata>
	<organizations default="">
		<organization identifier="<?php echo 'ATUTOR_'.md5($this->title); ?>" structure="hierarchical">
			<title><?php echo $this->title; ?></title>
			<item identifier="ITEM1" identifierref="RESOURCE1">
				<title><?php echo $this->title; ?></title>
			</item>
		</organization>
	</organizations>
	<resources>
		<?php
		//Respondus only supports type imsqti_xmlv1p1, qti 2.0 can use imsqti_item_xmlv1p1 as each individual items
		?>
		<resource identifier="RESOURCE1" type="imsqti_xmlv1p1" href="<?php echo $this->xml_filename; ?>">
			<metadata/>
			<file href="<?php echo $this->xml_filename; ?>"/>
			<!-- dependancies go here -->
		<?php foreach ($this->dependencies as $dependency_href): ?>
			<file href="resources/<?php echo $dependency_href; ?>"/>
		<?php endforeach; ?>
		</resource>
	</resources>
</manifest>
