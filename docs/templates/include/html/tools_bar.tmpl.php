<tr>
	<td id="tools" valign="top" colspan="2">
		<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="">
		<tr>
		<td width="20%" class="cat2" valign="top"><a name="navigation"></a><?php

		/* home */
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
			echo '<a class="white" href="'.$_base_path.'index.php?g=14" accesskey="1" title="'._AT('home').' Alt-1"><img src="'.$_base_path.'images/home.gif" class="menuimage" border="0" alt="'._AT('home').'" /></a>'."\n";
		}
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
			echo ' <a class="white" href="'.$_base_path.'index.php?g=14" accesskey="1" title="'._AT('home').' Alt-1">'._AT('home').'</a>'."\n";
		}
		echo '</td>'."\n";

		/* tools */
		echo '<td width="20%" class="cat2b">'."\n";
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
			echo '<a class="white" href="'.$_base_path.'tools/index.php?g=15" accesskey="2" title="'._AT('tools').' Alt-2"><img src="'.$_base_path.'images/tools.gif" class="menuimage"  border="0" alt="'._AT('tools').'" /></a>'."\n";
		}
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
			echo ' <a class="white" href="'.$_base_path.'tools/index.php?g=15" accesskey="2" title="'._AT('tools').' Alt-2">'._AT('tools').'</a>'."\n";
		}
		echo '</td>'."\n";

		/* resources */
		echo '<td width="20%" class="cat2c">'."\n";
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
			echo '<a class="white" href="'.$_base_path.'resources/index.php?g=16" accesskey="3" title="'._AT('resources').' Alt-3"><img src="'.$_base_path.'images/resources.gif" class="menuimage" border="0" alt="'._AT('resources').'" /></a>'."\n";
		}
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
			echo ' <a class="white" href="'.$_base_path.'resources/index.php?g=16" accesskey="3" title="'._AT('resources').' Alt-3">'._AT('resources').'</a>'."\n";
		}
		echo '</td>'."\n";

		/* discussions */
		echo '<td width="20%" class="cat2d" style="white-space:nowrap;">';
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
			echo '<a class="white" href="'.$_base_path.'discussions/index.php?g=17" accesskey="4" title="'._AT('discussions').' Alt-4"><img src="'.$_base_path.'images/discussions.gif" class="menuimage"  border="0" alt="'._AT('discussions').'" /></a>'."\n";
		}
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
			echo '<a class="white" href="'.$_base_path.'discussions/index.php?g=17" accesskey="4" title="'._AT('discussions').' Alt-4">'._AT('discussions').'</a>'."\n";
		}
		echo '</td>'."\n";

		/* help */
		echo '<td width="20%" class="cat2e">'."\n";
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 2) {
			echo '<a class="white" href="'.$_base_path.'help/index.php?g=18" accesskey="5" title="'._AT('help').' Alt-5"><img src="'.$_base_path.'images/help.gif" class="menuimage" height="25" width="28" border="0" alt="'._AT('help').'" /></a>'."\n";
		}
		if ($_SESSION['prefs'][PREF_NAV_ICONS] != 1) {
			echo ' <a class="white" href="'.$_base_path.'help/index.php?g=18" accesskey="5" title="'._AT('help').' Alt-5">'._AT('help').'</a>'."\n";
		}
		echo '</td>'."\n";
		?>
		</tr>
		</table>
	</td>
</tr>