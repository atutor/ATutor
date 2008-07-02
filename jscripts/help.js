function toggleToc() {
	//var tocmain = document.getElementById('help');
	var toc = document.getElementById('help');
	var showlink=document.getElementById('showlink');
	var hidelink=document.getElementById('hidelink');
	if(toc.style.display == 'none') {
		toc.style.display = tocWas;
		hidelink.style.display='';
		showlink.style.display='none';
		//tocmain.className = '';

		var help = document.getElementById('help-title');
		help.className = '';

	} else {
		tocWas = toc.style.display;
		toc.style.display = 'none';
		hidelink.style.display='none';
		showlink.style.display='';
		//tocmain.className = 'tochidden';

		var help = document.getElementById('help-title');
		help.className = 'line';
	}
}
