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

exit('do not think this file gets used!');


define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$myPrefs = loadDefaultPrefs();

	$adminPass = $_REQUEST['adminPass'];
	if ($adminPass != $admin['adminPass']) {
		
		$location = 'adminLogin.php';
		Header('Location: '.$location);
		exit;
	}

	if ($_POST['submit']) {
		$admin['adminPass']				= $_POST['newAdminPass'];
		$adminPass						= $_POST['newAdminPass'];
		$admin['chatName']				= $_POST['chatName'];
		$admin['returnL']				= $_POST['returnL'];
		$admin['returnT']				= $_POST['returnT'];
		$admin['msgLifeSpan']			= $_POST['msgLifeSpan'];
		$admin['chatSessionLifeSpan']	= $_POST['chatSessionLifeSpan'];
		$admin['chatIDLifeSpan']		= $_POST['chatIDLifeSpan'];

		writeAdminSettings($admin);
	} else if ($_POST['submit2']) {

		if ($_POST['function'] == 'startTran') {
			if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['tranFile']))){
				$error ='<b><font color="red">Error: transcript filename rejected. Please ensure that it is  alphanumeric and contains no spaces.</font></b><br /><br />';
			} else {
				$admin['produceTran'] = 1;
				$admin['tranFile'] = $_POST['tranFile'] . '.html';
				writeAdminSettings($admin);

				$tran = '<h1>'.$admin['chatName'].' - Transcript</h1>';
				$tran .= '<p>Transcript Start: '.date('Y-M-d H:i').'</p>';
				$tran .= '<table border="1" cellpadding="3">';
				
				$fp = @fopen('tran/'.$admin['tranFile'], 'w+');

				@flock($fp, LOCK_EX);
				if (!@fwrite($fp, $tran)) {
					return 0;
				}
				flock($fp, LOCK_UN);

			}
		} else if ($_POST['function'] == 'stopTran') {
			$admin['produceTran'] = 0;
			writeAdminSettings($admin);
			
			$tran = '</table><p>Transcript End: '.date('Y-M-d H:i').'</p>';
			$fp = @fopen('tran/'.$admin['tranFile'], 'a');

			@flock($fp, LOCK_EX);
			if (!@fwrite($fp, $tran)) {
				return 0;
			}
			flock($fp, LOCK_UN);
		}
	} else if ($_GET['function'] == 'clearOldChatIDs') {
		$return = clearOutOldChatPrefs();
	} else if ($_POST['submit3']) {
		deleteUser($_POST['delName']);
	} else if ($_POST['submit4']) {
		if ($dir = @opendir('users/')) {
			while (($file = readdir($dir)) !== false) {
				if (substr($file, -strlen('.prefs')) == '.prefs') {
					$chatName = substr($file, 0, -strlen('.prefs'));
					deleteUser($chatName);
				}
			}
		}
	}
?>
<html>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<head>
	<title>Administrative Screen</title>
</head>
<?php
	printStylesheet($myPrefs);
?>
<body bgColor="<?php echo $myPrefs['back']; ?>" text="<?php echo $myPrefs['front']; ?>">

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="<?php echo $myPrefs['darkBack']; ?>"><h3><?php echo $admin['chatName'];?>: Administrative Settings</h3></td>
</tr>
</table>
<br />
<?php
	if ($return != '') {
		echo '<code>'.$return.'</code>';
	}
	
	if ($admin['msgLifeSpan'] < 650) {
        $m10 = ' selected ';
    } else if ($admin['msgLifeSpan'] < 950) {
        $m30 = ' selected ';
    } else if ($admin['msgLifeSpan'] < 1850) {
        $m60 = ' selected ';
    } else if ($admin['msgLifeSpan'] < 10850) {
        $m180 = ' selected ';
    } else {
        $m1D = ' selected ';
    }

    if ($admin['chatSessionLifeSpan'] < 650) {
        $s10 = ' selected ';
    } else if ($admin['chatSessionLifeSpan'] < 950) {
        $s30 = ' selected ';
    } else if ($admin['chatSessionLifeSpan'] < 1850) {
        $s60 = ' selected ';
    } else if ($admin['chatSessionLifeSpan'] < 10850) {
        $s180 = ' selected ';
    } else {
        $s1D = ' selected ';
    }
    if ($admin['chatIDLifeSpan'] < 86450) {
        $i1D = ' selected ';
    } else if ($admin['chatIDLifeSpan'] < 1728050) {
        $i20D = ' selected ';
    } else if ($admin['chatIDLifeSpan'] < 2592050) {
        $i1M = ' selected ';
    } else {
        $i1Y = ' selected ';
    } 

