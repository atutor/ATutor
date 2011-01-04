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
	tinymce.PluginManager.requireLangPack('swap_toolbar');
	
	tinymce.create('tinymce.plugins.Swap_toolbarPlugin', {
		
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function (ed, url) {			
			
			ed.addCommand('mceSwapToComplex', function () {
				tinyMCE.execCommand('mceRemoveControl', false, ed.id);
				ATutor.tinymce.initComplex();
                tinyMCE.execCommand('mceAddControl', false, ed.id);
                jQuery("#complexeditor").val('1');
			});
			
			ed.addButton('swap_toolbar_complex', {
				title : 'swap_toolbar_complex.desc',
				cmd : 'mceSwapToComplex',
				image : url + '/img/bullet_arrow_down.png'
			});
			
			ed.addCommand('mceSwapToSimple', function () {
				tinyMCE.execCommand('mceRemoveControl', false, ed.id);
				ATutor.tinymce.initSimple();
                tinyMCE.execCommand('mceAddControl', false, ed.id);
                jQuery("#complexeditor").val('0');
			});
			
			ed.addButton('swap_toolbar_simple', {
				title : 'swap_toolbar_simple.desc',
				cmd : 'mceSwapToSimple',
				image : url + '/img/bullet_arrow_up.png'
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
				longname : 'Swap toolbar plugin',
				author : 'ATutor',
				authorurl : 'http://www.atutor.ca',
				infourl : 'http://www.atutor.ca',
				version : "0.9beta"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('swap_toolbar', tinymce.plugins.Swap_toolbarPlugin);
})();