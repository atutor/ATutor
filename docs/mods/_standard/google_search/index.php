<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('SOAP_Google.php');

$search_key = $_config['gsearch'];

if (empty($search_key)) {
	$msg->addError('GOOGLE_KEY_EMPTY');
	require(AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<div class="input-form" style="max-width: 525px">
		<div class="row">
			<?php echo _AT('google_search_txt'); ?>
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="keywords"><?php echo _AT('search_words'); ?></label><br />
			<input type="text" name="search_query" size="30" id="keywords" value="<?php echo htmlspecialchars(stripslashes($_GET['search_query'])); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('search'); ?>" accesskey="s" />
		</div>

		<?php if (defined('HOME_URL') && HOME_URL): ?>
			<div class="row">
				<input type="checkbox" name="site_search" <?php if ($_GET['site_search']) { echo 'checked="checked"'; } ?> /><?php echo _AT('search_site', HOME_URL); ?>
			</div>
		<?php endif; ?>
		<div class="row">
			<small><?php echo _AT('powered_by_google'); ?></small>
		</div>
	</div>
</form>

<?php
if (isset($_GET['submit'])) {
	$google = new SOAP_Google($search_key);
	$search_array = array();
	$search_array['filter'] = true;	
	$search_array['query'] = stripslashes($_GET['search_query']);
	$search_array['maxResults'] = 10;
	$search_array['lr'] = "lang_en";
	if ($_GET['site_search']) {
		$search_array['query'] .= ' site:'.HOME_URL;
	}

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

require(AT_INCLUDE_PATH.'footer.inc.php');
?>