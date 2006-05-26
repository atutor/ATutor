<?php
session_start();
$enable_user_notes = true;


require(dirname(__FILE__) .'/common/vitals.inc.php');

if($_GET['lang']){
	$req_lang = stripslashes($_GET['lang']);
	session_start();
	$_SESSION['lang'] = $req_lang;
	$lang = $req_lang;
	$_available_sections = array('none' => '');
}else if($_SESSION['lang']){
	$req_lang = $_SESSION['lang'];
}
// using 401 authentication
if (key($_GET) == 'login') {
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="Administrator Login"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Wrong username/password combination.';
		exit;
	} else {
		$_POST['username'] = $_SERVER['PHP_AUTH_USER'];
		$_POST['password'] = $_SERVER['PHP_AUTH_PW'];
		$_POST['submit']   = true;
	}
	unset($_SERVER['PHP_AUTH_USER']);
	unset($_SERVER['PHP_AUTH_PW']);
}

	$config_location = '../include/config.inc.php';
	if (is_file($config_location) && is_readable($config_location)) {
		require($config_location);
		if (defined('AT_ENABLE_HANDBOOK_NOTES') && AT_ENABLE_HANDBOOK_NOTES) {
			define('AT_HANDBOOK_DB_USER', DB_USER);

			define('AT_HANDBOOK_DB_PASSWORD', DB_PASSWORD);

			define('AT_HANDBOOK_DB_DATABASE', DB_NAME);

			define('AT_HANDBOOK_DB_PORT', DB_PORT);

			define('AT_HANDBOOK_DB_HOST', DB_HOST);

			define('AT_HANDBOOK_DB_TABLE_PREFIX', TABLE_PREFIX);

			define('AT_HANDBOOK_ENABLE', false);

			if (isset($_POST['submit'])) {
				// try to validate $_POST
				// authenticate against the ATutor database if a connection can be made
				$_POST['username'] = addslashes($_POST['username']);
				$_POST['password'] = addslashes($_POST['password']);

				$db = @mysql_connect(AT_HANDBOOK_DB_HOST . ':' . AT_HANDBOOK_DB_PORT, AT_HANDBOOK_DB_USER, AT_HANDBOOK_DB_PASSWORD);
				if (@mysql_select_db(AT_HANDBOOK_DB_DATABASE, $db)) {
					$enable_user_notes = true;
				}

				// check if it's an admin login.
				$sql = "SELECT login, `privileges` FROM ".TABLE_PREFIX."admins WHERE login='$_POST[username]' AND PASSWORD(password)=PASSWORD('$_POST[password]') AND `privileges`>0";
				$result = mysql_query($sql, $db);
				if ($row = mysql_fetch_assoc($result)) {
					$_SESSION['handbook_admin'] = true;
					header('Location: '.$_SERVER['PHP_SELF']);
					exit;
				}
			} else if (isset($_GET['logout'])) {
				header('WWW-Authenticate: Basic realm="Administrator Login"');
				header('HTTP/1.0 401 Unauthorized');

				unset($_SERVER['PHP_AUTH_USER']);
				unset($_SERVER['PHP_AUTH_PW']);
				unset($_SESSION['handbook_admin']);
				session_write_close();
				header('Location: '.$_SERVER['PHP_SELF']);
				exit;
			}
		}
	}
	if (!defined('AT_HANDBOOK_ENABLE')) {
		// use local config file
		require('./config.inc.php');

		if (isset($_POST['submit'])) {
			// try to validate $_POST
			if (($_POST['username'] == AT_HANDBOOK_ADMIN_USERNAME) && ($_POST['password'] == AT_HANDBOOK_ADMIN_PASSWORD)) {
				$_SESSION['handbook_admin'] = true;
				header('Location: '.$_SERVER['PHP_SELF']);
				exit;
			}
		} else if (key($_GET) == 'logout') {
			header('WWW-Authenticate: Basic realm="Administrator Login"');
			header('HTTP/1.0 401 Unauthorized');

			unset($_SERVER['PHP_AUTH_USER']);
			unset($_SERVER['PHP_AUTH_PW']);
			unset($_SESSION['handbook_admin']);
			session_write_close();
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
	}

	if (defined('AT_HANDBOOK_ENABLE') && AT_HANDBOOK_ENABLE) {
		$db = @mysql_connect(AT_HANDBOOK_DB_HOST . ':' . AT_HANDBOOK_DB_PORT, AT_HANDBOOK_DB_USER, AT_HANDBOOK_DB_PASSWORD);
		if (@mysql_select_db(AT_HANDBOOK_DB_DATABASE, $db)) {
			$enable_user_notes = true;
		}
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php if ($req_lang) { echo $req_lang; } else { echo 'dp'; } ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php echo get_text('doc_title'); ?></title>
	<link rel="stylesheet" href="common/styles.css" type="text/css" />
</head>
<body>
<?php if ($missing_lang): ?>
	<div style="margin: 20px auto; border: 1px solid #aaf; padding: 4px; text-align: center; background-color: #eef;">
		<?php get_text('page_not_translated'); ?>
	</div>
<?php endif; ?>
<h1><?php echo get_text('doc_title'); ?></h1>
<p><?php echo get_text('doc_welcome'); ?></p>

	<ol>
		<li><a href="general/"><?php echo get_text('doc_user'); ?></a></li>
		<li><a href="admin/"><?php echo get_text('doc_admin'); ?></a></li>
		<li><a href="instructor/"><?php echo get_text('doc_instructor'); ?></a></li>
		<li><a href="developer/guidelines.html"><?php echo get_text('doc_dev'); ?></a></li>
		<li><a href="developer/modules.html"><?php echo get_text('doc_mods'); ?></a></li>
	</ol>

	<ol>
		<li><a href="http://www.atutor.ca">atutor.ca</a></li>
		<li><a href="http://www.atutor.ca/forums/">atutor.ca/forums/</a></li>
		<li><a href="http://www.atutor.ca/atutor/docs/index.php">atutor.ca/atutor/docs/</a></li>
	</ol>

<?php if ($enable_user_notes && (!isset($_SESSION['handbook_admin']) || (isset($_SESSION['handbook_admin']) && !$_SESSION['handbook_admin']))): ?>
	<div style="text-align: right;">
		<p><?php echo get_text('doc_notes_enabled');  ?></p>
	</div>
<?php elseif ($enable_user_notes): ?>

<?php echo get_text('doc_logged_in'); ?>
	

	<?php 
		$sql = "SELECT note_id, date, section, page, email, note FROM ".AT_HANDBOOK_DB_TABLE_PREFIX."handbook_notes WHERE approved=0 ORDER BY date DESC";
		$result = mysql_query($sql, $db);

	?>
	<div class="add-note">
		<h3><?php echo get_text('doc_unapproved_notes'); ?></h3>
	</div>

	<?php if ($result && (mysql_num_rows($result) > 0)): ?>
		<?php while ($row = mysql_fetch_assoc($result)): ?>
			<div class="note">
				<h5><?php echo $row['date']; ?>
					<a href="approve_note.php?id=<?php echo $row['note_id']; ?>" onclick="return confirm('<?php echo get_text('doc_approved_confirm'); ?>');"><?php echo get_text('doc_approve'); ?></a> | 
					<a href="delete_note.php?id=<?php echo $row['note_id']; ?>" onclick="return confirm('<?php echo get_text('doc_delete_confirm'); ?>');"><?php echo get_text('doc_delete'); ?></a>
				</h5>
				<h4><?php echo $row['email'];?></h4>
				<p><?php echo nl2br($row['note']); ?></p>
			</div>
		<?php endwhile; ?>
	<?php else: ?>
		<div class="note"><?php echo get_text('doc_no_notes'); ?></div>
	<?php endif; ?>

<?php endif; ?>

</body>
</html>