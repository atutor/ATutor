<?php
session_start();
$enable_user_notes = false;

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

			define('AT_HANDBOOK_ENABLE', true);

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
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor 1.5 Documentation</title>
	<link rel="stylesheet" href="common/styles.css" type="text/css" />
</head>
<body>

<h1>ATutor Handbook</h1>
<p>Welcome to the official ATutor Handbook!</p>

	<ol>
		<li><a href="general/">General User Documentation</a></li>
		<li><a href="admin/">Administrator Documentation</a></li>
		<li><a href="instructor/">Instructor Documentation</a></li>
		<li><a href="developer/guidelines.html">Developer Guidelines &amp; Documentation</a></li>
		<li><a href="developer/modules.html">Module Development Documentation</a></li>
	</ol>

	<ol>
		<li><a href="http://www.atutor.ca">atutor.ca</a></li>
		<li><a href="http://www.atutor.ca/forums/">atutor.ca/forums/</a></li>
		<li><a href="http://www.atutor.ca/atutor/docs/index.php">atutor.ca/atutor/docs/</a></li>
	</ol>

<?php if ($enable_user_notes && (!isset($_SESSION['handbook_admin']) || (isset($_SESSION['handbook_admin']) && !$_SESSION['handbook_admin']))): ?>
	<div style="text-align: right;">
		<p>User contributed notes is <em>enabled</em>. <a href="<?php echo $_SERVER['PHP_SELF']; ?>?login">Administrator Login</a>.</p>
	</div>
<?php elseif ($enable_user_notes): ?>
	Logged in as notes moderator. <a href="<?php echo $_SERVER['PHP_SELF'];?>?logout">Log-out</a>

	<?php 
		$sql = "SELECT note_id, date, section, page, email, note FROM ".AT_HANDBOOK_DB_TABLE_PREFIX."handbook_notes WHERE approved=0 ORDER BY date DESC";
		$result = mysql_query($sql, $db);

	?>
	<div class="add-note">
		<h3>Un-Approved User Contributed Notes</h3>
	</div>

	<?php if ($result && (mysql_num_rows($result) > 0)): ?>
		<?php while ($row = mysql_fetch_assoc($result)): ?>
			<div class="note">
				<h5><?php echo $row['date']; ?>
					<a href="approve_note.php?id=<?php echo $row['note_id']; ?>" onclick="return confirm('Are you sure you want to approve this note?');">Approve</a> | 
					<a href="delete_note.php?id=<?php echo $row['note_id']; ?>" onclick="return confirm('Are you sure you want to delete this note?');">Delete</a>
				</h5>
				<h4><?php echo $row['email'];?></h4>
				<p><?php echo nl2br($row['note']); ?></p>
			</div>
		<?php endwhile; ?>
	<?php else: ?>
		<div class="note">There are no un-approved user contributed notes.</div>
	<?php endif; ?>

<?php endif; ?>

</body>
</html>