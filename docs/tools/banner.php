<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/cssparser.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_ADMIN);

$_section[0][0] =  _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] =  _AT('course_banner');

$msg->addInfo('HEADFOOT_DEPRECATED');

$sql="SELECT header, footer FROM ".TABLE_PREFIX."courses WHERE course_id='$_SESSION[course_id]'";
$result=mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	if ($row['header'] != '') {			
		$infos = array('HEADFOOT_DEPRECATED_DL_H', $_SERVER['PHP_SELF'].'?dl=header');
		$msg->addInfo($infos);
		
		if (isset($_GET['dl']) && $_GET['dl']=='header') {
			header('Content-Type: '.$mime[$ext]);
			header('Content-Type: application/force-download');
			header('Content-transfer-encoding: binary'); 
			header('Content-Length: '.strlen($row['header']));
			header('Content-Disposition: attachment; filename="header.html"');
			
			echo $row['header'];
			exit;
		}
	}
	if ($row['footer'] != '') {		
		$infos = array('HEADFOOT_DEPRECATED_DL_F', $_SERVER['PHP_SELF'].'?dl=footer');
		$msg->addInfo($infos);
		if (isset($_GET['dl']) && $_GET['dl']=='footer') {
			header('Content-Type: '.$mime[$ext]);
			header('Content-Type: application/force-download');
			header('Content-transfer-encoding: binary'); 
			header('Content-Length: '.strlen($row['footer']));
			header('Content-Disposition: attachment; filename="footer.html"');
			
			echo $row['footer'];
			exit;
		}
	}
}

//get default styles
$theme_info = get_theme_info($_SESSION['prefs']['PREF_THEME']);
$defaults = $theme_info['banner'];

//get vars from db
$sql	= "SELECT banner_text, banner_styles FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] ";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$banner_text_html  = $row['banner_text'];
	$b_styles		   = $row['banner_styles'];

	if ($banner_text_html == '') {
		$default_checked = 'checked = "checked"';
		$custom_checked	= '';
		$banner_text_html = $_SESSION['course_title'];
	} else {
		$default_checked = '';
		$custom_checked = 'checked = "checked"';
	}

	if ($b_styles == '') {
		//use config file		
		$banner_styles['font-family']		= $defaults['font-family'];
		$banner_styles['font-weight']		= $defaults['font-weight'];    
		$banner_styles['color']				= $defaults['color'];  
		$banner_styles['font-size']			= $defaults['font-size'];  
		$banner_styles['text-align']		= $defaults['text-align']; 

		$banner_styles['background-color']	= $defaults['background-color'];  

		$banner_styles['background-image']	= $defaults['background-image']; 
		$banner_styles['background-image']	= str_replace('url(', '', $banner_styles['background-image']);
		$banner_styles['background-image']	= str_replace(')',    '', $banner_styles['background-image']);

		$banner_styles['vertical-align']	= $defaults['vertical-align']; 
		$banner_styles['padding']			= $defaults['padding'];
	} else {
		//parse css
		$css = new cssparser();
		$css->ParseStr($b_styles);

		$style_name = '#course-banner';
		$banner_styles['font-family']		= $css->css[$style_name]['font-family'];
		$banner_styles['font-weight']		= $css->css[$style_name]['font-weight'];    
		$banner_styles['color']				= $css->css[$style_name]['color'];
		$banner_styles['font-size']			= $css->css[$style_name]['font-size'];  
		$banner_styles['text-align']		= $css->css[$style_name]['text-align']; 

		$banner_styles['background-color']	= $css->css[$style_name]['background-color'];

		$banner_styles['background-image']	= $css->css[$style_name]['background-image']; 
		$banner_styles['background-image']	= str_replace("url(", "", $banner_styles['background-image']);
		$banner_styles['background-image']	= str_replace(")", "", $banner_styles['background-image']);

		$banner_styles['vertical-align']	= $css->css[$style_name]['vertical-align']; 
		$banner_styles['padding']			= $css->css[$style_name]['padding'];

	}
}

