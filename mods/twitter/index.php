<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');
require('Twitter.php');
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<div class="input-form">
<?php echo _AT('twitter_intro'); ?>
	<div class="row">
		<input type="text" name="term" id="term" value="<?php echo htmlspecialchars(stripslashes($_POST['term'])); ?>" size="50" />
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('search'); ?>" accesskey="s" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" /> 
	</div>
</div>
</form> 

<?php
if (isset($_POST['submit'])) {

	if ($_POST['term'] == '') {
		$msg->addError(array('TWITTER_ERROR', 'AT_TWITTER_ERROR'));
	}

	if (!$msg->containsErrors()) {				
		$twitter = new Twitter();
		$tweets = $twitter->search($_POST['term']);
		echo "<ul>";
		foreach($tweets['results'] as $tweet){
			echo "<li><a href='http://www.twitter.com/".$tweet['from_user']."'>@".$tweet['from_user']."</a> : ". $tweet['text']."</li>";
		}
		echo "</ul>";	
	}
}
?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
