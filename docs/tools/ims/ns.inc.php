<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008					*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: ims_import.php 8766 2009-08-07 01:39:05Z hwong $

/**
 * Version 1.0 -IMS Common Cartridge Final Namespaces to Schema Locations
 * http://www.imsglobal.org/cc/index.html
 * IMS test packages are different than the ones specified in the specification, which should we follow?  
 * Following the online specification = failing the test
 * Following the test = failing the online specification.
 * Both of them are from IMS CC.
 */

/*
$ns['http://www.imsglobal.org/xsd/imscp_v1p1']	=	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/imscp_v1p2.xsd';
$ns['http://www.imsglobal.org/xsd/imscc/imscp_v1p1'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/imscp_v1p2_localised.xsd';
$ns['http://www.imsglobal.org/xsd/imsccauth_v1p0'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_0/imsccauth_v1p0_localised.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM'] =		'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_1/lomLoose_localised.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM/unique'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_1/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM/vocab'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_1/vocab/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM/extend'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_1/extend/custom.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM'] = 			'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_2/lomLoose_localised.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM/unique'] =		'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_2/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM/vocab'] = 		'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_2/vocab/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM/extend'] = 		'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_2/extend/custom.xsd';
$ns['http://www.imsglobal.org/xsd/imscp_extensionv1p2'] = 'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_3/imscp_extensionv1p2_localised.xsd';
$ns['http://www.imsglobal.org/xsd/ims_qtiasiv1p2'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_4/ims_qtiasiv1p2_localised.xsd';
$ns['http://www.imsglobal.org/xsd/imswl_v1p0'] = 	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imswl_v1p0_localised.xsd';
$ns['http://www.imsglobal.org/xsd/imsdt_v1p0'] = 	'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_6/imsdt_v1p0_localised.xsd';
*/

$ns['http://www.imsglobal.org/xsd/imscp_v1p1']	=	'http://www.imsglobal.org/profile/cc/ccv1p0/imscp_v1p2.xsd';
$ns['http://www.imsglobal.org/xsd/imscc/imscp_v1p1'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/imscp_v1p2_localised.xsd';
$ns['http://www.imsglobal.org/xsd/imsccauth_v1p0'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_0/imsccauth_v1p0_localised.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM'] =		'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_2/lomLoose_localised.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM/unique'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_1/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM/vocab'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_1/vocab/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/imscc/LOM/extend'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_1/extend/custom.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM'] = 			'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_1/lomLoose_localised.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM/unique'] =		'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_2/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM/vocab'] = 		'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_2/vocab/loose.xsd';
$ns['http://ltsc.ieee.org/xsd/LOM/extend'] = 		'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_2/extend/custom.xsd';
$ns['http://www.imsglobal.org/xsd/imscp_extensionv1p2'] = 'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_3/imscp_extensionv1p2_localised.xsd';
$ns['http://www.imsglobal.org/xsd/ims_qtiasiv1p2'] =	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_4/ims_qtiasiv1p2_localised.xsd';
$ns['http://www.imsglobal.org/xsd/imswl_v1p0'] = 	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_5/imswl_v1p0_localised.xsd';
$ns['http://www.imsglobal.org/xsd/imsdt_v1p0'] = 	'http://www.imsglobal.org/profile/cc/ccv1p0/domainProfile_6/imsdt_v1p0_localised.xsd';

?>
