<?php
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Fraser Health Online Learning System - Terms and Conditions of Use</title>
<style type="text/css">
<!--
body {
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
#maintable {
	background-color:#FFFFFF;
	border-left:1px solid #C4C4C4;
	border-right:1px solid #C4C4C4;
	border-bottom:1px solid #C4C4C4;
	border-top:4px solid #004780;
}
#maintitle {
	margin-top:25px; 
	margin-left:20px; 
	margin-right:20px; 
	margin-bottom:20px; 
	color:#004780;
	font-family:'Trebuchet MS'; 
	font-weight:bold;
}
#logo {
	border:0px solid red; 
	margin-right:20px; 
	margin-top:5px;
}
#attention {
	width:450px;
	color:red;
	text-align:left;
	font-weight:bold;
	font-size:12px;
}
.martop20 {
	margin-top:20px;
}
.marbot20 {
	margin-top:20px;
}
-->
</style>
</head>
<body>
<div align="center">
  <table id="maintable" cellpadding="0" cellspacing="0" border="0">
    <tr style="background-color:#EBF4F9;">
      <td><div id="maintitle"> <span style="font-size:18pt;"><?php echo $this->site_name;?></span> <br>
          <span style="font-size:24pt;"><?php echo _AT('terms_and_conditions'); ?></span> </div></td>
      <td width="350" align="right" valign="top"><img id="logo" src="FHA-logo.gif"> </td>
    </tr>
    <tr>
      <td style="border-top:1px solid #70A1CA;" colspan="2" align="center" valign="middle"><div class="marbot20 martop20">
          <?php echo $this->body_text; ?>
        </div></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><div class="marbot20">
          <form>
            <input type="button" value="<?php echo _AT('i_do_not_agree');?>" onClick="parent.location='<?php echo $this->tac_link; ?>'">
            <input type="button" value="<?php echo _AT('i_agree');?>" onClick="parent.location='<?php echo $this->base_href; ?>login.php'">
          </form>
        </div></td>
    </tr>
  </table>
</div>
</body>
</html>
