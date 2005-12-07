/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('acheck', 'en'); // <- Add a comma separated list of all supported languages

/****
 * Steps for creating a plugin from this acheck:
 *
 * 1. Change all "acheck" to the name of your plugin.
 * 2. Remove all the callbacks in this file that you don't need.
 * 3. Remove the popup.htm file if you don't need any popups.
 * 4. Add your custom logic to the callbacks you needed.
 * 5. Write documentation in a readme.txt file on how to use the plugin.
 * 6. Upload it under the "Plugins" section at sourceforge.
 *
 ****/

/**
 * Gets executed when a editor needs to generate a button.
 */
function TinyMCE_acheck_getControlHTML(control_name) {
	switch (control_name) {
		case "acheck":
			return '<img id="{$editor_id}_acheck" src="{$pluginurl}/images/acheck.gif" title="{$lang_acheck_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceacheck\', true);" />';
	}

	return "";
}

/**
 * Gets executed when a command is called.
 */
function TinyMCE_acheck_execCommand(editor_id, element, command, user_interface, value) {
	
	if (command == "mceacheck") {
			var formObj = tinyMCE.selectedInstance.formElement.form;

			if (formObj) {
				tinyMCE.triggerSave();

				// Disable all UI form elements that TinyMCE created
				for (var i=0; i<formObj.elements.length; i++) {
					var elementId = formObj.elements[i].name ? formObj.elements[i].name : formObj.elements[i].id;

					if (elementId.indexOf('mce_editor_') == 0)
						formObj.elements[i].disabled = true;
				}

				// calls the JavaScript function
				newWindowWithCode();

				// alternate method but user must include our form in body of document
//				document.accessibilityform.edittext.value = document.editorform.edittext.value;
//				document.accessibilityform.submit();

			} else
				alert("Error: No form element found.");

		return true;
	}
	
	// Pass to next handler in chain
	return false;
}

/**
 * Gets executed when the selection/cursor position was changed.
 */
function TinyMCE_acheck_handleNodeChange(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	// Deselect acheck button
	tinyMCE.switchClassSticky(editor_id + '_acheck', 'mceButtonNormal');

	// Select acheck button if parent node is a strong or b
	if (node.parentNode.nodeName == "STRONG" || node.parentNode.nodeName == "B")
		tinyMCE.switchClassSticky(editor_id + '_acheck', 'mceButtonSelected');

	return true;
}

function fileBrowserCallBack(field_name, url, type, win) {
	// This is where you insert your custom filebrowser logic
	alert("Filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);

	// Insert new URL, this would normaly be done in a popup
	win.document.forms[0].elements[field_name].value = "someurl.htm";
}

// This function is required by accessibility checker.
// Note: you must set the "name" attribute of the editor form to "editorform".
// You must also set the "name" attribute of the textarea control in the form to "edittext".
var accessWin = null;
function newWindowWithCode() {
    if (accessWin) {
        accessWin.close();
    }
    var nodeList = document.getElementsByTagName("textarea");
    var elm = nodeList.item(0);
    if (elm != null) {
        theVal = elm.value;
    }
//    var theVal = document.editorform.edittext.value;
    var theCode = '<html><body onLoad="document.accessform.submit();"> \n';
    theCode += '<h1>Submitting Code for Accessibility Checking.....</h1>\n';
    theCode += '<form action="http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/Checkacc" name="accessform" method="post"> \n';
    theCode += '<input type="hidden" name="guide" value="wcag-2-0-aaa.xml" /> \n';
    theCode += '<input type="hidden" name="type" value="form" /> \n';
    theCode += '<textarea name="edittext">' + theVal + '</textarea>\n';
    theCode += '<input type="submit" /></form> \n';  
    theCode += '</body></html> \n';
    accessWin = window.open('', 'accessWin',  '');
    accessWin.document.writeln(theCode);
    accessWin.document.close();
}