?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="<?php echo $myPrefs['darkBack'];?>"><h4>Administrative Settings</h4></td>
</tr>
</table>

<form name="f1" method="post" action="./admin.php">
	<input type="hidden" name="adminPass" value="<?php echo $adminPass;?>" />

<p class="light" style="margin-left: 30; margin-right: 30;">The <em>Administrative Password</em> is the password used to enter the administrative section of the site.<br />

<b>Administrative Password:</b> <input type="text" maxlength="10" name="newAdminPass" value="<?php echo $adminPass;?>" /></p>

<p class="light" style="margin-left: 30; margin-right: 30;">The <em>Chat Name</em> is used for all titles,  headings, and inline references to the A-Chat. Example context: <i>"When you send a message to the <?php echo $admin['chatName'];?>, the message is not sent immediately to other participants... "</i><br />

<b>Chat Name:</b> <input type="text" maxlength="30" name="chatName" value="<?php echo $admin['chatName'];?>" /></p>

<p class="light" style="margin-left: 30; margin-right: 30;">The <em>Return Link</em> and the <em>Return Text</em> are displayed on the login and logout screens as a link. It may be used as a link back to the site of origin. If you do not desire such a link, leave the fields blank. <i>Entering HTML code into these fields will cause errors. These fields are so structured that none is required - only a URL and the text which should be displayed are necessary.</i><br />

<b>Return Link:</b> <input type="text" size="60" name="returnL" value="<?php echo $admin['returnL'];?>" /><br />
<b>Return Text:</b> <input type="text" size="60" name="returnT" value="<?php echo $admin['returnT'];?>" /></p>

<p class="light" style="margin-left: 30; margin-right: 30;">The <em>Message Life Span</em> is the duration of time that a particular message will sit in the history before being deleted.<br />

<b>Message Life Span:</b> 
	<select name="msgLifeSpan">
		<option value="600" <?php echo $m10; ?>>10 minutes</option>
		<option value="900" <?php echo $m30; ?>>30 minutes</option>
		<option value="1800" <?php echo $m60; ?>>60 minutes</option>
		<option value="10800" <?php echo $m180; ?>>180 minutes</option>
		<option value="86400" <?php echo $m1D; ?>>1 day</option>
	</select></p>

<p class="light" style="margin-left: 30; margin-right: 30;">The <em>Chat Session Life Span</em> is the length of time that a Chat ID will appear in the User List without action by that user. This is necessary since the chat has no way of knowing if a user had exited the chat without using the logout option (e.g. by simply closing their browser). After this amount of time of inactivity, a Chat ID will be declared inactive, and will no longer appear in the User List. Note that this will not delete the users preference settings.<br />
<b>Chat Session Life Span:</b>
	<select name="chatSessionLifeSpan">
		<option value="600" <?php echo $s10; ?>>10 minutes</option>
		<option value="900" <?php echo $s30; ?>>30 minutes</option>
		<option value="1800" <?php echo $s60; ?>>60 minutes</option>
		<option value="10800" <?php echo $s180; ?>>180 minutes</option>
		<option value="86400" <?php echo $s1D; ?>>1 day</option>
	</select></p>

<p class="light" style="margin-left: 30; margin-right: 30;">The <em>Chat ID Lifespan</em> is the amount of time since the user's last login that a Chat ID and the associated preference settings will sit before being deleted. After this amount of time of innactivity, a Chat ID and associated Preference Settings will be deleted <i>only if the administrator clicks the 'Clean Up Old Chat IDs' button below</i>.<br />
<b>Chat ID Lifespan:</b>
	<select name="chatIDLifeSpan">
		<option value="86400" <?php echo $i1D; ?>>1 Day</option>
        <option value="1728000" <?php echo $i20D; ?>>20 Days</option>
        <option value="2592000" <?php echo $i1M; ?>>1 Month</option>
        <option value="31104000" <?php echo $i1Y; ?>>12 Months</option>
	</select></p>
