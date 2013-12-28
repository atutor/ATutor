<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
error_reporting(E_ALL ^ E_NOTICE);
@set_time_limit(0);

// set the default timezone to avoid the warning of "cannot rely on system timezone"
@date_default_timezone_set(@date_default_timezone_get());

if(function_exists('mysqli_connect')){
	define('MYSQLI_ENABLED',	1);
} 

require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
if(!is_object($msg)){
    require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
    $msg = new Message($savant);
}
/* atutor default configuration options */
/* used on: ustep1.php, step3.php, step5.php */
$_defaults['admin_username'] = ($_POST['old_path'] ? 'admin' : '');
$_defaults['admin_password'] = '';
$_defaults['admin_email'] = '';

$_defaults['site_name'] = 'Course Server';
$_defaults['header_img'] = '';
$_defaults['header_logo'] = '';
$_defaults['home_url'] = '';

$_defaults['email_notification'] = 'TRUE';
$_defaults['email_confirmation'] = 'TRUE';
$_defaults['allow_instructor_requests'] = 'TRUE';
$_defaults['auto_approve_instructors'] = 'FALSE';

$_defaults['max_file_size'] = '1048576';
$_defaults['max_course_size'] = '10485760';
$_defaults['max_course_float'] = '2097152';
$_defaults['ill_ext'] = 'exe, asp, php, php3, bat, cgi, pl, com, vbs, reg, pcd, pif, scr, bas, inf, vb, vbe, wsc, wsf, wsh';
$_defaults['cache_dir'] = '';

$_defaults['theme_categories'] = 'FALSE';
$_defaults['content_dir'] = realpath('../').DIRECTORY_SEPARATOR.'content';

$_defaults['course_backups'] = 5;

