<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* atutor default configuration options */
/* used on: ustep1.php, step3.php */
$_defaults['admin_username'] = ($_POST['old_path'] ? 'admin' : '');
$_defaults['admin_password'] = '';
$_defaults['admin_email'] = '';

$_defaults['site_name'] = 'Course Server';
$_defaults['email_notification'] = 'TRUE';
$_defaults['allow_instructor_requests'] = 'TRUE';
$_defaults['auto_approve_instructors'] = 'FALSE';

$_defaults['max_file_size'] = '1048576';
$_defaults['max_course_size'] = '10485760';
$_defaults['max_course_float'] = '2097152';
$_defaults['ill_ext'] = 'exe, asp, php, php3, bat, cgi, pl, com, vbs, reg, pcd, pif, scr, bas, inf, vb, vbe, wsc, wsf, wsh';
$_defaults['cache_dir'] = '';

require('include/classes/sqlutility.php');


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
                    if (mysql_query($prefixed_query[0],$db) !== false) {
						$progress[] = 'Table <b>'.$table . '</b> created successfully.';
                    }else{
						$errors[] = 'Table <b>' . $table . '</b> creation failed.';
                    }
                }
                elseif($prefixed_query[1] == 'INSERT INTO'){
                    mysql_query($prefixed_query[0],$db);
                }elseif($prefixed_query[1] == 'ALTER TABLE'){
                    mysql_query($prefixed_query[0],$db);
                }elseif($prefixed_query[1] == 'DROP TABLE'){
                    mysql_query($prefixed_query[0],$db);
                }
            }
        }
        return true;
    }

function print_errors( $errors ) {
	?>
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
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="fbkbox">
	<td><h3 class="good"><img src="images/feedback.gif" align="top" alt="" class="img" /> Feedback</h3>
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

		$_POST['step'.$step][$key] = urlencode(stripslashes($value));
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
	
	echo '<h4>Installation Progress</h4><p>';

		
			$num_steps = count($install_steps);
			for ($i=0; $i<$num_steps; $i++) {
				if ($i == $step) {
					echo '<b style="margin-left: 0px; font-size: 1.2em; color: #006699;">Step '.$i.': '.$install_steps[$i]['name'].'</b>';
				} else {
					echo '<small style="margin-left: 10px; font-size: 1em; color: gray;">';
					if ($step > $i) {
						echo '<img src="../images/check.gif" height="9" width="9" alt="Step Done!" /> ';
					} else {
						echo '<img src="../images/clr.gif" height="9" width="9"> ';
					}
					echo 'Step '.$i.': '.$install_steps[$i]['name'].'</small>';
				}
				
				echo '<br />';
			}

	echo '</p>';

	echo '<h3>'.$install_steps[$step]['name'].'</h3>';
}


function scandir($dirstr) {
	$files = array();
	$fh = opendir($dirstr);
	while (false !== ($filename = readdir($fh))) {
		array_push($files, $filename);
	}
	closedir($fh);
	return $files;
} 
?>