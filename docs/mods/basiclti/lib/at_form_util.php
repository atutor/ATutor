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

function at_form_input($row,$fieldinfo)
{
    $info = parseFormString($fieldinfo);
    if ( isset($info[0]) ) $field = $info[0]; else return;
    if ( isset($info[1]) ) $type = $info[1]; else return;
    $label = $field;
    if ( isset($info['label']) ) $label = $info['label'];
    $required = isset($info['required']);

    if ( $type == 'text' || $type == 'integer' ) { 
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

    if ( $type == 'text' || $type == 'integer' || $type == 'textarea') { 
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
    return $retval;
}

function at_get_field_value($fieldvalue, $type) {
    if ( $type == 'radio' || $type == 'integer') {
        if ( strlen($fieldvalue) < 1 ) $fieldvalue = '0';
    } else {
        $fieldvalue = "'".mysql_real_escape_string($fieldvalue)."'";
    }
    return $fieldvalue;
}

function at_form_insert($row, $form_definition) {
    $fieldlist = "";
    $valuelist = "";
    foreach ( $form_definition as $forminput ) {
        $info =  parseFormString($forminput);
        $fieldname = $info[0]; 
        $type = $info[1]; 
        $fieldvalue = $row[$fieldname];
        if ( ! isset($fieldvalue) ) continue;
        $fieldvalue = trim($fieldvalue);
        if ( strlen($fieldvalue) < 1 ) continue;
        $fieldvalue = at_get_field_value($fieldvalue, $type);
        if ( $fieldlist != "" ) $fieldlist = $fieldlist.", ";
        if ( $valuelist != "" ) $valuelist = $valuelist.", ";
        $fieldlist = $fieldlist.$fieldname;
        $valuelist = $valuelist.$fieldvalue;
      }
      $sql = "( $fieldlist ) VALUES ( $valuelist )";
      return $sql;
}

function at_form_update($row, $form_definition) {
    $setlist = "";
    foreach ( $form_definition as $forminput ) {
        $info =  parseFormString($forminput);
        $fieldname = $info[0]; 
        $type = $info[1]; 
        $fieldvalue = $row[$info[0]];
        if ( ! isset($fieldvalue) ) $fieldvalue = '';
        $fieldvalue = trim($fieldvalue);
        $fieldvalue = at_get_field_value($fieldvalue, $type);
        if ( $setlist != "" ) $setlist = $setlist.", ";
        $setlist = $setlist.$fieldname." = ".$fieldvalue;
    }
    return $setlist;
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
}
