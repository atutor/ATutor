<?php



if($_GET['atalker_on'] == '1'){ 
	$_SESSION['atalker_on'] = '1';
	header("Location: ".$_SERVER['PHP_SELF']);

}else if($_GET['atalker_on'] == '2'){

	session_unregister('atalker_on');
	header("Location: ".$_SERVER['PHP_SELF']);

}
//session_unregister('atalker_on');
if( $_SESSION['atalker_on'] == '1'){ 

	echo 'Voice:<strong>On</strong> / <a href="'.$_base_href.$_SERVER['PHP_SELF'].'?atalker_on=2">Off</a>';

}else if(!$_SESSION['atalker_on']){

	echo 'Voice:<a href="'.$_base_href.$_SERVER['PHP_SELF'].'?atalker_on=1">On</a> / <strong>Off</strong>';

}

if($_GET['messages_on'] == '1'){ 
	$_SESSION['messages_on'] = '1';
	header("Location: ".$_SERVER['PHP_SELF']);

}else if($_GET['messages_on'] == '2'){

	session_unregister('messages_on');
	header("Location: ".$_SERVER['PHP_SELF']);

}

if( $_SESSION['messages_on'] == '1'){ 
	require(AT_INCLUDE_PATH."../mods/atalker/message_reader.php");
	echo ' -- Messages:<strong>On</strong> / <a href="'.$_base_href.$_SERVER['PHP_SELF'].'?messages_on=2">Off</a>';

}else if(!$_SESSION['messages_on']){

	echo ' -- Messages:<a href="'.$_base_href.$_SERVER['PHP_SELF'].'?messages_on=1">On</a> / <strong>Off</strong>';

}

?>