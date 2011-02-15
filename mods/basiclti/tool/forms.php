<?php

require_once('../lib/at_form_util.php');

$blti_instructor_form = array(
	'title:text:label=bl_title:required=true:size=25',
        'toolid:id:label=bl_toolid:required=true:size=16',
	'description:textarea:label=bl_description:required=true:rows=2:cols=25',
	'toolurl:url:label=bl_toolurl:required=true:size=80',
	'resourcekey:text:label=bl_resourcekey:required=true:size=80',
	'password:text:required=true:label=bl_password:size=80',
	'preferheight:integer:label=bl_preferheight:size=80',
        'preferheight:radio:label=bl_allowpreferheight:choices=off,on',
	'launchinpopup:radio:label=bl_launchinpopup:choices=off,on,content',
	'debuglaunch:radio:label=bl_debuglaunch:choices=off,on,content',
	'sendname:radio:label=bl_sendname:choices=off,on,content',
	'sendemailaddr:radio:label=bl_sendemailaddr:choices=off,on,content',
	'acceptgrades:radio:label=bl_acceptgrades:choices=off,on',
	'allowroster:radio:label=bl_allowroster:choices=off,on,content',
	'allowsetting:radio:label=bl_allowsetting:choices=off,on,content',
        'allowcustomparameters:radio:label=bl_allowcustomparameters:choices=off,on',
	'customparameters:textarea:label=bl_customparameters:rows=5:cols=25',
        );

$blti_admin_form = array();
foreach ( $blti_instructor_form as $line ) {
   $newline = str_replace('choices=off,on,content','choices=off,on,instructor',$line);
   $blti_admin_form[] = $newline;
}

$blti_admin_form = array_merge($blti_admin_form, array(
	'organizationid:text:label=bl_organizationid:size=80',
	'organizationurl:text:label=bl_organizationurl:size=80',
	'organizationdescr:text:label=bl_organizationdescr:size=80',
        ) );

$blti_content_edit_form = array(
	'preferheight:integer:label=bl_preferheight:size=80',
	'launchinpopup:radio:label=bl_launchinpopup:choices=off,on',
	'debuglaunch:radio:label=bl_debuglaunch:choices=off,on',
	'sendname:radio:label=bl_sendname:choices=off,on',
	'sendemailaddr:radio:label=bl_sendemailaddr:choices=off,on',
	'allowroster:radio:label=bl_allowroster:choices=off,on',
	'allowsetting:radio:label=bl_allowsetting:choices=off,on',
	'customparameters:textarea:label=bl_customparameters:rows=5:cols=25',
        );

if ( ! function_exists('isCli') ) {
    function isCli() {
        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
            return true;
        } else {
            return false;
        }
    }
}

// If we are running from the command line - do a unit test
if ( isCli() ) {
    function startsWith($haystack,$needle,$case=true) {
        if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
        return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
    }
    
    function endsWith($haystack,$needle,$case=true) {
        if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
    }

    $i18nstrings = array_merge(foorm_i18n_util($blti_instructor_form), 
                               foorm_i18n_util($blti_admin_form),
                               foorm_i18n_util($blti_content_edit_form));
    $i18nstrings = array_unique($i18nstrings);
    sort($i18nstrings);
    foreach ($i18nstrings as $i18n ) {
         $value = $i18n;
         if ( startsWith($value,"bl_") ) $value = substr($value,3);
         if ( endsWith($value,"_on") ) $value = 'Always enabled';
         if ( endsWith($value,"_off") ) $value = 'Never allowed';
         if ( endsWith($value,"_instructor") ) $value = 'Delegate to Instructor';
         if ( endsWith($value,"_content") ) $value = 'Specify in each Content Item';
         $value = ucfirst($value);
         
         echo("INSERT INTO 'language_text' VALUES ('en', '_module','$i18n','$value',NOW(),'');\n");
    }
}
?>