if (isset($_POST['update'])) {
	
	/* apply the default value if the input is empty: */
	foreach($_POST['banner_styles'] as $element => $value) {
		$value = trim($value);
		if (!$value) {
			$_POST['banner_styles'][$element] = $defaults[$element];
		}
	}

	$banner_style = make_css($_POST['banner_styles']);


	//save array to db
	if (($_POST['banner_text'] == 'custom') && ($_POST['banner_text_html'] != '')) {
		$banner_text = $addslashes($_POST['banner_text_html']);
	} else {
		$banner_text = '';
	}
	$sql ="UPDATE ".TABLE_PREFIX."courses SET banner_styles='".$addslashes($banner_style)."', banner_text='".$banner_text."' WHERE course_id='$_SESSION[course_id]'";
	$result = mysql_query($sql, $db);
	
	$msg->addFeedback('BANNER_UPDATED');

	header('Location: banner.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/banner-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('course_banner');
	}
echo '</h3>';

$msg->printAll();
?>
<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="cyan"><?php echo _AT('banner_styles'); ?></th>
	</tr>
	<tr>
		<td class="row1"><?php print_popup_help('BANNER_TEXT'); ?><?php echo _AT('text'); ?>: </td>
		<td class="row1"><input type="radio" name="banner_text" value="default" <?php echo $default_checked; ?> id="default" onclick="disableCustom();" /> <label for="default"><?php echo _AT('default'); ?></label><br />
		<input type="radio" name="banner_text" value="custom" <?php echo $custom_checked; ?> id="custom" onclick="enableCustom();" /><label for="custom"><?php echo _AT('custom'); ?></label>
		<p align="center"> <textarea name="banner_text_html" rows="5" cols="50" class="formfield" id="b_text" 
		<?php 
			if ( $custom_checked == '') { 
				echo 'disabled="disabled"'; 				
			}
		?>><?php echo $banner_text_html; ?></textarea></p>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_background_colour'); ?>: </td>
		<td class="row1"><input type="text" name="banner_styles[background-color]" class="formfield" size="8" value="<?php echo $banner_styles['background-color']; ?>" /> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['background-color']; ?></code></small></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_background_image'); ?>: </td>
		<td class="row1"><input type="text" name="banner_styles[background-image]" class="formfield" size="40" value="<?php echo $banner_styles['background-image']; ?>" /> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['background-image']; ?></code></small></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_font_family'); ?>: </td>
		<td class="row1">
			<select name="banner_styles[font-family]">
				<option value='verdana, arial, helvetica, sans-serif' <?php if ($banner_styles['font-family']=='verdana, arial, sans-serif') { echo 'selected="selected"'; } ?>>Verdana, Arial, sans-serif</option>
				<option value='helvetica, arial, sans-serif' <?php if ($banner_styles['font-family']=='helvetica, arial, sans-serif') { echo 'selected="selected"'; } ?>>Helvetica, Arial, sans-serif</option>
				<option value='times, "times new roman", serif' <?php if ($banner_styles['font-family']=='times, "times new roman", serif') { echo 'selected="selected"'; } ?>>Times, "Times New Roman", serif</option>
				<option value='"courier new", courier, monospace' <?php if ($banner_styles['font-family']=='"courier new", courier, monospace') { echo 'selected="selected"'; } ?>>"Courier New", Courier, monospace</option>
			</select> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['font-family']; ?></code></small>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_font_colour'); ?>: </td>
		<td class="row1"><input type="text" name="banner_styles[color]" class="formfield" size="8" value="<?php echo $banner_styles['color']; ?>" /> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['color']; ?></code></small></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_font_size'); ?>: </td>
		<td class="row1">
			<select name="banner_styles[font-size]">
				<option value='x-small' <?php if ($banner_styles['font-size']=='x-small') { echo 'selected="selected"'; } ?>>x-small</option>
				<option value='small' <?php if ($banner_styles['font-size']=='small') { echo 'selected="selected"'; } ?>>small</option>
				<option value='medium' <?php if ($banner_styles['font-size']=='medium') { echo 'selected="selected"'; } ?>>medium</option>
				<option value='large' <?php if ($banner_styles['font-size']=='large') { echo 'selected="selected"'; } ?>>large</option>
				<option value='x-large'	<?php if ($banner_styles['font-size']=='x-large') { echo 'selected="selected"'; } ?>>x-large</option>
				<option value='xx-large' <?php if ($banner_styles['font-size']=='xx-large') { echo 'selected="selected"'; } ?>>xx-large</option>
			</select> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['font-size']; ?></code></small>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_font_weight'); ?>: </td>
		<td class="row1">
			<select name="banner_styles[font-weight]">
				<option value='lighter'	<?php if ($banner_styles['font-weight']=='lighter') { echo 'selected="selected"'; } ?>>lighter</option>
				<option value='normal' <?php if ($banner_styles['font-weight']=='normal') { echo 'selected="selected"'; } ?>>normal</option>
				<option value='bold' <?php if ($banner_styles['font-weight']=='bold') { echo 'selected="selected"'; } ?>>bold</option>
				<option value='bolder' <?php if ($banner_styles['font-weight']=='bolder') { echo 'selected="selected"'; } ?>>bolder</option>											
			</select> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['font-weight']; ?></code></small>	
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_horizontal_alignment'); ?>: </td>
		<td class="row1">
			<select name="banner_styles[text-align]">				
				<option value='left' <?php if ($banner_styles['text-align']=='left') { echo 'selected="selected"'; } ?>>left</option>
				<option value='center' <?php if ($banner_styles['text-align']=='center') { echo 'selected="selected"'; } ?>>center</option>
				<option value='right' <?php if ($banner_styles['text-align']=='right') { echo 'selected="selected"'; } ?>>right</option>
			</select> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['text-align']; ?></code></small>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_vertical_alignment'); ?>: </td>
		<td class="row1">
			<select name="banner_styles[vertical-align]">			
				<option value='top' <?php if ($banner_styles['vertical-align']=='top') { echo 'selected="selected"'; } ?>>top</option>
				<option value='middle' <?php if ($banner_styles['vertical-align']=='middle') { echo 'selected="selected"'; } ?>>middle</option>
				<option value='bottom' <?php if ($banner_styles['vertical-align']=='bottom') { echo 'selected="selected"'; } ?>>bottom</option>
			</select> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['vertical-align']; ?></code></small>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php echo _AT('css_padding'); ?>: </td>
		<td class="row1"><input type="text" name="banner_styles[padding]" class="formfield" value="<?php echo $banner_styles['padding']; ?>" size="8" /> <small><?php echo _AT('default'); ?>: <code><?php echo $defaults['padding']; ?></code></small></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td align="center" class="row1" colspan="2">		
			<input type="hidden" name="update" value="1" /><br />
			<input type="submit" name="submit" value="<?php echo _AT('save_styles'); ?> Alt-s" accesskey="s" class="button"/>
		</td>
	</tr>

</table>
</form>

<script language="javascript" type="text/javascript">
	function disableCustom() { document.form.banner_text_html.disabled = true; }
	function enableCustom()  { document.form.banner_text_html.disabled = false; }
</script>

 
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>