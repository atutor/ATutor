<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

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
        $admin['returnLink'] = '<a href="'.$admin['returnL'].'">'.$admin['returnT'].'</a>';
    } else {
        $admin['returnLink'] = '';
    }

	return $admin;
}

require('chat_defaults.inc.php');
$admin = getAdminSettings();
if ($admin === 0) {
	$admin = defaultAdminSettings();
}

function postMessage($chatID, $message, &$topMsgNum, &$bottomMsgNum) {
	global $admin;

	$topMsgNum++;
	if (!is_dir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs')) {
		@mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs');
	}
	$fp = @fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$topMsgNum.'.message', 'w+');
	if (!$fp) {
		// error
		return 0;
	}

	flock($fp, LOCK_EX);
	if (!@fwrite($fp, $chatID."\n".$message."\n")) {
		return 0;
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	chmod(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$topMsgNum.'.message', 0600);

	/* the transcript: */
    if ($admin['produceTran'] > 0) {
		global $myPrefs;
		$message = htmlspecialchars($message);
        $colourT = getChatIDColour($chatID,  'whiteBlack');
        printToTran('<tr><td valign="top"><span style="color: '.$colourT.';">'.stripslashes($chatID).'</span></td><td><span style="color: '.$colourT.';">'.stripslashes($message).'</span></td></tr>');
    }
}

function printToTran($message) {
	global $admin;
	$fp = fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$admin['tranFile'], 'a');
	if ($fp) {
		fwrite($fp, $message."\n");
	}else{
		echo "nope";
		exit;
	}
	@fclose($fp);
}

function howManyMessages(&$topMsgNum, &$bottomMsgNum) {
    $topMsgNum = 0;
    $bottomMsgNum = 0;
	
	if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/')) {
		while (($file = readdir($dir)) !== false) {
			if (($file == '..') || ($file == '.')) {
				continue;
			}
			$tempNum = substr($file, 0, -strlen('.message'));
			if ($tempNum > $topMsgNum) {
				$topMsgNum = $tempNum;
			}
			if (($tempNum < $bottomMsgNum) || ($bottomMsgNum == 0)) {
				$bottomMsgNum = $tempNum;
			}
		}  
		closedir($dir);
	}
}



function getChatIDColour($chatID, $colours) {
    $refNumT1 = strlen($chatID);
    $char2T = substr($chatID, -1);
    $refNumT2 = letterToNumber($char2T);
	$char3T = substr($chatID, -2, 1);
    $refNumT3 = letterToNumber($char3T);
	$colourStr = '#';

	if (($colours == 'blackYellow')
			|| ($colours == 'blueWhite')
			|| ($colours == 'blackWhite'))
	{
		if ($refNumT1%3 == 0) { 
			$colourStr .= 'ff';
		} else if ($refNumT1%3 == 1) { 
			$colourStr .= 'cc';
		} else { 
			$colourStr .= '99';
		}

		if ($refNumT2%3 == 0) { 
			$colourStr .= 'ff';
		} else if ($refNumT2%3 == 1) { 
			$colourStr .= 'cc';
		} else { 
			$colourStr .= '99'; 
		}

		if ($refNumT3%3 == 0) { 
			$colourStr .= 'ff';
		} else if ($refNumT3%3 == 1) { 
			$colourStr .= 'cc';
		} else { 
		   $colourStr .= '99';
		}
	} else {
		if ($refNumT1%3 == 0) { 
			$colourStr .= '00';
		} else if ($refNumT1%3 == 1) { 
			$colourStr .= '33';
		} else { 
			$colourStr .= '66';
		}
		if ($refNumT2%3 == 0) { 
			$colourStr .= '00';
		} else if ($refNumT2%3 == 1) { 
			$colourStr .= '33';
		} else { 
			$colourStr .= '66'; 
		}
		if ($refNumT3%3 == 0) { 
			$colourStr .= '00';
		} else if ($refNumT3%3 == 1) {
			$colourStr .= '33';
		} else { 
			$colourStr .= '66';
		}
    }
    return $colourStr;
}