require_once(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');

	function queryFromFile($sql_file_path){
		global $db, $progress, $errors;
		
		$tables = array();

		if (!file_exists($sql_file_path)) {
			return false;
		}

		$sql_query = trim(fread(fopen($sql_file_path, 'r'), filesize($sql_file_path)));
		SqlUtility::splitSqlFile($pieces, $sql_query);

		foreach ($pieces as $piece) {
			$piece = trim($piece);
			// [0] contains the prefixed query
			// [4] contains unprefixed table name

			if ($_POST['tb_prefix'] || ($_POST['tb_prefix'] == '')) {
				$prefixed_query = SqlUtility::prefixQuery($piece, $_POST['tb_prefix']);
			} else {
				$prefixed_query = $piece;
			}

			if ($prefixed_query != false ) {
				$table = $_POST['tb_prefix'].$prefixed_query[4];
				if($prefixed_query[1] == 'CREATE TABLE'){
				    $result = queryDB($prefixed_query[0], array());
				    if($result > 0){
						$progress[] = 'Table <strong>'.$table . '</strong> created successfully.';
					} else {
						if (at_db_errno($db) == 1050) {
							$progress[] = 'Table <strong>'.$table . '</strong> already exists. Skipping.';
						} else {
							$errors[] = 'Table <strong>' . $table . '</strong> creation failed.';
						}
					}
				} elseif($prefixed_query[1] == 'INSERT INTO'){
					queryDB($prefixed_query[0], array());
				} elseif($prefixed_query[1] == 'REPLACE INTO'){
					queryDB($prefixed_query[0], array());
				} elseif($prefixed_query[1] == 'ALTER TABLE'){
				    $result = queryDB($prefixed_query[0], array());
				    if($result > 0){
						$progress[] = 'Table <strong>'.$table.'</strong> altered successfully.';
					} else {
					    if (at_db_errno($db) == 1060) 
							$progress[] = 'Table <strong>'.$table . '</strong> fields already exists. Skipping.';
						elseif (at_db_errno($db) == 1091) 
							$progress[] = 'Table <strong>'.$table . '</strong> fields already dropped. Skipping.';
						else
							$errors[] = 'Table <strong>'.$table.'</strong> alteration failed.';
					}

				} elseif($prefixed_query[1] == 'DROP TABLE'){
					queryDB($prefixed_query[1] . ' ' .$table, array());
				} elseif($prefixed_query[1] == 'UPDATE'){
					queryDB($prefixed_query[0], array());
				}
			}
		}
		return true;
	}

function print_errors( $errors ) {
	?>
	<br />
	<table border="0" class="errbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="errbox">
	<td>
		<h3 class="err"><img src="images/bad.gif" align="top" alt="" class="img" /> Error</h3>
		<?php
			echo '<ul>';
			foreach ($errors as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?>
		</td>
	</tr>
	</table>	<br />
<?php
}

function print_feedback( $feedback ) {
	?>
	<br />
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="fbkbox">
	<td><h3 class="feedback2"><img src="images/feedback.gif" align="top" alt="" class="img" /> Feedback</h3>
		<?php
			echo '<ul>';
			foreach ($feedback as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?></td>
	</tr>
	</table>
	<br />
<?php
}

function store_steps($step) {

	global $stripslashes;

	foreach($_POST as $key => $value) {
		if (substr($key, 0, strlen('step')) == 'step') {
			continue;
		} else if ($key == 'step') {
			continue;
		} else if ($key == 'action') {
			continue;
		} else if ($key == 'submit') {
			continue;
		}

		$_POST['step'.$step][$key] = urlencode($stripslashes($value));
	}
}


function print_hidden($current_step) {
	for ($i=1; $i<$current_step; $i++) {
		if (is_array($_POST['step'.$i])) {
			foreach($_POST['step'.$i] as $key => $value) {
				echo '<input type="hidden" name="step'.$i.'['.$key.']" value="'.$value.'" />'."\n";
			}
		}
	}
}

function print_progress($step) {
	global $install_steps;
	
	echo '<div class="install"><h3>Installation Progress</h3><p>';

	$num_steps = count($install_steps);
	for ($i=0; $i<$num_steps; $i++) {
		if ($i == $step) {
			echo '<strong style="margin-left: 12px; color: #006699;">Step '.$i.': '.$install_steps[$i]['name'].'</strong>';
		} else {
			echo '<small style="margin-left: 10px; color: gray;">';
			if ($step > $i) {
				echo '<img src="../images/check.gif" height="9" width="9" alt="Step Done!" /> ';
			} else {
				echo '<img src="../images/clr.gif" height="9" width="9" alt="" /> ';
			}
			echo 'Step '.$i.': '.$install_steps[$i]['name'].'</small>';
		}
		if ($i+1 < $num_steps) {
			echo '<br />';
		}
	}
	echo '</p></div><br />';

	echo '<h3>'.$install_steps[$step]['name'].'</h3>';
}


if (version_compare(phpversion(), '5.0') < 0) {
	function scandir($dirstr) {
		$files = array();
		$fh = opendir($dirstr);
		while (false !== ($filename = readdir($fh))) {
			array_push($files, $filename);
		}
		closedir($fh);
		return $files;
	}
}

/** 
 * Print the HTML of the meta forward codes
 */
function print_meta_redirect(){
	$body = 'ATutor appears to have been installed already. <br/>';
	$body .= '<a href="../index.php">Click here<a/> to login.';

	$html = "<html>\n";
	$html .= '<body>'.$body.'</body>'."\n";
	$html .= "</html>\n";

	return $html;
}

/**
 * This function calculate the ATutor installation path
 * @access  public
 * @param   include_path: The relative path to install/include
 * @return  string: atutor installation path, for example: /ATutor/
 */
function get_atutor_installation_path($include_path) {
	/* get the base url	*/
	if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) {
		$server_protocol = 'https://';
	} else {
		$server_protocol = 'http://';
	}

	$dir_deep	 = substr_count($include_path, '..');
	$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	$base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-2);
	$base_href	 = $server_protocol . implode('/', $base_href).'/';
	
	$session_path = substr($base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));
	
	return $session_path;
}

/**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
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