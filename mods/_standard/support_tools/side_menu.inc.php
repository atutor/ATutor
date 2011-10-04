<?php 
/* start output buffering: */

ob_start(); 

global $db;
global $_base_path;
global $savant;
global $_config;

if($_SESSION['prefs']['PREF_DICTIONARY'] == 1 ||
$_SESSION['prefs']['PREF_THESAURUS'] == 1 ||
$_SESSION['prefs']['PREF_ENCYCLOPEDIA'] == 1 ||
$_SESSION['prefs']['PREF_CALCULATOR'] == 1 ||
$_SESSION['prefs']['PREF_ATLAS']  == 1 ||
$_SESSION['prefs']['PREF_NOTE_TAKING']  == 1 ||
$_SESSION['prefs']['PREF_ABACAS']  == 1
){
echo "<ul>";
if($_SESSION['prefs']['PREF_DICTIONARY'] == 1){
	echo '<li><a href="'.$_config['dictionary'].'" target="scaffold" title="'._AT('dictionary')._AT('new_window').'">'._AT('dictionary').'</a></li>';
}
if($_SESSION['prefs']['PREF_ENCYCLOPEDIA'] == 1 && isset($_config['encyclopedia'])){
	echo '<li><a href="'.$_config['encyclopedia'].'" target="scaffold" title="'._AT('encyclopedia')._AT('new_window').'">'._AT('encyclopedia').'</a></li>';
}
if($_SESSION['prefs']['PREF_THESAURUS'] == 1 && isset($_config['thesaurus'])){
	echo '<li><a href="'.$_config['thesaurus'].'"  target="scaffold" title="'._AT('thesaurus')._AT('new_window').'">'._AT('thesaurus').'</a></li>';
}
if($_SESSION['prefs']['PREF_NOTE_TAKING'] == 1 && isset($_config['note_taking'])){
	echo '<li><a href="'.$_config['note_taking'].'" target="scaffold" title="'._AT('note_taking')._AT('new_window').'">'._AT('note_taking').'</a></li>';
}
if($_SESSION['prefs']['PREF_CALCULATOR'] == 1 && isset($_config['calculator'])){
	echo '<li><a href="'.$_config['calculator'].'" target="scaffold" title="'._AT('calculator')._AT('new_window').'">'._AT('calculator').'</a></li>';
}
if($_SESSION['prefs']['PREF_ATLAS'] == 1 && isset($_config['atlas'])){
	echo '<li><a href="'. $_config['atlas'].'" target="scaffold" title="'._AT('atlas')._AT('new_window').'">'._AT('atlas').'</a></li>';
}
if($_SESSION['prefs']['PREF_ABACUS'] == 1 && isset($_config['abacus'])){
	echo '<li><a href="'.$_config['abacus'].'" target="scaffold" title="'._AT('abacus')._AT('new_window').'">'._AT('abacus').'</a></li>';
}
echo "</ul>";

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('support_tools')); // the box title
$savant->display('include/box.tmpl.php');

}
?>