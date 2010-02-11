/**
 * $Id: $
 *
 * @author Laurel A. Williams
 * @copyright Copyright © 2008, ATutor, All rights reserved.
 */

(function() {
	
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
		init : function(ed, url) {			
			ed.addCommand('mceInsertTermTag', function() {
				var insert_string = '[?]' + ed.selection.getContent() + '[/?]';
				ed.selection.setContent(insert_string);
			});
			ed.addButton('insert_term_tag', {
				title : 'insert_tag.termdesc',
				cmd : 'mceInsertTermTag',
				image : url + '/img/term.png'
			});

			
			ed.addCommand('mceInsertCodeTag', function() {
				ed.selection.setContent('[code]' + ed.selection.getContent() + '[/code]');				
			});
			ed.addButton('insert_code_tag', {
				title : 'insert_tag.codedesc',
				cmd : 'mceInsertCodeTag',
				image : url + '/img/code.png'
			});

			ed.addCommand('mceInsertMediaTag', function() {
				ed.selection.setContent('[media|640|480]http://' + ed.selection.getContent() + '[/media]');				
			});
			ed.addButton('insert_media_tag', {
				title : 'insert_tag.mediadesc',
				cmd : 'mceInsertMediaTag',
				image : url + '/img/media.png'
			});

			ed.addCommand('mceInsertTexTag', function() {
				ed.selection.setContent('[tex]' + ed.selection.getContent() + '[/tex]');				
			});
			ed.addButton('insert_tex_tag', {
				title : 'insert_tag.texdesc',
				cmd : 'mceInsertTexTag',
				image : url + '/img/tex.png'
			});

	},	
		
		
		/**
		 * Returns information about the plugin as a name/value array. The
		 * current keys are longname, author, authorurl, infourl and version.
		 * 
		 * @return {Object} Name/value array containing information about the
		 *         plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Insert tag plugin',
				author : 'ATutor',
				authorurl : 'http://www.atutor.ca',
				infourl : 'http://www.atutor.ca',
				version : "0.1alpha"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('insert_tag', tinymce.plugins.Insert_tagPlugin);
})();