<?php

// Parse a form field description
// field:type:key=value:key2=value2
function parseFormString($str) { 
    $op = array(); 
    $pairs = explode(":", $str); 
    foreach ($pairs as $pair) { 
        $kv = explode("=", $pair);
	if ( sizeof($kv) == 1 ) {
            $op[] = $pair;
        } else {
            $op[$kv[0]] = $kv[1];
	}
    } 
    return $op; 
} 

// Filter a form definition based on a controlling row.
//
// The controlling row has fields that are interpreted as
// 0=force off, 1=force on, 2 = delegate setting
// For radio buttons in our form, it simply checks for 
// the field of the same name in the controlling row.  
// For non-radio fields, it looks for a field in the 
// controlling row prepended by 'allow'.
function filterForm($control_row, $fieldinfo)
{
    $new_form = array();
    foreach ($fieldinfo as $line) {
       $fields = parseFormString($line);
       if ( $fields[1] == 'radio' ) {
           if ( $control_row[$fields[0]] == 2 ) $new_form[] = $line;
       }
       // See if a non-radio field is controlled by an allow field
       $allowfield = 'allow'.$fields[0];
       if ( isset( $control_row[$allowfield] ) ) {
           if ( $control_row[$allowfield] == 1 ) $new_form[] = $line;
       }
    }
    return $new_form;
}

function at_form_input($row,$fieldinfo)
{
    $info = parseFormString($fieldinfo);
    if ( isset($info[0]) ) $field = $info[0]; else return;
    if ( isset($info[1]) ) $type = $info[1]; else return;
    $label = $field;
    if ( isset($info['label']) ) $label = $info['label'];
    $required = isset($info['required']);

    if ( $type == 'text' || $type == 'url' || $type == 'id' || $type == 'integer' ) { 
        $size = isset($info['size']) ? $info['size'] : 40; ?>
        <div class="row">
                <?php if ($required) { ?><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php } ?><label for="<?php echo $field;?>"><?php echo _AT($label); ?></label><br />
                <input type="text" id="<?php echo $field;?>" name="<?php echo $field;?>" size="<?php echo $size;?>" value="<?php echo htmlspecialchars($row[$field]); ?>" />
        </div>
    <?php }
    else if ( $type == 'textarea' ) {
        $cols = isset($info['cols']) ? $info['cols'] : 25;
        $rows = isset($info['rows']) ? $info['rows'] : 2; ?>
        <div class="row">
                <?php if ($required) { ?><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php } ?><label for="<?php echo $field;?>"><?php echo _AT($label); ?></label><br />
                <textarea id="<?php echo $field;?>" name="<?php echo $field;?>" cols="<?php echo $cols;?>" rows="<?php echo $rows;?>"><?php echo htmlspecialchars($row[$field]); ?></textarea>
        </div>
    <?php }
    else if ( $type == 'radio' ) {
        if ( isset($info['choices']) ) {
            $choices = explode(',', $info['choices']);
        } else {
            echo('<!-- at_form_radio requires choices=on,off,part -->');
            return;
        }
        $current = isset($row[$field]) ? $row[$field] : -1;
        ?>
        <div class="row">
            <?php if ($required) { ?><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php } ?><label for="<?php echo $field;?>"><?php echo _AT($label); ?></label><br />
<?php
foreach($choices as $key => $value ) { 
$checked = '';
if ( $key == $current ) $checked = ' checked="checked"';
?>
                <label><input type="radio" name="<?php echo $field; ?>" value="<?php echo $key?>" id="<?php echo $field.'_'.$value;?>"<?php echo $checked; ?>/><?php echo _AT($label.'_'.$value); ?></label><br />
<?php } ?>
        </div>
<?php
    }
}

function at_form_generate($row, $form_definition) {
    foreach ( $form_definition as $forminput ) {
      at_form_input($row,$forminput);
    }
}


