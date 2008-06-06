<?php
define('AT_INCLUDE_PATH', '../../include/');
include('SOAP_openmeetings.php');
include('openmeetings.inc.php');
//include('openmeetingsaudience_gateway.php');


$om = new SOAP_openmeetings('http://localhost:5080/openmeetings/services/UserService?wsdl');
//$om = new SOAP_openmeetings();
$username = 'atutor';
$password = 'atutor';

$param_login = array (	'username' => $username, 
						'userpass' => $password);

$result = $om->login($param_login);
if ($result < 0){
	debug($om->getError($result), 'error');
}

// Save user instance
$params = array(
            "username"				=> 'atutor',
            "firstname"				=> '',
		    "lastname"				=> '',
		    "profilePictureUrl"		=> '',
		    "email"					=> ''
          );
$om->saveUserInstance($params);
debug(om_getRoom($om->_sid, 1, 'Course Road'), 'adding rooms');

unset($om);


//------------- debug
function debug($var, $title='') {
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

?>