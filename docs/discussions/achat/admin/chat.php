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

define('AT_INCLUDE_PATH', '../../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	//authenticate(USER_ADMIN);

	$_SECTION[0][0] = _AC('home');
	$_SECTION[0][1] = 'home.php';
	$_SECTION[1][0] = _AC('admin');
	$_SECTION[1][1] = 'admin/';
	$_SECTION[2][0] = _AC('chat_settings');
	$_SECTION[2][1] = 'admin/chat.php';

/* @See ./admin.php */
function writeAdminSettings(&$admin) {
	if (file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings')) {
		chmod(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 0755);
	}

	$fp = @fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 'w+');
	if (!$fp) {
		// error
		return 0;
	}

	$settings = '';
	foreach ($admin as $prefKey => $prefValue) {
		$settings .= $prefKey.'='.$prefValue."\n";
	}

	flock($fp, LOCK_EX);
	if (!@fwrite($fp, $settings)) {
		return 0;
	}
	flock($fp, LOCK_UN);
	chmod(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 0600);

	return 1;
}

function getAdminSettings() {
	if (!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings')) {
		return 1;
	}

	$admin = array();

	$file_prefs = file(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings');
	foreach ($file_prefs as $pref) {
		$pref = explode('=', $pref, 2);
		$admin[$pref[0]] = trim($pref[1]);
	}

    if ($admin['returnT'] && $admin['returnL']) {
        $admin['returnLink'] = '<a href="'.$admin['returnL'].'" onFocus="this.className=\'highlight\'" onBlur="this.className=\'\'">'.$admin['returnT'].'</a>';
    } else {
        $admin['returnLink'] = '';
    }

	return $admin;
}

$admin = getAdminSettings();
if ($admin === 0) {
	$admin = defaultAdminSettings();
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
		if(file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$_POST['tranFile'].'.html')){

			$warnings[] = array(AT_WARNING_CHAT_TRAN_EXISTS, $_POST['tranFile']); //'file already exists';

		}else if ($_POST['function'] == 'startTran') {
			if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['tranFile']))){
				$errors[] = AT_ERROR_CHAT_TRAN_REJECTED;
				//print_errors
				} else {
				$admin['produceTran'] = 1;
				$admin['tranFile'] = $_POST['tranFile'] . '.html';
				writeAdminSettings($admin);

				$tran = '<h4>'._AC('chat_transcript').'</h4>';
				$tran .= '<p>'._AC('chat_transcript_start').' '.date('Y-M-d H:i').'</p>';
				$tran .= '<table border="1" cellpadding="3">';
				
				$fp = @fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$admin['tranFile'], 'w+');

				@flock($fp, LOCK_EX);
				if (!@fwrite($fp, $tran)) {
					return 0;
				}
				flock($fp, LOCK_UN);

			}
		} else if ($_POST['function'] == 'stopTran') {
			$admin['produceTran'] = 0;
			writeAdminSettings($admin);
			
			$tran = '<p>'._AC('chat_transcript_end').' '.date('Y-M-d H:i').'</p>';
			$fp = @fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$admin['tranFile'], 'a');

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
		if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/')) {
			while (($file = readdir($dir)) !== false) {
				if (substr($file, -strlen('.prefs')) == '.prefs') {
					$chatName = substr($file, 0, -strlen('.prefs'));
					deleteUser($chatName);
				}
			}
		}
	}

require(AT_INCLUDE_PATH.'header.inc.php');

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

    echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/">'._AT('discussions').'</a>';
echo '</h2>';
echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/chat-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="discussions/achat/">'._AT('chat').'</a>';
echo '</h3>';


?>
<p align="center"><a href="discussions/achat/chat.php?firstLoginFlag=1" onFocus="this.className='highlight'" onBlur="this.className=''"><b> <?php echo _AC('enter_chat');  ?></b></a></p>

<h4><?php echo _AC('transcripts'); ?></h4>
<form name="f1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<p><?php echo _AC('chat_keep_tran'); ?><br /><br />

<?php
	if ($admin['produceTran'] > 0) {
		echo _AC('chat_current_tran').' <a href="discussions/achat/tran.php?t='.str_replace('.html', '', $admin['tranFile']).'" >'.str_replace('.html', '', $admin['tranFile']).'</a></p>';

        echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="f8" style="margin: 0;">';
		echo '<input type="hidden" name="function" value="stopTran" />';
    	echo '<input type="hidden" name="adminPass" value="'.$adminPass.'" />';
    	echo '<input type="submit" value="'._AC('chat_stop_tran').'" name="submit2" class="button" /></form></p>';
    } else {
        echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="f9" style="margin: 0;">';
        echo '<input type="hidden" name="function" value="startTran" />';
    	echo '<input type="hidden" name="adminPass" value="'.$adminPass.'" />';
    	echo _AC('chat_tran_file_name').' ';
    	echo '<input type="text" name="tranFile" class="formfield" />';
    	echo '<input type="submit" value="'._AC('chat_start_tran').'" name="submit2" class="button" /></form></p>';
    }
    if ($admin['tranFile'] && $admin['produceTran'] < 1) {
        echo '<p>'._AC('chat_last_tran').'
               <a href="'.$_base_href.'content/chat/'.$_SESSION['course_id'].'/tran/'.$admin['tranFile'].'" target="_new">tran/'.$admin['tranFile'].'</a>.</p>';
    }

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>