/**
 * $Id: $
 * 
 * @author Laurel A. Williams
 * @copyright Copyright © 2010, ATutor, All rights reserved.
 */

var ATutor = ATutor || {};
ATutor.users = ATutor.users || {};
ATutor.users.preferences = ATutor.users.preferences || {};

(function() {
	
    ATutor.users.preferences.user_styles = 
    	'<style id="pref_style" type="text/css"><!-- ' + 
    	'   body { ' +
    	'     {FONT}' +
    	'        } ' +
    	'    --></style>';

    ATutor.users.preferences.setStyles = function (font) {
		var font_style = font ? 'font-family:' + font + ';\n' : '';
		var replaced = ATutor.users.preferences.user_styles.replace('{FONT}', font_style);
	    jQuery('#pref_style').replaceWith(replaced);
	};	
	
})();
