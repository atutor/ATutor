<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


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
		return 0;
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

function defaultAdminSettings() {
	$admin = array();

    //$admin['cgiURL'] = 'http://dev.atutor.ca/chat/';
    //$admin['htmlDir'] = '/usr/webserver/content/snow/chat/';
    //$admin['htmlURL'] = 'http://dev.atutor.ca/discussions/achat/';
    $admin['msgLifeSpan']		= 1800;		/* 30 min  */
    $admin['chatIDLifeSpan']	= 2678400;	/* 1 month */
    $admin['chatSessionLifeSpan'] = 3600;	/* 1 hour  */
    //$admin['chatName'] = 'Accessible Chat';
    //$admin['chatIDListFlag'] = 0;
   // $admin['returnL'] = 'http://dev.atutor.ca';
    //$admin['returnT'] = 'Return to the ATRC';
    //$admin['adminPass'] = 'temppass';

	return $admin;
}

$admin = getAdminSettings();
if ($admin === 0) {
	$admin = defaultAdminSettings();
}

if (isset($_POST['submit'])) {
	$admin['adminPass']				= $_POST['newAdminPass'];
	$adminPass						= $_POST['newAdminPass'];
	$admin['chatName']				= $_POST['chatName'];
	$admin['returnL']				= $_POST['returnL'];
	$admin['returnT']				= $_POST['returnT'];
	$admin['msgLifeSpan']			= $_POST['msgLifeSpan'];
	$admin['chatSessionLifeSpan']	= $_POST['chatSessionLifeSpan'];
	$admin['chatIDLifeSpan']		= $_POST['chatIDLifeSpan'];
	writeAdminSettings($admin);

} else if (isset($_POST['submit2'])) {
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'] . '/tran');
	if(file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$_POST['tranFile'].'.html')){
		$warnings = array('CHAT_TRAN_EXISTS', $_POST['tranFile']); //'file already exists';
		$msg->addWarning($warnings);
	} else if ($_POST['function'] == 'startTran') {
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $_POST['tranFile']))){
			$msg->addError('CHAT_TRAN_REJECTED');
		} else {
			$admin['produceTran'] = 1;
			$admin['tranFile'] = $_POST['tranFile'] . '.html';
			writeAdminSettings($admin);
			$tran = '<p>'._AT('chat_transcript_start').' '.date('Y-M-d H:i').'</p>';
			$tran .= '<table border="0" cellpadding="3" summary="" class="chat-transcript">';
				
			$fp = fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$admin['tranFile'], 'w+');

			flock($fp, LOCK_EX);
			if (!fwrite($fp, $tran)) {
				return 0;
			}
			flock($fp, LOCK_UN);

			header('Location: index.php');
			exit;
		}
	} else if ($_POST['function'] == 'stopTran') {
		$admin['produceTran'] = 0;
		writeAdminSettings($admin);
			
		$tran = '<p>'._AT('chat_transcript_end').' '.date('Y-M-d H:i').'</p>';
		$fp = @fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$admin['tranFile'], 'a');

		@flock($fp, LOCK_EX); 
		if (!@fwrite($fp, $tran)) {
			return 0;
		}
		flock($fp, LOCK_UN);

		header('Location: index.php');
		exit;
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

//check chat directory
if (!@opendir(AT_CONTENT_DIR . 'chat/')){
	mkdir(AT_CONTENT_DIR . 'chat/', 0777);
}

if(!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings')){
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'], 0777);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/', 0776);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/', 0776);
	@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/', 0776);
	@copy('admin.settings.default', AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings');
	@chmod (AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/admin.settings', 0777);

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
?>

<form name="f1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<?php echo _AT('chat_keep_tran'); ?>
	</div>

<?php
   	echo '<input type="hidden" name="adminPass" value="'.$adminPass.'" />';

	if ($admin['produceTran'] > 0) {
		echo '<input type="hidden" name="function" value="stopTran" />';
		echo '<div class="row">';
			echo _AT('chat_current_tran').' <a href="mods/_standard/chat/view_transcript.php?t='.str_replace('.html', '', $admin['tranFile']).'" >'.str_replace('.html', '', $admin['tranFile']).'</a>.</p>';
		echo '</div>';

		echo '<div class="row buttons">';
	    	echo '<input type="submit" value="'._AT('chat_stop_tran').'" name="submit2" />';
		echo '</div>';

    } else {
        echo '<input type="hidden" name="function" value="startTran" />';

		echo '<div class="row">';
			echo _AT('chat_tran_file_name').' ';
			echo '<input type="text" name="tranFile" class="formfield" />';
		echo '</div>';		

		echo '<div class="row buttons">';
    		echo '<input type="submit" value="'._AT('chat_start_tran').'" name="submit2" />';
		echo '</div>';
    }
	echo '</div>';
	echo '</form>';
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>