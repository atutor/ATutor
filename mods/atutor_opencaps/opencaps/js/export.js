/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

$(document).ready(function () {
	$("#export-tab").addClass('current');
});

function set_format(chosen_format) {
  document.forms[0].format.value = chosen_format;
  document.forms[0].submit() ;
}