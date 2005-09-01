<?php


if($_GET['atalker_on'] == '1'){ 
	$_SESSION['atalker_on'] = '1';
	$feedback =  VOICE_ON;
	$msg->addFeedback($feedback);
	header("Location: ".$_SERVER['PHP_SELF']);

}else if($_GET['atalker_on'] == '2'){

	session_unregister('atalker_on');
	$feedback =  VOICE_OFF;
	$msg->addFeedback($feedback);
	header("Location: ".$_SERVER['PHP_SELF']);

}
echo '<div style="text-align:right;">';
if( $_SESSION['atalker_on'] == '1'){ 

	echo '<small>( Voice:<strong>On</strong> / <a href="'.$_base_href.$_SERVER['PHP_SELF'].'?atalker_on=2">Off</a></small> ';

}else if(!$_SESSION['atalker_on']){

	echo '<small>( Voice:<a href="'.$_base_href.$_SERVER['PHP_SELF'].'?atalker_on=1">On</a> / <strong>Off</strong></small>';

}

if($_GET['messages_on'] == '1'){ 
	$_SESSION['messages_on'] = '1';
	$feedback =  MESSAGE_ON;
	$msg->addFeedback($feedback);
	header("Location: ".$_SERVER['PHP_SELF']);


}else if($_GET['messages_on'] == '2'){

	session_unregister('messages_on');
	$feedback =  MESSAGE_OFF;
	$msg->addFeedback($feedback);
	header("Location: ".$_SERVER['PHP_SELF']);

}

if( $_SESSION['messages_on'] == '1'){ 
	require(AT_INCLUDE_PATH."../mods/atalker/message_reader.php");
	echo ' <small> -- Messages:<strong>On</strong> / <a href="'.$_base_href.$_SERVER['PHP_SELF'].'?messages_on=2">Off</a> )</small> ';

}else if(!$_SESSION['messages_on']){

	echo ' <small>-- Messages:<a href="'.$_base_href.$_SERVER['PHP_SELF'].'?messages_on=1">On</a> / <strong>Off</strong> )</small>';

}

echo '</div>';
?>