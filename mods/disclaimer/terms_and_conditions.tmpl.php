<?php
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="<?php echo $this->base_href; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_href.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_href.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php echo $this->base_href.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
	<title><?php echo $this->site_name . ' - ' .  _AT('disclaimer'); ?></title>
	<style type="text/css">
	<!--
	.tac {
		width: 60%;
		margin: 0px auto;
		border: 1px solid #C4C4C4;
		padding-bottom: 0.5em;
	}
	#tac_body{
		width: 100%;
		padding:0.5em;
	}
	#attention {
		width:450px;
		color:red;
		text-align:left;
		font-weight:bold;
		font-size:12px;
	}
	.mar20 {
		margin-top:20px;
		border: 1px solid black; 
		padding: 1em; 
		overflow: scroll; 
		width: 450px; 
		height: 250px; 
		font-size: 0.9em;
	}
	-->
	</style>
</head>
<body>
	<div class="tac" align="center">
		<div id="header">
			<!-- section title -->
			<h1 id="section-title"><?php echo SITE_NAME . ' - ' . _AT('disclaimer'); ?></h1>
		</div>
		<div id="tac_body">
			<div class="attention"><?php echo _AT('tac_attention'); ?></div>
			<div class="mar20"><?php echo $this->body_text; ?></div>
		</div>
		<div>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
			<input class="button" type="button" value="<?php echo _AT('i_do_not_agree');?>" onClick="parent.location='<?php echo $this->tac_link; ?>'" />
			<input class="button" type="submit" name="agree" value="<?php echo _AT('i_agree');?>" />
		</form></div>
	</div>
</body>
</html>
