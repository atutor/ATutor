<?php
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/google_search/SOAP_Google.php');
$_custom_css = $_base_path . 'mods/_standard/google_search/module.css'; // use a custom stylesheet
$search_key = $_config['gsearch'];

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<?php 
/* 
 * Check which type of google search this is, and perform the corresponding action
 * Note:	If the key was invalid, it doesn't matter what search type the admin has chosen.
 *			It should simply just forward the query to the Google site.
 */
if (!$_config['gsearch'] || $_config['gtype']==GOOGLE_TYPE_SOAP){ ?>
	<?php
	//Search post - SOAP
	if (isset($_GET['submit'])){
		$google = new SOAP_Google($search_key);
		$search_array = array();
		$search_array['filter'] = true;	
		$search_array['query'] = stripslashes($_GET['q']);
		$search_array['maxResults'] = 10;
		$search_array['lr'] = "lang_en";

		$result = $google->search($search_array);

		if (isset($result['faultstring'])) {
			$msg->printErrorS('GOOGLE_QUERY_FAILED');
		} else if ($result) {
			echo '<h3>Search Results</h3>';
			if (is_array($result['resultElements'])) {
				echo '<ol>';

				foreach ($result['resultElements'] as $r) {
					echo '<li><a href="' . $r['URL'] . '">' . ($r['title'] ? $r['title'] : '<em>'._AT('no_title').'</em>' ) . '</a>';
					echo '<br />';
					echo '<small>'.($r['snippet'] ? $r['snippet'] : '<em>'._AT('no_content_avail').'</em>' ) .'<br /><i>'.$r['URL'].'</i></small>';
					echo '</li>';
				}

				if (count($result['resultElements']) == 10) {
					$search_array['start'] = 10;	
					$result2 = $google->search($search_array);

					if (false !== $result2) {		
						foreach ($result2['resultElements'] as $r) {
							echo '<li><a href="' . $r['URL'] . '">' . ($r['title'] ? $r['title'] : '<em>'._AT('no_title').'</em>' ) . '</a>';
							echo '<br />';
							echo '<small>'.($r['snippet'] ? $r['snippet'] : '<em>'._AT('no_content_avail').'</em>' ) .'<br /><i>'.$r['URL'].'</i></small>';
							echo '</li>';
						}
					} 
				}
				echo '</ol>';

				if (count($result2['resultElements']) == 10) {
					echo '<p><i>'._AT('top_20').'</i></p>';
				}
			} else {
				echo '<p>'._AT('none_found').'</p>';
			}
		} 
	}
	?>
	<?php if ($_config['gsearch']): ?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<?php else: ?>
		<form action="http://www.google.com/search" method="get" target="_new">
		<input type="hidden" name="hl" value="<?php echo $_SESSION['lang']; ?>" />
	<?php endif; ?>

	<div class="input-form" style="max-width: 525px">
		<div class="row">
			<?php echo _AT('google_search_txt'); ?>
			<?php if (!$_config['gsearch']): ?>
				<br /><br />
				<p><?php echo _AT('google_new_window'); ?></p>
			<?php endif; ?>
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="keywords"><?php echo _AT('search_words'); ?></label><br />
			<input type="text" name="q" size="30" id="keywords" value="<?php echo htmlspecialchars($stripslashes($_GET['q'])); ?>" />
		</div>

		<div class="row buttons">
			<input type="hidden" name="submit" value="<?php echo _AT('search'); ?>"/>
			<input type="submit"  accesskey="s" />
		</div>

		<div class="row">
			<small><?php echo _AT('powered_by_google'); ?></small>
		</div>
	</div>
	</form>

<?php
} elseif ($_config['gtype']==GOOGLE_TYPE_AJAX){  
	$side_menu_q = stripslashes($_GET['q']);	//side menu query
	include('gsearch.php');
} ?>


<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>