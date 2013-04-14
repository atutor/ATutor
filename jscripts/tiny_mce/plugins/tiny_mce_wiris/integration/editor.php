<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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

$wirisformulaeditorlang = '';
if (isset($_GET['lang'])) {
	$wirisformulaeditorlang = $_GET['lang'];
	$wirisformulaeditorlang = strtolower($wirisformulaeditorlang);
	$wirisformulaeditorlang = str_replace("-", "_", $wirisformulaeditorlang);
}
if (file_exists('../lang/' . $wirisformulaeditorlang . '/strings.js')){
	$config['wirisformulaeditorlang'] = $wirisformulaeditorlang;
}else if(file_exists('../lang/' . substr($wirisformulaeditorlang, 0, 2) . '/strings.js')){
		$wirisformulaeditorlang = substr($wirisformulaeditorlang, 0, 2);
		$config['wirisformulaeditorlang'] = $wirisformulaeditorlang;
}else{
	$config['wirisformulaeditorlang'] = 'en';
}
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<?php 
			$attr =  '';
			$i = 0;			
			foreach($wrs_imageConfigProperties as $key => $value){
				if (isset($config[$value])){
					if ($i != 0){
						$attr .= ',';
					}else{
						$i++;
					}

					$confVal = $config[$value];
					$confVal = str_replace('\\', '\\\\', $confVal);
					$confVal = str_replace('\'', '\\\'', $confVal);
					
					$attr .= '\'' . $key . '\' : \'' . $confVal . '\'';
				}
			}
			if ($i > 0){
				$attr = '<script type="text/javascript">window.wrs_attributes = {' . $attr . '};</script>' . "\n";
				echo $attr;
			}
		?>
		<script type="text/javascript" src="<?php echo wrs_getImageServiceURL($config, 'editor') . '?lang=' . rawurlencode($config['wirisformulaeditorlang']); ?>"></script>
		<script type="text/javascript" src="../core/editor.js"></script>
		<script type="text/javascript" src="<?php echo '../lang/' . $config['wirisformulaeditorlang'] . '/strings.js' ?>"></script>
		<title>WIRIS editor</title>
		<style type="text/css">
			/*<!--*/
			
			html,
			body,
			#container {
				height: 100%;
			}
			
			body {
				margin: 0;
			}
			
			#links {
				text-align: right;
				margin-right: 20px;
			}

			#links_rtl {
				text-align: left;
				margin-left: 20px;
			}
			
			#controls {
				float: left;
			}

			#controls_rtl {
				float: right;
			}			
			/*-->*/
		</style>
	</head>
	<body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
		<div id="container">
			<div id="editorContainer"></div>
			
			<div id="controls">
			</div>
			
			<div id="links">
				<a href="http://www.wiris.com/editor3/docs/manual/latex-support" target="_blank" id="a_latex" >LaTeX</a> | 
				<a href="http://www.wiris.com/editor3/docs/manual" target="_blank" id="a_manual" >Manual</a>
			</div>
		</div>
	</body>
</html>