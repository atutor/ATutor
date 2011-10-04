function iframeSetHeight(id, height) {
	document.getElementById("qframe" + id).style.height = (height + 20) + "px";
}

/**
 * jQuery - Mimic confirm alert box
 * @param	DOM input element	The input submit button.
 * @param	String				The message that confirms submission
 */
function confirmSubmit(input, confirmMsg){
	input_button = jQuery(input);
	submit_row = input_button.parent();
	//jquery submit button alternation
	input_button.attr('id', 'submit_test');
	input_button.removeAttr('onclick');

	//label for the modified submit button
	input_label = jQuery('<label>').attr('for', 'submit_test');
	input_label.text(confirmMsg);

	submit_row.prepend(jQuery('<br>'));
	submit_row.prepend(input_label);
}
