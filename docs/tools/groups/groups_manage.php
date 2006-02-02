<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GROUPS);


require(AT_INCLUDE_PATH.'header.inc.php');

?>
<div class="input-form">
	<div class="row">
		<input type="radio">create custom group - MANUAL. creates a SINGLE group to an existing Type.
		<p>(create a single group and choose the members you want to add to it.)</p>

		<input type="radio">create multiple groups - Automatically creates <em>n</em> groups.
		<p>(create groups to which you can add members later or create groups in which students are randomly distributed).</p>

		<input type="radio">create multiple groups - Automatically add groups to an existing Type.
		<p>(create groups based on those found in another group Type).</p>

		<input type="radio">create multiple groups - Based on an existing Group Type.
		<p>(create groups based on those found in another group Type - with Shuffle option).</p>

	</div>

	<div class="row buttons">
		<input type="submit" value="Continue" />
	</div>
</div>

<div class="input-form">
	<div class="row">
		<h3>Create A Single Group Manually</h3>
	</div>
	
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>Group Type (Project 1, Tutorials)<br />
		<select>
			<option>Tutorials - 19 Groups</option>
			<option>Project 1 - 10 Groups</option>
		</select>
	</div>

	<div class="row">
		Group Name<br />
		<input type="text" /> <input type="checkbox">Use Type convension
	</div>

	<div class="row">
		Group Description<br />
		<input type="text" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>Max Number of Students<br />
		<p>There are <em>43</em> students currently not assigned in this group type.</p>
		<input type="text">
	</div>

	<div class="row">
		Fill group<br />
		<input type="radio">Fill group with un-assigned students.<br />
		<input type="radio">Do not fill group. leave empty.<br />
		<input type="radio">Allow self-registration.<br />
	</div>

	<div class="row">Anonymous:<br />
		<input type="checkbox">Make group members anonymous/hidden from non-group members.
	</div>

	<div class="row">
		Tools<br />
		<input type="checkbox">Forum<br />
		<input type="checkbox">Calendar<br />
		<input type="checkbox">Drafting Room<br />
		<input type="checkbox">Chat<br />
	</div>

	<div class="row buttons">
		<input type="submit" value="Save" />
	</div>
</div>

<div class="input-form">
	<div class="row">
		<h3>Create Groups Automatically</h3>
	</div>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>Type of Groups (Project 1, Tutorials)<br />
		<input type="text" />
	</div>

	<div class="row">
		Group Prefix. Word or phrase all group names start with: (Group, Team..)<br />
		<input type="text" />
	</div>

	<div class="row">Default description:<br />
		<textarea></textarea>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>Number of Groups<br />
		<p>There are <em>43</em> students currently enrolled in this course.</p>
		<input type="radio"> members of students per group: <input type="text"><br />
		<input type="radio"> number of groups: <input type="text"><br />
	</div>

	<div class="row">
		Fill groups<br />
		<input type="radio">Fill groups randomly.<br />
		<input type="radio">Do not fill groups. leave empty.<br />
		<input type="radio">Allow self-registration.<br />
	</div>

	<div class="row">
		Remaining students<br />
		<input type="radio">Distribute extra students among the existing groups (Will increase group limit to accomodate extras)<br />
		<input type="radio">Place students in their own group (A new group will be created with less students than the other groups)<br />
	</div>

	<div class="row">Anonymous:<br />
		<input type="checkbox">Make group members anonymous/hidden from non-group members.
	</div>

	<div class="row">
		Tools<br />
		<input type="checkbox">Forum<br />
		<input type="checkbox">Calendar<br />
		<input type="checkbox">Drafting Room<br />
		<input type="checkbox">Chat<br />
	</div>

	<div class="row buttons">
		<input type="submit" value="Save">
	</div>

</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>