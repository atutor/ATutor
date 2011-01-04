/**
 * $Id: $
 *
 * @author Laurel A. Williams
 * @copyright Copyright © 2008, ATutor, All rights reserved.
 */

/*global tinymce*/

"use strict";
(function () {
	
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('insert_tag');
	
	tinymce.create('tinymce.plugins.Insert_tagPlugin', {

		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function (ed, url) {
		
			/**
			 * Places the cursor in the appropriate insertion point between [][/] 
			 * tags. It deletes the <span> with the id="remove_me", which was 
			 * placed between the [][/] tags, leaving the caret between 
			 * the tags as desired.
			 */
			var placeCursor = function () {
				ed.selection.select(ed.dom.select('span#remove_me')[0]);
				ed.dom.remove(ed.dom.select('span#remove_me')[0]);
			};
			
			/**
			 * A function which generates a function to insert the appropriate 
			 * tags, either [?], [code] or [tex].
			 * 
			 * Note the slightly hacky insertion of a span with id="remove_me" 
			 * which is used to place the cursor in the correct insertion point.
			 */
			var insertionFunction = function (insertionString) {
				return function () {
					if (ed.selection.isCollapsed()) {
						ed.selection.setContent('['+ insertionString + 
								']<span id="remove_me"></span>[/' + insertionString + ']');
						placeCursor();
					}
					else {
						ed.selection.setContent('['+ insertionString + ']' + 
							ed.selection.getContent() + '[/' + insertionString + ']');
					}
				}; 
			};
	
			//[?] tag 
			ed.addCommand('mceInsertTermTag', insertionFunction("?"));
			ed.addButton('insert_term_tag', {
				title : 'insert_tag.termdesc',
				cmd : 'mceInsertTermTag',
				image : url + '/img/term.png'
			});

			//[code] tag - has not been added to interface because it doesn't work
			ed.addCommand('mceInsertCodeTag', insertionFunction("code"));
			ed.addButton('insert_code_tag', {
				title : 'insert_tag.codedesc',
				cmd : 'mceInsertCodeTag',
				image : url + '/img/code.png'
			});

			//[tex] tag
			ed.addCommand('mceInsertTexTag', insertionFunction("tex"));
			ed.addButton('insert_tex_tag', {
				title : 'insert_tag.texdesc',
				cmd : 'mceInsertTexTag',
				image : url + '/img/tex.png'
			});

			//[media] tag
			// a bit more complex tag, so formed inline instead of using insertionFunction
			ed.addCommand('mceInsertMediaTag', function () {
				if (ed.selection.isCollapsed()) {
					ed.selection.setContent('[media|640|480]http://<span id="remove_me"></span>[/media]');
					placeCursor();
				}
				else {
					ed.selection.setContent('[media|640|480]http://' + 
							ed.selection.getContent() + '[/media]');
					
				}
			});
			ed.addButton('insert_media_tag', {
				title : 'insert_tag.mediadesc',
				cmd : 'mceInsertMediaTag',
				image : url + '/img/media.png'
			});

		},	
		
		
		/**
		 * Returns information about the plugin as a name/value array. The
		 * current keys are longname, author, authorurl, infourl and version.
		 * 
		 * @return {Object} Name/value array containing information about the
		 *         plugin.
		 */
		getInfo : function () {
			return {
				longname : 'Insert tag plugin',
				author : 'ATutor',
				authorurl : 'http://www.atutor.ca',
				infourl : 'http://www.atutor.ca',
				version : "0.9beta"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('insert_tag', tinymce.plugins.Insert_tagPlugin);
})();