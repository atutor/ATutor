<?php

//
//  Copyright (c) 2011, Maths for More S.L. http://www.wiris.com
//  This file is part of WIRIS Plugin.
//
//  WIRIS Plugin is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  any later version.
//
//  WIRIS Plugin is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with WIRIS Plugin. If not, see <http://www.gnu.org/licenses/>.
//

include 'libwiris.php';
$config = wrs_loadConfig(WRS_CONFIG_FILE);
/*
$accessibleParameters = array('wirisimageservicehost', 'wirisimageserviceport', 'wirisimageservicepath', 'wirisimageserviceprotocol', 
'wirisimageserviceversion', 'wiristransparency', 'wirisimagebgcolor', 'wirisimagefontsize', 'wirisimageidentcolor', 'wirisimagenumbercolor',
 'wirisimagesymbolcolor', 'wirisimageidentmathvariant', 'wirisimagenumbermathvariant', 'wirisimagefontident', 'wirisimagefontnumber', 'wirisimagefontranges',
 'wirisformulaeditorcodebase', 'wirisformulaeditorarchive', 'wirisformulaeditorcode', 'wirisformulaeditorlang', 'wiriscascodebase', 
 'wiriscasarchive', 'wiriscasclass', 'wiriscaslanguages', 'CAS_width', 'CAS_height', 'wirisconfigurationclass', 'wirisconfigurationrefreshtime');
*/
echo json_encode($config);
?>