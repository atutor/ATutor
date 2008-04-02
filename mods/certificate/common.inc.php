<?php

define('AT_INCLUDE_PATH', '../../include/');
require_once (AT_INCLUDE_PATH.'vitals.inc.php');

function initialize_default_vars()
{
	global $default_certificate, $default_organization, $fields_array;
	
	$default_certificate = "default_certificate.pdf";
	$default_organization = "Fraser Health Authority";
	
	$fields_array = load_field_data("default_certificate.pdf.fields");
}

function load_field_data( $field_report_fn )
{
  $ret_val= array();

  $fp= fopen( $field_report_fn, "r" );
  
  if( $fp ) 
  {
    $line= '';
    $rec= array();
    
    while( ($line= fgets($fp, 2048))!== FALSE ) 
    {
      $line= rtrim( $line );
      
      if( $line== '---' ) 
      {
				if( 0< count($rec) ) 
				{ // end of record
				  $ret_val[]= $rec;
				  $rec= array();
				}
				continue;
    	}

      $data_pos= strpos( $line, ':' );
      $name= substr( $line, 0, $data_pos );
      $value= substr( $line, $data_pos+ 2 );

			$rec[ $name ]= $value;
    }

    if( 0< count($rec)) 
    { // pack final record
      $ret_val[]= $rec;
    }

    fclose( $fp );
  }

  return $ret_val;
}

function is_pass_score_defined_in_base_table() 
{
	global $db;
	
	$sql	= "SELECT passscore, passpercent FROM ".TABLE_PREFIX."tests limit 1";
	
	if (mysql_query($sql, $db)===false) return false;
	else return true;
}

// Initialize tokens into a global array
function initialize_tokens($result_id)
{
	global $db;
	
	$sql = "SELECT *, date_format(date_taken, '%Y-%m-%d') date_taken 
	          FROM ".TABLE_PREFIX."tests t, ".TABLE_PREFIX."tests_results r 
	         WHERE r.result_id=".$result_id."
	           AND t.test_id = r.test_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	$tokens[] = array("name"=>"[TNAME]", "value"=>$row["title"]);
	$tokens[] = array("name"=>"[USCORE]", "value"=>$row["final_score"]);
	$tokens[] = array("name"=>"[OSCORE]", "value"=>$row["out_of"]);
	$tokens[] = array("name"=>"[PSCORE]", "value"=>($row["final_score"]/$row["out_of"]*100).'%');
	$tokens[] = array("name"=>"[SYSDATE]", "value"=>$row["date_taken"]);

	$sql = "SELECT * FROM ".TABLE_PREFIX."courses c where c.course_id=".$_SESSION["course_id"] ;
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	$tokens[] = array("name"=>"[CNAME]", "value"=>$row["title"]);

	$sql = "SELECT * FROM ".TABLE_PREFIX."members m where m.member_id=".$_SESSION["member_id"] ;
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	$tokens[] = array("name"=>"[USERID]", "value"=>$row["member_id"]);
	$tokens[] = array("name"=>"[USERMAIL]", "value"=>$row["email"]);
	$tokens[] = array("name"=>"[FNAME]", "value"=>$row["first_name"]);
	$tokens[] = array("name"=>"[LNAME]", "value"=>$row["last_name"]);

	return $tokens;
}

// replace tokens in the pass-in string
// param: $str - string with tokens in
//        $tokens - array of matching between token name and value
function replace_tokens($str, $tokens)
{
	foreach ($tokens as $token)
		$str = str_replace($token["name"], $token["value"], $str);
		
	return $str;
}

if ($include_javascript)
{
?>

<script language="JavaScript">
function open_certificate_win(certificate_url, radio_name, hidden_name)
{
	// find selected radio button
	var radio_value = 0;
	var hidden_value = 0;

	if (eval("document.form."+radio_name+".length") > 0)
	{
		for( i = 0; i < eval("document.form."+radio_name+".length"); i++ )
		{
			if( eval("document.form."+radio_name+"[i].checked") == true )
			{
				radio_value = eval("document.form."+radio_name+"[i].value");
				hidden_value = eval("document.form."+hidden_name+"[i].value");
			}
		}
	}
	else
	{
		if( eval("document.form."+radio_name+".checked") == true )
		{
			radio_value = eval("document.form."+radio_name+".value");
			hidden_value = eval("document.form."+hidden_name+".value");
		}
	}

	certificate_url = certificate_url.replace(/{radio_value}/, radio_value);
	certificate_url = certificate_url.replace(/{hidden_value}/, hidden_value);
	
	window.open (certificate_url, 'Certificate')
}

</script>

<?php
}
?>