function letterToNumber($letter) {
	$letter = strtolower($letter);

    if ($letter == '0') { return 0; }
    if ($letter == '1') { return 1; }
    if ($letter == '2') { return 2; }
    if ($letter == '3') { return 3; }
    if ($letter == '4') { return 4; }
    if ($letter == '5') { return 5; }
    if ($letter == '6') { return 6; }
    if ($letter == '7') { return 7; }
    if ($letter == '8') { return 8; }
    if ($letter == '9') { return 9; }
    if ($letter == 'b') { return 10; }
    if ($letter == 'c') { return 11; }
    if ($letter == 'd') { return 12; }
    if ($letter == 'e') { return 13; }
    if ($letter == 'f') { return 14; }
    if ($letter == 'g') { return 15; }
    if ($letter == 'h') { return 16; }
    if ($letter == 'i') { return 17; }
    if ($letter == 'j') { return 18; }
    if ($letter == 'k') { return 19; }
    if ($letter == 'l') { return 20; }
    if ($letter == 'm') { return 21; }
    if ($letter == 'n') { return 22; }
    if ($letter == 'o') { return 23; }
    if ($letter == 'p') { return 24; }
    if ($letter == 'q') { return 25; }
    if ($letter == 'r') { return 26; }
    if ($letter == 's') { return 27; }
    if ($letter == 't') { return 28; }
    if ($letter == 'u') { return 30; }
    if ($letter == 'v') { return 31; }
    if ($letter == 'w') { return 32; }
    if ($letter == 'x') { return 33; }
    if ($letter == 'y') { return 34; }
    if ($letter == 'z') { return 35; }

    return 36;
}


function printStylesheet($prefs) {
    $h3SizeT = $prefs['fontSize'] + 4;
    $h4SizeT = $prefs['fontSize'] + 2;

	print "<style type=\"text/css\"><!--
    BODY { margin: 5; }
    TD { font-family: $prefs[fontFace]; font-size: $prefs[fontSize]; }
    LI { font-family: $prefs[fontFace]; font-size: $prefs[fontSize]; }
    UL { margin-left: 40; margin-right: 40; margin-top: 5; margin-bottom: 5; }
    H3 { font-size: $h3SizeT; margin: 0; font-family: $prefs[fontFace]; }
    H4 { font-size: $h4SizeT; margin: 0; font-family: $prefs[fontFace]; }
    B { font-size: $h4SizeT; }
    A { font-size: $prefs[fontSize]; font-weight: bold; color: $prefs[front]; text-decoration: underline; }
    A:hover { font-size: $prefs[fontSize]; font-weight: bold; background-color: $prefs[darkBack]; text-decoration: underline; }
    P { margin-left: 0; margin-right: 0; margin-top: 0; margin-bottom: 10; padding-left: 20; padding-right: 20; padding-top: 5; padding-bottom: 10; font-family: $prefs[fontFamily]; font-size: $prefs[fontSize]; }
    P.light { background-color: $prefs[lightBack]; font-family: $prefs[fontFace]; font-size: $prefs[fontSize]; }
    FORM { margin-left: 0; margin-right: 0; margin-top: 10; margin-bottom: 10; }
    --></style>\n";
}

function getLastAccessed($chatID) {
	$tempPrefs = getPrefs($chatID, false);
	return $tempPrefs['lastAccessed'];
}

