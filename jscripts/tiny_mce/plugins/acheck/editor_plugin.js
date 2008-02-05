/* Import plugin specific language pack */

tinyMCE.importPluginLanguagePack('acheck', 'en'); // <- Add a comma separated list of all supported languages

var TinyMCE_ACheckPlugin = {
	getInfo : function() {
		return {
			longname : 'ACheck',
			author   : 'ATutor',
			authorurl : 'http://www.atutor.ca',
			infourl : 'http://www.atutor.ca',
			version : "1.0"
		};
	},

	/**
	 * Gets executed when a TinyMCE editor instance is initialized.
	 *
	 * @param {TinyMCE_Control} Initialized TinyMCE editor control instance. 
	 */
	initInstance : function(inst) {
		// You can take out plugin specific parameters
		//alert("Initialization parameter:" + tinyMCE.getParam("somename_someparam", false));

		// Register custom keyboard shortcut
		//inst.addShortcut('ctrl', 't', 'lang_somename_desc', 'mceSomeCommand');
	},

	/**
	 * Returns the HTML code for a specific control or empty string if this plugin doesn't have that control.
	 * A control can be a button, select list or any other HTML item to present in the TinyMCE user interface.
	 * The variable {$editor_id} will be replaced with the current editor instance id and {$pluginurl} will be replaced
	 * with the URL of the plugin. Language variables such as {$lang_somekey} will also be replaced with contents from
	 * the language packs.
	 *
	 * @param {string} cn Editor control/button name to get HTML for.
	 * @return HTML code for a specific control or empty string.
	 * @type string
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "acheck":
				return tinyMCE.getButtonHTML(cn, 'lang_acheck_button_desc', '{$pluginurl}/images/acheck.gif', 'mceACheck');
		}

		return "";
	},


	/**
	 * Executes a specific command, this function handles plugin commands.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that issued the command.
	 * @param {HTMLElement} element Body or root element for the editor instance.
	 * @param {string} command Command name to be executed.
	 * @param {string} user_interface True/false if a user interface should be presented.
	 * @param {mixed} value Custom value argument, can be anything.
	 * @return true/false if the command was executed by this plugin or not.
	 * @type
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceACheck":
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
					//Acheck_newWindowWithCode();
					//alert('checking');
					
					var accessWin = null;
					if (accessWin) {
						accessWin.close();
					}
					var nodeList = document.getElementsByTagName("textarea");
					var elm = nodeList.item(0);
					if (elm != null) {
						theVal = elm.value;
					}
					var theCode = '<html><body onLoad="document.accessform.submit();"> \n';
					theCode += '<h1>Submitting Code for Accessibility Checking.....</h1>\n';
					theCode += '<form action="http://checker.atrc.utoronto.ca/servlet/Checkacc" name="accessform" method="post"> \n';
					theCode += '<input type="hidden" name="guide" value="wcag-2-0-aaa.xml" /> \n';
					theCode += '<input type="hidden" name="type" value="form" /> \n';
					theCode += '<textarea name="edittext">' + theVal + '</textarea>\n';
					theCode += '<input type="submit" /></form> \n';  
					theCode += '</body></html> \n';
					accessWin = window.open('', 'accessWin',  '');
					accessWin.document.writeln(theCode);
					accessWin.document.close();
				} else
					alert("Error: No form element found.");

			return true;
		}

		// Pass to next handler in chain
		return false;
	},

	/**
	 * Gets called ones the cursor/selection in a TinyMCE instance changes. This is useful to enable/disable
	 * button controls depending on where the user are and what they have selected. This method gets executed
	 * alot and should be as performance tuned as possible.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that was changed.
	 * @param {HTMLNode} node Current node location, where the cursor is in the DOM tree.
	 * @param {int} undo_index The current undo index, if this is -1 custom undo/redo is disabled.
	 * @param {int} undo_levels The current undo levels, if this is -1 custom undo/redo is disabled.
	 * @param {boolean} visual_aid Is visual aids enabled/disabled ex: dotted lines on tables.
	 * @param {boolean} any_selection Is there any selection at all or is there only a cursor.
	 */
	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	},

	/**
	 * Gets called when a TinyMCE editor instance gets filled with content on startup.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that was filled with content.
	 * @param {HTMLElement} body HTML body element of editor instance.
	 * @param {HTMLDocument} doc HTML document instance.
	 */
	setupContent : function(editor_id, body, doc) {
	},

	/**
	 * Gets called when the contents of a TinyMCE area is modified, in other words when a undo level is
	 * added.
	 *
	 * @param {TinyMCE_Control} inst TinyMCE editor area control instance that got modified.
	 */
	onChange : function(inst) {
	},

	/**
	 * Gets called when TinyMCE handles events such as keydown, mousedown etc. TinyMCE
	 * doesn't listen on all types of events so custom event handling may be required for
	 * some purposes.
	 *
	 * @param {Event} e HTML editor event reference.
	 * @return true - pass to next handler in chain, false - stop chain execution
	 * @type boolean
	 */
	handleEvent : function(e) {
		return true;
	},

	/**
	 * Gets called when HTML contents is inserted or retrived from a TinyMCE editor instance.
	 * The type parameter contains what type of event that was performed and what format the content is in.
	 * Possible valuses for type is get_from_editor, insert_to_editor, get_from_editor_dom, insert_to_editor_dom.
	 *
	 * @param {string} type Cleanup event type.
	 * @param {mixed} content Editor contents that gets inserted/extracted can be a string or DOM element.
	 * @param {TinyMCE_Control} inst TinyMCE editor instance control that performes the cleanup.
	 * @return New content or the input content depending on action.
	 * @type string
	 */
	cleanup : function(type, content, inst) {
		return content;
	},

	// Private plugin internal methods

	/**
	 * This is just a internal plugin method, prefix all internal methods with a _ character.
	 * The prefix is needed so they doesn't collide with future TinyMCE callback functions.
	 *
	 * @param {string} a Some arg1.
	 * @param {string} b Some arg2.
	 * @return Some return.
	 * @type string
	 */
	_someInternalFunction : function(a, b) {
		return 1;
	}
};


tinyMCE.addPlugin("acheck", TinyMCE_ACheckPlugin);