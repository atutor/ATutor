	<SCRIPT src="<?php echo $_base_path; ?>taeditor.class.js" type=text/javascript></SCRIPT>
	<SCRIPT type=text/javascript>
// <![CDATA[ Hide from XML parser
	function init()
	{
		var a = new TAEditor( document.forms['form'], 'body', 'buttonArea', '[', ']' );
		a.basicButton( 'B', 'b' );
		a.basicButton( 'i', 'i');
		a.basicButton( 'u', 'u' );
		a.complexButton( 'http://', 'url', 0, 'linkGetter', init );
		a.complexButton( 'image', 'image', 0, 'imgGetter', init );
		a.basicButton( 'quote', 'quote' );
		
		a.basicButton( '', 'blue' );
		a.basicButton( '', 'red' );
		a.basicButton( '', 'green' );
		a.basicButton( '', 'orange' );
		a.basicButton( '', 'purple' );
		a.basicButton( '', 'gray' );

		//a.addHTMLElement( 'br', null );

		a.basicIcon('images/forum/smile.gif', 'smile', ':)' );
		a.basicIcon('images/forum/wink.gif', 'wink', ';)' );
		a.basicIcon('images/forum/frown.gif', 'frown', ':(' );
		a.basicIcon('images/forum/ohwell.gif', 'oh well', ':\\' );
		a.basicIcon('images/forum/54.gif', 'lol', '::lol::' );
		a.basicIcon('images/forum/3.gif', 'confused', '::confused::' );
		a.basicIcon('images/forum/57.gif', 'roll eyes', '::rolleyes::' );

		
		init.linkGetter = function()
		{
			var url = prompt( "Enter the URL for this link", "http://" );
			document.form.body.focus();
			if (url != null) {
				return a.insert( url );
			}
		}
		init.emailGetter = function()
		{
			var linkText = prompt( "Enter the text for this mailto: link.\nIf there is a selection active, click [ OK ] to use that selection", "" );
			var email = '=\'' + prompt( "Enter the email address", "" )  + '\'';
			return a.createTagMaker( linkText, email );
		}
		init.imgGetter = function()
		{
			var src = prompt( "Enter the source for this image", "http://" );
			return a.createTagMaker( src, null );
		}
		
		document.form.body.focus();
		a.saveCaret();
	}
// ]]>
</SCRIPT>
	<style>
		.b {
			font-weight: bold;
		}
		.i {
			font-style: italic;
		}

		.sspace {
			margin-right: 2px;
		}

		.red {
			background-color: red;
			background-image: none;
			font-size: smaller;
		}
		.blue {
			background-color: blue;
			background-image: none;
			font-size: smaller;
		}
		.green {
			background-color: green;
			background-image: none;
			font-size: smaller;
		}
		.orange {
			background-color: orange;
			background-image: none;
			font-size: smaller;
		}
		.purple {
			background-color: purple;
			background-image: none;
			font-size: smaller;
		}
		.gray {
			background-color: gray;
			background-image: none;
			font-size: smaller;
		}

	</style>
<div id=buttonArea></div>