function &defaultAdminSettings() {
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
/*
function getAdminSettings() {
	if (!file_exists('admin.settings')) {
		return 0;
	}

	$admin = array();

	$file_prefs = file('admin.settings');
	foreach ($file_prefs as $pref) {
		$pref = explode('=', $pref, 2);
		$admin[$pref[0]] = trim($pref[1]);
	}

    if ($admin['returnT'] && $admin['returnL']) {
        $admin['returnLink'] = '<a href="'.$admin['returnL'].'">'.$admin['returnT'].'</a>';
    } else {
        $admin['returnLink'] = '';
    }

	return $admin;
}
*/
function resetLastAccessed($chatID) {
	$tempPrefs = getPrefs($chatID);
	$tempPrefs['lastAccessed'] = 0;
	writePrefs($tempPrefs, $chatID);

	/*
    open(LA,">$cgiDIR"."users/$tempChatID.la") || &printError("resetLastAccessed","$!");
    flock(LA,2);
    print LA "0\n";
    close(LA);
    chmod (0666, "$cgiDIR"."users/$tempChatID.la");
	*/
}



function cleanUp() {
	global $admin;
    $msgLifeSpan			= $admin['msgLifeSpan'];
    $chatSessionLifeSpan	= $admin['chatSessionLifeSpan'];
    $chatIDLifeSpan			= $admin['chatIDLifeSpan'];

	$now = time();

	if (!$msgLifeSpan || !$chatSessionLifeSpan || !$chatIDLifeSpan) {
        echo 'Nope, something missing: '.$msgLifeSpan.', '.$chatSessionLifeSpan.', '.$chatIDLifeSpan.'<br />';
    } else {
		/* Clean up messages */
		if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/')) {
			while (($file = readdir($dir)) !== false) {
				if (substr($file, -strlen('.message')) == '.message') {
					$info = @stat(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$file);
					if ($now - $info['mtime'] > $msgLifeSpan) {
						unlink(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$file);
					}
				}
			}
		}

		/* Clean up inactive users (doesn't delete the users, just logs them out) */
		if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/')) {
			while (($file = readdir($dir)) !== false) {
				if (substr($file, -strlen('.prefs')) == '.prefs') {
					$chatName = substr($file, 0, -strlen('.prefs'));
					$la	= getLastAccessed($chatName);
					if ($now - $la > $chatSessionLifeSpan && $la > 0) {
						postMessage('system',
							'User '.$chatName.' has been logged out due to inactivity.',
							$topMsgNum,
							$bottomMsgNum);
						resetLastAccessed($chatName);

					}
				}
			}
		}
	}
}


/* @See ./history.php */
function getLower20Bound($topNum, $bottomMsgNum) {
    for ($i = $topNum; ($i-$bottomMsgNum)%20 !=0; $i--) { ; }
    return $i;
}


function showMessage($msgNum, &$prefs) {
	if (file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$msgNum.'.message')) {
		$msg = file(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$msgNum.'.message');

		$sender = trim($msg[0]);
		$msg = stripslashes(htmlspecialchars(trim($msg[1])));
        $colour = getChatIDColour($sender, $prefs['colours']);
	
        if ($msgNum > $prefs['lastRead']) {
            echo '<tr><td width="75" class="row1" align="right"><b><span style="color: '.$colour.';">'.stripslashes($sender).'</span></b>:</td><td class="row1"><b><span style="color: '.$colour.';">'.$msg.'</span></b></td></tr>';
        } else {
            echo '<tr><td width="75" class="row1" align="right"><span style="color: '.$colour.';">'.stripslashes($sender).'</span>:</td><td class="row1"><span style="color: '.$colour.';">'.$msg.'</span></td></tr>';
        }
	}

}


/* @See ./filterHistory.php */
function showMessageFiltered($msgNum, &$prefs, $chatID) {
    if (file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$msgNum.'.message')) {
		$msg = file(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/msgs/'.$msgNum.'.message');

		$sender = trim($msg[0]);
		$msg = trim($msg[1]);

        if ($sender == $chatID) {
	        $colour = getChatIDColour($sender, $prefs['colours']);
			
			if ($msgNum > $prefs['lastRead']) {
                echo '<tr><td width="75" class="row1" align="right"><b><span style="color: '.$colour.';">'.stripslashes($sender).' : </span></b></td><td class="row1"><b><span style="color: '.$colour.';">'.stripslashes($msg).'</span></b></td></tr>';
            } else {
                echo '<tr><td width="75" class="row1" align="right"><span style="color: '.$colour.';">'.stripslashes($sender).' : </span></td><td class="row1"><span style="color: '.$colour.';">'.stripslashes($msg).'</span></td></tr>';
            }
        }
    }
}

