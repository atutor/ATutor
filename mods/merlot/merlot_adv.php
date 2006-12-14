<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: merlot.php 6614 2006-09-27 19:32:29Z greg $

?>
<div  class="input-form" style="width: 70%; padding:5px;">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" name="merlotForm" method="get"  id="merlotForm">
<input type="hidden" name="advsearch" value="1" />
<script type="text/javascript" language="JavaScript" src="<?php echo $_base_path; ?>mods/merlot/merlot.js"></script>
<strong>   <?php echo _AT('merlot_by_keywords');  ?>:</strong>
<table>
  <tr>
	<td colspan="2" align="right">
	<small>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>"><?php echo _AT('merlot_simple');  ?></a>
	</small>
	</td>
  </tr>
  <tr>
    <td class="fieldLabel">
        <label for="keywords"><?php echo _AT('merlot_keywords');  ?></label>
    </td>
    <td>
        <input type="text" id="keywords" name="keywords" value="" />
    </td>
  </tr>
   <tr><td colspan="2"><strong><?php echo _AT('merlot_attribute');  ?>:</strong></td></tr>

  <tr>
    <td class="fieldLabel">
        <a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm", "help", "height=400,width=500,resizable=1,location=0")'><label for="title"><?php echo _AT('merlot_title');  ?></label></a>:
    </td>
    <td>
        <input type="text"  id="title" name="title" value="" /><span class="fieldError"></span>
    </td>
  </tr>

  <tr>
    <td class="fieldLabel">
        <a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm", "help", "height=400,width=500,resizable=1,location=0")'><label for="url"><?php echo _AT('merlot_url');  ?></label></a>:
    </td>
    <td>
        <input type="text" name="url" id="url" value="" /><span class="fieldError"></span>
    </td>
  </tr>
  <tr>

    <td class="fieldLabel">
        <a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm", "help", "height=400,width=500,resizable=1,location=0")'><label for="description"><?php echo _AT('merlot_description');  ?></label></a>:
    </td>
    <td>
        <input  type="text" id="description" name="description" value=""/><span class="fieldError"></span>
    </td>
  </tr>
  <tr>
    <td class="fieldLabel">
        <a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm ", "help", "height=400,width=500,resizable=1,location=0")'><label for="category"><?php echo _AT('merlot_subjects');  ?></label></a>:
    </td>
    <td>
        <span id="catPath"></span><br />
	<select name="category" id="category" onchange="javascript:subcat(-1, 'category')" style="width:400px"><option></option></select>
	<script  type="text/javascript" language='Javascript'>defaultCat('', 'category');</script>
	<span class="fieldError"></span>	
    </td>
    </tr>
    <tr>
	<td class="fieldLabel">
	<a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm ", "help", "height=400,width=500,resizable=1,location=0")'><label for="mlanguage"><?php echo _AT('merlot_language');  ?></label></a>:
	</td>
	<td>
        <select id="mlanguage" name="language" tabindex="5">
            	<option value="">Any</option>
		<option value="en">English</option>
		<option value="ar">Arabic</option>
		<option value="zh">Chinese</option>
		<option value="cs">Czech</option>
		<option value="da">Danish</option>
		<option value="nl">Dutch</option>
		<option value="fr">French</option>
		<option value="de">German</option>
		<option value="el">Greek</option>
		<option value="he">Hebrew</option>
		<option value="is">Icelandic</option>
		<option value="it">Italian</option>
		<option value="ja">Japanese</option>
		<option value="ko">Korean</option>
		<option value="la">Latin</option>
		<option value="pt">Portuguese</option>
		<option value="ru">Russian</option>
		<option value="es">Spanish</option>
		<option value="sv">Swedish</option>
		<option value="tr">Turkish</option> 
		<option value="vi">Vietnamese</option>
        </select>
        <span class="fieldError"></span>
	</td>
      </tr>
      <tr>
	<td class="fieldLabel">
    <a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm", "help", "height=400,width=500,resizable=1,location=0")' ><label for="type"><?php echo _AT('merlot_material_type');  ?></label></a>:
	</td>
	<td>
   	 <select id="type" name="materialType">
		<option value="">Any</option>
		<option value="Simulation">Simulation</option>
		<option value="Animation">Animation</option>
		<option value="Tutorial">Tutorial</option>
		<option value="Drill and Practice" >Drill and Practice</option>
		<option value="Quiz/Test">Quiz/Test</option>
		<option value="Lecture/Presentation">Lecture/Presentation</option>
		<option value="Case Study">Case Study</option>
		<option value="Collection">Collection</option>
		<option value="Reference Material">Reference Material</option>
		<option value="Learning Object Repository">Learning Object Repository</option>
	</select>
	</td>
      </tr>
        <tr>
		<td class="fieldLabel">
   		 <a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm", "help", "height=400,width=500,resizable=1,location=0")'><label for="audience"><?php echo _AT('merlot_audience');  ?></label></a>:
		</td>
		<td>
    		<select id="audience" name="audience">
      			<option value="">Any</option>
			<option value="Grade School" >Grade School</option>
			<option value="Middle School">Middle School</option>
			<option value="High School">High School</option>
			<option value="College General Ed">College General Ed</option>
			<option value="College Lower Division">College Lower Division</option>
			<option value="College Upper Division">College Upper Division</option>
			<option value="Graduate School">Graduate School</option>
			<option value="Professional">Professional</option>
 		</select>
		</td>
        </tr>
	<tr>
		<td colspan="2">	
			<strong><?php echo _AT('merlot_by_author');  ?> :</strong>
		</td>
	</tr>
	<tr>
            <td class="fieldLabel">
		<a href='javascript:popup("http://taste.merlot.org/WebHelp/MERLOT.htm", "help", "height=400,width=500,resizable=1,location=0")'><label for="name"><?php echo _AT('merlot_name');  ?></label></a>
            </td>
	<td>
        	<input type="text" id="name" name="contributorName" size="50" value="" />
	</td>
	</tr>
         <tr>
   		 <td class="buttonBar" colspan="2">
			<div class="row buttons" style="float:right;">
			<input type="submit" name="submit" value="<?php echo _AT('merlot_search'); ?>" />
			<input type="submit" name="_cancel" value="<?php echo _AT('merlot_cancel'); ?>" />
			</div>
    		</td>
  	</tr>
</table>
</form>
</div>