function at_form_output($row,$fieldinfo)
{
    $info = parseFormString($fieldinfo);
    if ( isset($info[0]) ) $field = $info[0]; else return;
    if ( isset($info[1]) ) $type = $info[1]; else return;
    $label = $field;
    if ( isset($info['label']) ) $label = $info['label'];

    if ( $type == 'text' || $type == 'url' || $type == 'id' || $type == 'integer' || $type == 'textarea') { 
        if ( strlen($row[$field]) < 1 ) return; ?>
        <div class="row">
                <?php  echo _AT($label); ?><br/>
                <?php echo htmlspecialchars($row[$field]); ?>
        </div>
    <?php }
    else if ( $type == 'radio' ) {
        if ( isset($info['choices']) ) {
            $choices = explode(',', $info['choices']);
        } else {
            echo('<!-- at_form_radio requires choices=on,off,part -->');
            return;
        }
        $current = isset($row[$field]) ? $row[$field] : 0;
        if ( $current < 0 || $current >= sizeof($choices) ) $current = 0;
        ?>
        <div class="row"> <?php
            $value = $choices[$current];
            echo _AT($label)."<br/>\n";
            echo _AT($label.'_'.$value); ?>
        </div>
<?php
    }
}

function at_form_view($row, $form_definition) {
    foreach ( $form_definition as $forminput ) {
      at_form_output($row,$forminput);
    }
}

function at_form_validate($form_definition, $msg ) {
    $retval = true;
    $missing_fields = array();
    $numeric_fields = array();
    $url_fields = array();
    $id_fields = array();

    foreach ( $form_definition as $forminput ) {
        $info =  parseFormString($forminput);
        $label = isset($info['label']) ? $info['label'] : $info[0];
        $datafield = $_POST[$info[0]];
        $datafield = trim($datafield);
        // echo($info[0] . '=' . $datafield. "<br/>\n");
        if ( isset($info['required']) && strlen($datafield) < 1 ) {
           $missing_fields[] = _AT($label);
        }
        if ( $info[1] == 'integer' || $info[1] == 'radio') {
            if ( preg_match("/[0-9]+/", $datafield) == 1 || strlen($datafield) == 0 ) {
                // OK
            } else {
                $numeric_fields[] = _AT($label);
            }
        }
        if ( $info[1] == 'id' ) {
            if ( preg_match("/^[0-9a-zA-Z._-]*$/", $datafield) == 1 || strlen($datafield) == 0 ) {
                // OK
            } else {
                $id_fields[] = _AT($label);
            }
        }
        if ( $info[1] == 'url' ) {
	    $pattern = "'^(http://|https://)[a-z0-9][a-z0-9]*'";
            if ( preg_match($pattern, $datafield) == 1 || strlen($datafield) == 0 ) {
                // OK
            } else {
                $url_fields[] = _AT($label);
            }
        }
    }
    if (sizeof($missing_fields) > 0) {
        $missing_fields = implode(', ', $missing_fields);
        $msg->addError(array('EMPTY_FIELDS', $missing_fields));
        $retval = false;
    }
    if (sizeof($numeric_fields) > 0) {
        $numeric_fields = implode(', ', $numeric_fields);
        // TODO: Make sure this prints out the list of fields
        $msg->addError(array('NUMERIC_FIELDS', $numeric_fields));
        $msg->addError($numeric_fields);
        $retval = false;
    }
    if (sizeof($url_fields) > 0) {
        $url_fields = implode(', ', $url_fields);
        $msg->addError(array('URL_FIELDS', $url_fields));
        $retval = false;
    }
    if (sizeof($id_fields) > 0) {
        $id_fields = implode(', ', $id_fields);
        $msg->addError(array('ID_FIELDS', $id_fields));
        $retval = false;
    }
    return $retval;
}

function at_get_field_value($fieldvalue, $type = false) {
    global $addslashes;
    
	if ( $fieldvalue === false ) {
       $fieldvalue = 'NULL';
    } else if ( is_int($fieldvalue) ) {
       $fieldvalue = $fieldvalue.'';
    } else if ( $type == 'radio' || $type == 'integer') {
        if ( strlen($fieldvalue) < 1 ) $fieldvalue = '0';
    } else {
        $fieldvalue = "'".$addslashes($fieldvalue)."'";
    }
    return $fieldvalue;
}