<input type="reset" value="reset" /> <input type="submit" name="submit" value="Change Settings" /></form></p>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="<?php echo $myPrefs['darkBack'];?>"><h4>Transcripts</h4></td>
</tr>
</table>

<br /><p class="light" style="margin-left: 30; margin-right: 30;">This section of the administrative display allows you to keep a transcript of the chat in progress. Transcripts occur as HTML files in the directory <?php echo $tranDir;?> - to move or delete files, do so from there.<br /><br />

<?php
	if ($admin['produceTran'] > 0) {
		echo 'You may view the current transcript at: <a href="tran/'.$admin['tranFile'].'" target="_new">tran/'.$admin['tranFile'].'</a></p>';

        echo '<form action="./admin.php" method="post" name="f8" style="margin: 0;">';
		echo '<input type="hidden" name="function" value="stopTran" />';
    	echo '<input type="hidden" name="adminPass" value="'.$adminPass.'" />';
    	echo '<input type="submit" value="Stop Keeping a Transcript" name="submit2" /></form></p>';
    } else {
        echo '<form action="./admin.php" method="post" name="f9" style="margin: 0;">';
        echo '<input type="hidden" name="function" value="startTran" />';
    	echo '<input type="hidden" name="adminPass" value="'.$adminPass.'" />';
    	echo 'Transcript file name (alphanumeric, no file extension): ';
    	echo '<input type="text" name="tranFile" />';
    	echo '<input type="submit" value="Start Keeping a Transcript" name="submit2" /></form></p>';
    }
    if ($admin['tranFile'] && $admin['produceTran'] < 1) {
        echo '<p class="light" style="margin-left: 30; margin-right: 30;">
               Last produced transcript at: 
               <a href="tran/'.$admin['tranFile'].'" target="_new">tran/'.$admin['tranFile'].'</a>.</p>';
    }
?>
    
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td align="left" bgColor="<?php echo $myPrefs['darkBack'];?>"><h4>Manage Users</h4></td>
</tr>
</table>

<br /><p class="light" style="margin-left: 30; margin-right: 30;">Selecting the following link will cause the system to go through all Chat IDs on the system and delete all those which have not been used for the amount of time specified in the <i>Chat ID Lifespan</i> field above.<br />
<a href="./admin.php?adminPass=<?php echo $adminPass.SEP.'function=clearOldChatIDs'; ?>">Clean Up Old Chat IDs</a>.</p>
    
<p class="light" style="margin-left: 30; margin-right: 30;">Selecting the <em>Delete All Users</em> button will cause all stored Chat IDs and associated Preference settings to be deleted. Or, you may delete a particular Chat ID and associated Preferences using the <em>Delete This User</em> button below.

<form action="./admin.php" method="post" name="f2" onSubmit="return confirmDel();" style="margin: 0;">
	<input type="hidden" name="adminPass" value="<?php echo $adminPass; ?>" />
	<input type="submit" value="Delete All Users" name="submit4" /></form></p>

	<script language="javascript"><!--
		function confirmDel() {
			if (confirm("Are you sure you want to delete ALL users?")) {
				return true;
			}
			return false;
		}
	//--></script>

<p class="light" style="margin-left: 30; margin-right: 30;">
	<form action="./admin.php" onSubmit="return confirmParDel();" method="post" name="f3">
		<input type="hidden" name="adminPass" value="<?php echo $adminPass;?>" />
		<b>Delete a particular user:</b> <select name="delName">
	<?php
	if ($dir = @opendir('users/')) {
		while (($file = readdir($dir)) !== false) {
			if (($file == '..') || ($file == '.')) {
				continue;
			}

			$chatName	= substr($file, 0, -strlen('.prefs'));

			echo '<option value="'.$chatName.'">'.$chatName.'</option>';
		}
	}
	?>
    </select> <input type="submit" value="Delete this user" name="submit3" /></form></p>
	<script language="javascript"><!--
           function confirmParDel() {
               if (confirm("Are you sure you want to delete the user " + document.f3.delName[document.f3.delName.selectedIndex].value + "?")) {
                   return true;
               }
               return false;
           }
           //--></script>

</body>
</html>
