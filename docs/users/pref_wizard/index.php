<?php
define('AT_INCLUDE_PATH', '../../include/');
$_user_location = 'users';
require(AT_INCLUDE_PATH.'vitals.inc.php');

//debug($_POST);

if (isset($_POST['next']) && (is_array($_POST['pref_wiz']))) {   
	foreach ($_POST['pref_wiz'] as $pref => $template) {
	    $savant->assign('pref_template', $template);
        $savant->display('users/pref_wizard/index.tmpl.php');    
	}
} else {
    $savant->display('users/pref_wizard/index.tmpl.php');     
}


?>

<!-- 
<label for="clang">Preferred Language:</label>
<select name="clang" id="clang">
	<option value="en" selected="selected">English</option>
	<option value="fr">français</option>
	<option value="de">Deutsche</option>
	<option value="es">Española</option>
	<option value="it">Italiano</option>
	<option value="ta">Tamil</option>
	<option value="ur">Urdu</option>
</select>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="screenEnhance" id="screenEnhance">
<label for="screenEnhance">I would like to make the text on the screen
easier to see.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="structPres" id="structPres">
<label for="structPres">I would like to enhance the structure of the
content.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="structNav" id="structNav">
<label for="structNav">I would like to enhance the navigation of the
content.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="altToVisual" id="altToVisual">
<label for="altToVisual">I would like alternatives to visual content.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="altToText" id="altToText">
<label for="altToText">I would like alternatives to textual content.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="altToAudio" id="altToAudio">
<label for="altToAudio">I would like alternatives to auditory content.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="stylesheet" id="stylesheet">
<label for="stylesheet">I would like to specify the URL of my personal
stylesheet.</label>
<input
	type="checkbox" onkeypress="checkNext()" onclick="checkNext()"
	name="learnerScaffold" id="learnerScaffold">
<label for="learnerScaffold">I would like access to learner support
tools.</label>
-->
<!-- 



I want to make the text on the screen easier to see (leads to…screen enhancement options)

I want an overview of the course or the lesson (leads to…. list of lessons, table of contents, list of links?)

I want easier ways to move through the course (leads to options …. bread crumbs, previous/next button?, tab through headers)

I want alternatives to pictures and video (leads to options…. alt text and descriptions and audio alerts)

I want alternatives to sound and voice (leads to options…. captions and visual alerts)

I prefer a different language (leads to options ….how many? Dual language and option)

I want a dictionary or glossary (in what language?)

I want to be able to take notes (note taking utility in course)

I’m a visual learner (more pictures and video)

I want more detail (in text, by other means, linked, on the default screen)

1.	Can we add a question that deals with alternatives to a mouse, i.e. I want to make it easier to point and click.
The default presentation enables keyboard access to all functions. The standard enables the creation of personal keyboard shortcuts. We should discuss this further with respect to the specific scenarios.
2.	It must be less confusing is we separated some of the questions i.e. 
		I want an overview of the course
		I want an overview of the lesson 
		I want a dictionary 
		I want a glossary
Yes, definitely, any configuration of these is acceptable. 

3. The question I am a visual leaner may not be clear suggest we change it to – I would like to learn through more pictures and or videos
Yes, good rewording.

4. Can we add – I want alternatives to text….
 Yes
5. Can we add - I want less detail and more overview –
Yes
6. Can we add I want to move through the course at my speed when I want to 
This is likely a default but we should discuss
7. Can we add – I want to be able to save or download or print off sections of the course for reference later.
 This is a function of the LMS and wouldn’t need a personal preference. 




 -->
