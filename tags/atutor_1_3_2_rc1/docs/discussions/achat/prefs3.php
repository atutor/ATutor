<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

exit('does not get used');

	$_include_path = '../include/';
	require($_include_path.'vitals.inc.php');
	authenticate(USER_CLIENT, USER_ADMIN);
	require('include/functions.inc.php');
	$myPrefs = getPrefs($_SESSION['username']);

	if ($_POST['submit'] || $_POST['submit_p']) {
		getAndWriteFormPrefs($myPrefs);

		if ($_POST['submit_p']) {
			$location = './prefs2.php?firstLoginFlag='.$_POST['firstLoginFlag'];
		} else {
			$location = './chat.php?firstLoginFlag='.$_POST['firstLoginFlag'];
		}

		Header('Location: '.$location);
		exit;
	}
	writePrefs($myPrefs, $_SESSION['username']);

require($_include_path.'pub/header.inc.php');
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><h4>Preferences: Display Settings</h4></td>
</tr>
</table>

<form action="chat/prefs3.php" name="f1" method="post" target="_top">
	<input type="hidden" name="firstLoginFlag" value="<?php echo $_REQUEST['firstLoginFlag']; ?>" />

<!--p><table border="0" cellpadding="5" cellspacing="0">
<?php
    if ($myPrefs['fontSize'] < 11) {
       $fs10SelT = 'selected';
    } else if ($myPrefs['fontSize'] < 13) {
       $fs12SelT = 'selected';
    } else if ($myPrefs['fontSize'] < 15) {
       $fs14SelT = 'selected';
    } else {
       $fs18SelT = 'selected';
    }
?>
<tr>
	<td align="right"><b>Font Size:</b></td>
	<td><select name="fontSize">
			<option value="10" <?php echo $fs10SelT; ?>>10 pt</option>
			<option value="12" <?php echo $fs12SelT; ?>>12 pt</option>
			<option value="14" <?php echo $fs14SelT; ?>>14 pt</option>
			<option value="18" <?php echo $fs18SelT; ?>>18 pt</option>
		</select></td>
</tr>

<?php
    if ($myPrefs['fontFace'] == 'arial') {
		$fsArialSelT = 'selected';
	} else if ($myPrefs['fontFace'] == 'courier') {
		$fsCourierSelT = 'selected';
    } else {
       $fsTimesSelT = 'selected';
    }
?>
<tr>
	<td align="right"><b>Font Face:</b></td>
	<td><select name="fontFace">
			<option value="arial" <?php echo $fsArialSelT;?>>Arial</option>
			<option value="courier" <?php echo $fsCourierSelT;?>>Courier</option>
			<option value="times" <?php echo $fsTimesSelT;?>>Times</option>
		</select></td>
</tr>

<?php
	if ($myPrefs['colours'] == 'beigeBlack') {
       $c1SelT = 'selected';
    } else if ($myPrefs['colours'] == 'whiteBlack') {
       $c2SelT = 'selected';
    } else if ($myPrefs['colours'] == 'whiteBlue') {
       $c3SelT = 'selected';
    } else if ($myPrefs['colours'] == 'blackYellow') {
       $c4SelT = 'selected';
    } else if ($myPrefs['colours'] == 'blackBlue') {
       $c5SelT = 'selected';
    } else { /* blueWhite */
       $c6SelT = 'selected';
    }
?>

<tr>
	<td align="right"><b>Colour Scheme:</b></td>
	<td><select name="colours">
			<option value="beigeBlack" <?php echo $c1SelT; ?>>Black on Beige</option>
			<option value="whiteBlack" <?php echo $c2SelT; ?>>Black on White</option>
			<option value="whiteBlue" <?php echo $c3SelT; ?>>Blue on White</option>
			<option value="blackYellow" <?php echo $c4SelT; ?>>Yellow on Black</option>
			<option value="blackWhite" <?php echo $c5SelT; ?>>White on Black</option>
			<option value="blueWhite" <?php echo $c6SelT; ?>>White on Blue</option>
		</select></td>
</tr>
</table>

<p style="margin-left: 40;">These preference options allow you to set the font size, font face and colour scheme in the chat.-->

<?php
    if ($myPrefs['navigationAidFlag'] > 0) {
       $nAFSelT = 'selected';
    }
?>

<p style="margin-left: 40;"><b>Navigation Aids:</b>
	<select name="navigationAidFlag">
		<option value="0">No</option>
		<option value="1" <?php echo $nAFSelT; ?>>Yes</option>
	</select></p>

<p style="margin-left: 40;">This option allows you to turn on and off the <?php echo $admin['chatName']; ?> navigation aids, including <em>Jump To</em> links to different parts of the screen and Quick Key listings.</p>

<p style="margin-left: 40;"><i>We suggest that people using screen readers set the Navigation Aids option to "Yes".</i></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left"><input type="submit" value="Previous" name="submit_p" class="submit"  onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /> <input type="submit" value="Enter Chat" name="submit" class="submit" onFocus="this.className='submit highlight'" onBlur="this.className='submit'" /></td>
</tr>
</table>

</form>
<?php
	require($_include_path.'pub/footer.inc.php');
?>