/* @See ./prefs.php */
function getAndWriteFormPrefs(&$prefs) {
    if (isset($_POST['fontSize'])) { 
		$prefs['fontSize'] = $_POST['fontSize'];
	}

    if (isset($_POST['fontFace'])) { 
		$prefs['fontFace'] = $_POST['fontFace'];
	}

    if (isset($_POST['colours'])) { 
		$prefs['colours'] = $_POST['colours'];
	}

    if (isset($_POST['navigationAidFlag'])) {
		$prefs['navigationAidFlag'] = $_POST['navigationAidFlag'];
	}

    if (isset($_POST['newestFirstFlag'])) { 
		$prefs['newestFirstFlag'] = $_POST['newestFirstFlag'];
	}

    if (isset($_POST['onlyNewFlag'])) { 
		$prefs['onlyNewFlag'] = $_POST['onlyNewFlag'];
	}

    if (isset($_POST['bingFlag'])) { 
		$prefs['bingFlag'] = $_POST['bingFlag'];
	}

    if (isset($_POST['refresh'])) { 
		$prefs['refresh'] = $_POST['refresh'];
	}

	writePrefs($prefs, $_SESSION['login']);
}


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

function clearOutOldChatPrefs() {
    /* Clear out old user names */
	$now = time();
	$return = '';
	if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/')) {
		while (($file = readdir($dir)) !== false) {
			if (substr($file, -strlen('.prefs')) == '.prefs') {
				$chatName = substr($file, 0, -strlen('.prefs'));
				$la	= @stat(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$file);
				$la = $la['mtime'];

				if ($admin['chatIDLifeSpan'] && ($now - $la > $admin['chatIDLifeSpan'])) {
					$return .= 'Automated Clean Up: Deleting old Chat ID '.$chatName.'<br />';
					deleteUser($chatName);
				}
			}
		}
	}

	return $return;
}

function deleteUser($chatName) {
    @unlink(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatName.'.prefs');

	/* the bing file */
    @unlink ('bings/'.$chatName.'.html');
}


function makeBingFile($chatName) {
	global $myPrefs, $admin;

    if (($myPrefs['refresh'] == 'manual' && $myPrefs['bingFlag'] > 0)) {

	$bing = '<html><script language="vbscript">
		option explicit
		Dim IntervalID
		Dim count
		count = 0

		sub loaded

		    IntervalID = Window.setInterval("askServer",5000)
		end sub

		sub changedF2
		    askServer
		end sub

		sub askServer
		    Dim objAsp, theFile
		    set objAsp = CreateObject("Microsoft.XMLHTTP")
		    objAsp.open "GET", "'.$admin[cgiURL].'bing.php?uselessVar=" + CStr(count) + "&chatID='.$chatName.'", false
		    objAsp.send()
		    theFile = objAsp.responsetext
		    if InStr(theFile,"yes") > 0 then
			Player.URL = "chime.wav"
		    else
		    end if
		    count = count + 1
		    document.f1.f2.value = CStr(count) + theFile
		    objAsp = 3
		    theFile = ""
		end sub

		</script>
		<body onLoad="loaded" language="vbscript">
		<form name=f1><input type=text name=f2 length="200" /></form>
		<OBJECT ID="Player" height="0" width="0" CLASSID="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6"></OBJECT>
		</body>
		</html>';    

    } else if ($myPrefs['refresh'] == 'manual' && $myPrefs['bingFlag'] > 0) {
        print "<html>
        <body bgcolor=\"$myPrefs[back]\">
        <applet code='MachineThatGoesBing.class' width='1' height='1'>
        <param name='chatID' value='$chatName' />
        <param name='url' value='chime.wav' />
        </applet></body></html>\n";
    }

	$fp = @fopen('bings/'.$chatName.'.html', 'w+');
	if ($fp) {
		flock($fp, LOCK_EX);
		if (@fwrite($fp, $bing)) {
			flock($fp, LOCK_UN);
		}
	}
	@fclose($fp);
}


function securityCheck($uniqueID) {
	global $myPrefs;

    if ($myPrefs['uniqueID'] == $uniqueID) {
        return true;
    }
    return false;
}

function printError($err1, $err2) {
    print "An error has occured. Please <a href='./login.php' target='_top'>login again</a><br />\n";
    print "$err1 <br />\n";
    print "$err2 <br />\n";
	exit;
}

?>
