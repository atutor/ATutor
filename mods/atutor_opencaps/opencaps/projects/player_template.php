<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Capscribe Movies</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<style type="text/css">
	body {
		background-color: #FFFFFF;
		text-align:center;
	}
	
	#movie-commands {
		list-style:none;
		margin: 0px 0px 0px -30px;
		font-weight: bold;
		padding:0px;
	}
	#movie-commands li {
		display:inline;
		padding: 5px;
	}
</style>

<script language="JavaScript" type="text/javascript">
function stepback() {
	var time = document.movie1.GetTime();
	
	if (time<5000)
		document.movie1.Rewind();
	else
		document.movie1.SetTime(time-5000);
}

//adds missing zeros before number if ness
function padDigits(n, totalDigits) { 
	n = n.toString(); 
	var pd = ''; 
	if (totalDigits > n.length) 	{ 
		for (i=0; i < (totalDigits-n.length); i++) { 
			pd += '0'; 
		} 
	} 
	return pd + n.toString(); 
} 

function getFormattedTime(gt) {
	var total = gt/document.movie1.GetTimeScale();

	var gms = Math.round(total * 1000) % 1000;
	
	total = Math.floor(total);
	
	var gs = total % 60;
	total = Math.floor(total / 60);	
	
	var gm = total % 60;
	gh = Math.floor(total / 60);	
	
	var code = padDigits(gh, 2) + ":" + padDigits(gm, 2) + ":" + padDigits(gs, 2) + "." + padDigits(gms, 3);
	return code;
}


function gettime() {
	document.movie1.Stop();
	var time = document.movie1.GetTime();
	alert("The current time is " + getFormattedTime(document.movie1.GetTime()));
}

</script>

</head>


<body>

<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="%width%" height="%height%" id="movie1">
	<param name="src" value="smil.mov">
	<param name="enablejavascript" value="true">
	<param name="autoplay" value="false">
	<param name="bgcolor" value="#000000">
	<param name="controller" value="true">
	
	<embed src="smil.mov" width="%width%" height="%height%" pluginspage="http://www.apple.com/quicktime/download/" name="movie1" enablejavascript="true" id="movie1_embed" postdomevents="true" autoplay="false" controller="true" />
</object>

<div style="margin-top:1px; background: #a9a9a9">
	<ul id="movie-commands">
		<li><a href="#" onclick="javascript:document.movie1.Play();">Play</a></li>
		<li><a href="#" onclick="javascript:document.movie1.Stop();">Stop</a></li>	
	
		<li><a href="#" onclick="javascript:gettime()">Get Movie Time</a></li>	
		
		<li><a href="#" onclick="javascript:document.movie1.Rewind();">Rewind</a></li>
		<li><a href="#" onclick="stepback();">Step Back</a></li>
		<li><a href="#" onclick="javascript:document.movie1.SetTime(document.movie1.GetTime()+5000);">Step Ahead</a></li>
	
	
	</ul>	
</div>
	

</body>
</html>