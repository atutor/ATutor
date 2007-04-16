<?xml version="1.0" encoding="<?php echo $this->encoding; ?>"?>
<manifest xmlns="http://www.imsglobal.org/xsd/imscp_v1p2" xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:imsqti="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="MANIFEST-<?php echo md5(time()); ?>" xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p2 http://www.imsglobal.org/xsd/imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/imsqti_v2p1.xsd">
	<metadata>
		<schema>IMS Content</schema>
		<schemaversion>1.2</schemaversion>
	</metadata>

	<organizations/>

	<resources>
		<?php foreach ($this->resources as $resource): ?>
			<resource identifier="RES-<?php echo md5($resource['href']); ?>" type="imsqti_item_xmlv2p1" href="<?php echo $resource['href']; ?>">
				<metadata/>
				<file href="<?php echo $resource['href']; ?>"/>
				<?php foreach ($resource['dependencies'] as $dependency_href): ?>
					<dependency identifierref="<?php echo md5($dependency_href); ?>"/>
				<?php endforeach; ?>
			</resource>
		<?php endforeach; ?>

		<!-- dependancies go here -->
		<?php foreach ($this->dependencies as $dependency_href): ?>
			<resource identifier="<?php echo md5($dependency_href); ?>" type="webcontent">
				<metadata/>
				<file href="resources/<?php echo $dependency_href; ?>"/>
			</resource>
		<?php endforeach; ?>
	</resources>
</manifest>