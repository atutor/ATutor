<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Cindy Qi Li,			*/
/* & Harris Wong												*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$mime = array();
$mime['ez']    = array('application/andrew-inset',   '');
$mime['hqx']   = array('application/mac-binhex40',   '');
$mime['cpt']   = array('application/mac-compactpro', '');
$mime['bin']   = array('application/octet-stream',   'binary');
$mime['dms']   = array('application/octet-stream',   'binary');
$mime['lha']   = array('application/octet-stream',   'binary');
$mime['lzh']   = array('application/octet-stream',   'binary');
$mime['exe']   = array('application/octet-stream',   'binary');
$mime['com']   = array('application/octet-stream',   'binary');
$mime['class'] = array('application/octet-stream',   '');
$mime['oda']   = array('application/oda',   '');
$mime['pdf']   = array('application/pdf',   'pdf');
$mime['ai']    = array('application/postscript',   '');
$mime['eps']   = array('application/postscript',   '');
$mime['ps']    = array('application/postscript',   'ps');
$mime['rtf']   = array('application/rtf',   'rtf');
$mime['smi']   = array('application/smil',   '');
$mime['smil']  = array('application/smil',   '');
$mime['mif']   = array('application/vnd.mif',   '');
$mime['slc']   = array('application/vnd.wap.slc',   '');
$mime['sic']   = array('application/vnd.wap.sic',   '');
$mime['wmlc']  = array('application/vnd.wap.wmlc',   '');
$mime['wmlsc'] = array('application/vnd.wap.wmlscriptc',   '');
$mime['bcpio'] = array('application/x-bcpio',   '');
$mime['bz2']   = array('application/x-bzip2',   '');
$mime['vcd']   = array('application/x-cdlink',   '');
$mime['pgn']   = array('application/x-chess-pgn',   '');
$mime['cpio']  = array('application/x-cpio',   '');
$mime['csh']   = array('application/x-csh',   '');
$mime['dcr']   = array('application/x-director',   '');
$mime['dir']   = array('application/x-director',   '');
$mime['dxr']   = array('application/x-director',   '');
$mime['dvi']   = array('application/x-dvi',   'dvi');
$mime['spl']   = array('application/x-futuresplash',   '');
$mime['gtar']  = array('application/x-gtar',   '');
$mime['gz']    = array('application/x-gzip',   'zip');
$mime['tgz']   = array('application/x-gzip',   'zip');
$mime['hdf']   = array('application/x-hdf',   '');
$mime['js']    = array('application/x-javascript',   '');
$mime['kwd']   = array('application/x-kword',   '');
$mime['kwt']   = array('application/x-kword',   '');
$mime['ksp']   = array('application/x-kspread',   '');
$mime['kpr']   = array('application/x-kpresenter',   '');
$mime['kpt']   = array('application/x-kpresenter',   '');
$mime['chrt']  = array('application/x-kchart',   '');
$mime['kil']   = array('application/x-killustrator',   '');
$mime['skp']   = array('application/x-koan',   '');
$mime['skd']   = array('application/x-koan',   '');
$mime['skt']   = array('application/x-koan',   '');
$mime['skm']   = array('application/x-koan',   '');
$mime['latex'] = array('application/x-latex',   '');
$mime['nc']    = array('application/x-netcdf',   '');
$mime['cdf']   = array('application/x-netcdf',   '');
$mime['rpm']   = array('application/x-rpm',   '');
$mime['sh']    = array('application/x-sh',   '');
$mime['shar']  = array('application/x-shar',   '');
$mime['swf']   = array('application/x-shockwave-flash',   'swf');
$mime['sit']   = array('application/x-stuffit',   '');
$mime['sv4cpio'] = array('application/x-sv4cpio',   '');
$mime['sv4crc']  = array('application/x-sv4crc',   '');
$mime['tar']   = array('application/x-tar',   '');
$mime['tcl']   = array('application/x-tcl',   '');
$mime['tex']   = array('application/x-tex',   '');
$mime['texinfo'] = array('application/x-texinfo',   '');
$mime['texi']  = array('application/x-texinfo',   '');
$mime['t']     = array('application/x-troff',   '');
$mime['tr']    = array('application/x-troff',   '');
$mime['roff']  = array('application/x-troff',   '');
$mime['man']   = array('application/x-troff-man',   '');
$mime['me']    = array('application/x-troff-me',   '');
$mime['ms']    = array('application/x-troff-ms',   '');
$mime['ustar'] = array('application/x-ustar',   '');
$mime['src']   = array('application/x-wais-source',   'src');
$mime['zip']   = array('application/zip',   'zip');

$mime['ogg']   = array('audio/x-ogg',  'audio');
$mime['au']    = array('audio/basic',  'audio');
$mime['snd']   = array('audio/basic',  'audio');
$mime['mid']   = array('audio/midi',   'audio');
$mime['midi']  = array('audio/midi',   'audio');
$mime['kar']   = array('audio/midi',   'audio');
$mime['mpga']  = array('audio/mpeg',   'audio');
$mime['mp2']   = array('audio/mpeg',   'audio');
$mime['mp3']   = array('audio/mpeg',   'audio');
$mime['aif']   = array('audio/x-aiff',   'audio');
$mime['aiff']  = array('audio/x-aiff',   'audio');
$mime['aifc']  = array('audio/x-aiff',   'audio');
$mime['ram']   = array('audio/x-pn-realaudio',   'audio');
$mime['rm']    = array('audio/x-pn-realaudio',   'audio');
$mime['ra']    = array('audio/x-realaudio',   'audio');
$mime['wav']   = array('audio/x-wav',   'audio');

$mime['pdb']   = array('chemical/x-pdb',   '');
$mime['xyz']   = array('chemical/x-pdb',   '');

$mime['gif']   = array('image/gif',   'image');
$mime['ief']   = array('image/ief',   'image');
$mime['jpeg']  = array('image/jpeg',   'image');
$mime['jpg']   = array('image/jpeg',   'image');
$mime['jpe']   = array('image/jpeg',   'image');
$mime['png']   = array('image/png',   'image');
$mime['tiff']  = array('image/tiff',   'image');
$mime['tif']   = array('image/tiff',   'image');
$mime['wbmp']  = array('image/vnd.wap.wbmp',   'image');
$mime['bmp']   = array('image/ms-bmp', 'image');
$mime['ras']   = array('image/x-cmu-raster',   'image');
$mime['pnm']   = array('image/x-portable-anymap',   'image');
$mime['pbm']   = array('image/x-portable-bitmap',   'image');
$mime['pgm']   = array('image/x-portable-graymap',   'image');
$mime['ppm']   = array('image/x-portable-pixmap',   'image');
$mime['rgb']   = array('image/x-rgb',   'image');
$mime['xbm']   = array('image/x-xbitmap',   'image');
$mime['xpm']   = array('image/x-xpixmap',   'image');
$mime['xwd']   = array('image/x-xwindowdump',   'image');
$mime['igs']   = array('model/iges',   '');
$mime['iges']  = array('model/iges',   '');
$mime['msh']   = array('model/mesh',   '');
$mime['mesh']  = array('model/mesh',   '');
$mime['silo']  = array('model/mesh',   '');
$mime['wrl']   = array('model/vrml',   '');
$mime['vrml']  = array('model/vrml',   '');
$mime['css']   = array('text/css',   '');
$mime['asc']   = array('text/plain',   '');
$mime['txt']   = array('text/plain',   'txt');
$mime['sql']   = array('text/plain',   'sql2');
$mime['rtx']   = array('text/richtext',   '');
$mime['sgml']  = array('text/sgml',   '');
$mime['sgm']   = array('text/sgml',   '');
$mime['tsv']   = array('text/tab-separated-values',   '');
$mime['sl']    = array('text/vnd.wap.sl',   '');
$mime['si']    = array('text/vnd.wap.si',   '');
$mime['wml']   = array('text/vnd.wap.wml',   '');
$mime['wmls']  = array('text/vnd.wap.wmlscript',   '');
$mime['etx']   = array('text/x-setext',   '');
$mime['xml']   = array('text/xml',   'xml');
$mime['mpeg']  = array('video/mpeg',   'video');
$mime['mpg']   = array('video/mpeg',   'video');
$mime['mpe']   = array('video/mpeg',   'video');
$mime['mp4']   = array('video/mp4',   'video');
$mime['qt']    = array('video/quicktime',   'qt');
$mime['mov']   = array('video/quicktime',   'qt');
$mime['wmv']   = array('video/x-ms-wmv',   'video');
$mime['avi']   = array('video/x-msvideo',   'video');
$mime['movie'] = array('video/x-sgi-movie',  'video');
$mime['ice']   = array('x-conference/x-cooltalk',   '');
$mime['html']  = array('text/html',   '');
$mime['htm']   = array('text/html',   '');
$mime['log']   = array('text/plain',   '');
$mime['csv']   = array('text/plain',   'xls');

// microsoft office
$mime['xls']   = array('application/msexcel',   'xls');
$mime['doc']   = array('application/msword',    'doc');
$mime['mdb']   = array('application/msaccess',  'mdb');
$mime['vsd']   = array('application/visio',     'vsd');
$mime['mpp']   = array('application/msproject', 'mpp');
$mime['ppt']   = array('application/vnd.ms-powerpoint',   'ppt');

// open office
$mime['oot']   = array('application/x-vnd.oasis.openoffice.text',   'oot');
$mime['ott']   = array('application/x-vnd.oasis.openoffice.text',   'oot');
$mime['oos']   = array('application/x-vnd.oasis.openoffice.spreadsheet',   'oos');
$mime['ots']   = array('application/x-vnd.oasis.openoffice.spreadsheet',   'oos');
$mime['ood']   = array('application/x-vnd.oasis.openoffice.drawing',   'ood');
$mime['otd']   = array('application/x-vnd.oasis.openoffice.drawing',   'ood');
$mime['oop']   = array('application/x-vnd.oasis.openoffice.presentation',   'oop');
$mime['otp']   = array('application/x-vnd.oasis.openoffice.presentation',   'oop');

$mime['psd']   = array('image/x-photoshop', 'psd');
?>