// $overrides = array('course_id' => 12, "title" => "yo", "toolid" => false);
// false in the array becomes NULL in the database
function at_form_insert($row, $form_definition, $overrides=false) {
    $fieldlist = "";
    $valuelist = "";
    $handled = array();
    foreach ( $form_definition as $forminput ) {
        $info =  parseFormString($forminput);
        $fieldname = $info[0]; 
        $type = $info[1]; 
        $fieldvalue = null;
        if ( is_array($overrides) && isset($overrides[$fieldname]) ) $fieldvalue = $overrides[$fieldname];
        if ( ! isset($fieldvalue) ) $fieldvalue = $row[$fieldname];
        if ( ! isset($fieldvalue) ) continue;
        $fieldvalue = trim($fieldvalue);
        if ( strlen($fieldvalue) < 1 ) continue;
        $fieldvalue = at_get_field_value($fieldvalue, $type);
        $handled[] = $fieldname;
        if ( $fieldlist != "" ) $fieldlist = $fieldlist.", ";
        if ( $valuelist != "" ) $valuelist = $valuelist.", ";
        $fieldlist = $fieldlist.$fieldname;
        $valuelist = $valuelist.$fieldvalue;
      }
      if ( is_array($overrides) ) foreach($overrides as $fieldname => $fieldvalue) {
        if ( in_array ( $fieldname , $handled) ) continue;
        $fieldvalue = at_get_field_value($fieldvalue);
        if ( $fieldlist != "" ) $fieldlist = $fieldlist.", ";
        if ( $valuelist != "" ) $valuelist = $valuelist.", ";
        $fieldlist = $fieldlist.$fieldname;
        $valuelist = $valuelist.$fieldvalue;
      }
      $sql = "( $fieldlist ) VALUES ( $valuelist )";
      return $sql;
}

function at_form_update($row, $form_definition, $overrides=false) {
    $setlist = "";
    $handled = array();
    foreach ( $form_definition as $forminput ) {
        $info =  parseFormString($forminput);
        $fieldname = $info[0]; 
        $type = $info[1]; 
        $fieldvalue = null;
        if ( is_array($overrides) && isset($overrides[$fieldname]) ) $fieldvalue = $overrides[$fieldname];
        if ( ! isset($fieldvalue) ) $fieldvalue = $row[$info[0]];
        if ( ! isset($fieldvalue) ) $fieldvalue = '';
        $fieldvalue = trim($fieldvalue);
        $fieldvalue = at_get_field_value($fieldvalue, $type);
        if ( $setlist != "" ) $setlist = $setlist.", ";
        $setlist = $setlist.$fieldname." = ".$fieldvalue;
    }
    if ( is_array($overrides) ) foreach($overrides as $fieldname => $fieldvalue) {
        if ( in_array ( $fieldname , $handled) ) continue;
        $fieldvalue = at_get_field_value($fieldvalue);
        if ( $setlist != "" ) $setlist = $setlist.", ";
        $setlist = $setlist.$fieldname." = ".$fieldvalue;
    }
    return $setlist;
}

function foorm_i18n_util($fieldinfo) {
    $strings = array();
    foreach ($fieldinfo as $line) {
       $info = parseFormString($line);
       $label = $info[0];
       if ( isset($info['label']) ) $label = $info['label'];
       $strings[] = $label;
       if ( $info[1] == 'radio' ) {
          if ( isset($info['choices']) ) {
            $choices = explode(',', $info['choices']);
            foreach($choices as $choice) {
               $strings[] = $label.'_'.$choice;
            }
          }
       }
    }
    return $strings;
}

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
    print_r(parseFormString('title:text:required=true:size=25'));
    print_r(parseFormString('description:textarea:required=true:rows=2:cols=25'));
    print_r(parseFormString('sendemail:radio:requred=true:label=bl_sendemail:choices=on,off,part'));

    $row = array();
    $row['title'] = 'Fred';
    $row['description'] = 'Desc';
    $row['sendemail'] = 1;
    function _AT($str) { return $str; }

    at_form_input($row,'title:text:required=true:size=25');
    at_form_input($row,'description:textarea:required=true:rows=2:cols=25');
    at_form_input($row,'sendemail:radio:requred=true:label=bl_sendemail:choices=on,off,part');

    $test_frm = array(
        'title:text:size=80',
        'preferheight:integer:label=bl_preferheight:size=80',
        'sendname:radio:label=bl_sendname:choices=off,on,content',
        'acceptgrades:radio:label=bl_acceptgrades:choices=off,on',
        'customparameters:textarea:label=bl_customparameters:rows=5:cols=25',
        );

    $i18strings = foorm_i18n_util($test_frm);
    print_r($i18strings);

}

