<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Harris Wong								        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: gsearch.php 1 2007-08-31 12:15:41Z harris $
?>
<script src="http://www.google.com/uds/api?file=uds.js<?php echo "&amp;"; ?>hl=<?php echo $_SESSION['lang']; ?><?php echo "&amp;"; ?>v=1.0<?php echo "&amp;"; ?>key=<?php echo $search_key; ?>" type="text/javascript"></script>
<script type="text/javascript">
	/* This can only be run after importing the google uds.  
	 * Ref:		google ajax api.
	 * @url		http://code.google.com/apis/ajaxsearch/documentation/reference.html
	 */
	//<![CDATA[
	var gsearch;  
	function OnLoad() {
	  gsearch = new RawSearchControl();		//instantiate google search object.
	  /* The following is for the side menu search.  */
	  var q = "<?php echo $side_menu_q; ?>";
	  if (q != ""){
		  gsearch.instantSearch(q);
	  }
	}

	/**
	 * The RawSearchControl demonstrates how to use Searcher Objects
	 * outside of the standard GSearchControl. This includes calling
	 * searcher .execute() methods, reacting to search completions,
	 * and if you had previously disabled html generation, how to generate
	 * an html representation of the result.
	 */	
	function RawSearchControl() {
	  // latch on to key portions of the document
	  this.searcherform = document.getElementById("searcher");
	  this.results = document.getElementById("results");
	  this.searchform = document.getElementById("searchform");
	  this.selector = "";
	  this.query = "";
	  this.currentSearchType = "";

	  // create map of searchers as well as note the active searcher
	  this.searchers = new Array();
	  // create and wire up an instance of GwebSearch and one of
	  // GlocalSearch. Note that we register to handle search completion notifications
	  // when searches complete, they are called in the context of this instance
	  // of RawSearchControl and they are passed the searcher that just completed
	  for (var i=0; i<7; i++){
			var searcher = new GwebSearch();
			switch(i){
				case 0: 
					searcher = new GwebSearch();
					break;
				case 1:
					searcher = new GlocalSearch();
					searcher.setCenterPoint("98074");
					break;
				case 2:
					searcher = new GimageSearch();
					break;
				case 3:
					searcher = new GvideoSearch();
					break;
				case 4:
					searcher = new GnewsSearch();
					break;
				case 5:
					searcher = new GblogSearch();
					break;
				case 6:
					searcher = new GbookSearch();
					break;
			}
		  searcher.setNoHtmlGeneration();
		  searcher.setSearchCompleteCallback(this,
											 RawSearchControl.prototype.searchComplete,
											 [searcher]
											 );
		  searcher.setResultSetSize(GSearch.LARGE_RESULTSET);
		  searcher.setLinkTarget("temp_new_search");
		  this.searchers[i] = searcher;
	  }

	  // now, create a search form and wire up a submit and clear handler
	  this.searchForm = new GSearchForm(false, this.searchform);
	  this.searchForm.setOnSubmitCallback(this,
										  RawSearchControl.prototype.onSubmit);
	}

	/**
	 * figure out which searcher is active by looking at the radio
	 * button array
	 */
	RawSearchControl.prototype.computeActiveSearcher = function() {
		if (this.currentSearchType=="" ){
			  this.activeSearcher = 0;
		} else {
			  this.activeSearcher = this.currentSearchType;
		}
	}

	/**
	 * Inject the query into the form, and triggers a instant search
	 * @param	The query
	 */
	 RawSearchControl.prototype.instantSearch = function(q){
		 this.searchForm.input.value = q;
		 this.onSubmit(this.searchForm);
	 }

	/**
	  * figure out which link is being selected, then change that style.
	  * reset all other styles.
	  * @param	searcherTypeIndex	is the integer that maps onto the searcherType string
	  */
	RawSearchControl.prototype.activate = function (searcherTypeIndex){
		for (var i=0; i<this.searchers.length; i++){
			var gsearcher = this.searchers[i];						
			searcherType = this.getSearcherTypeString(i)[0];	
			this.selector = document.getElementById(searcherType + "Selector");
			if (i==searcherTypeIndex){
				//Run this query, activate the menu bar.
				this.currentSearchType = searcherTypeIndex;
				cssSetClass(this.selector, "active");
				this.computeActiveSearcher();
				var q = this.searchForm.input.value;
				if (q) {
					gsearcher.execute(q);
				}
			} else {
				cssSetClass(this.selector, "");
			}
		}
	}

	/**
	 * This functiion will retrieve the string of the searcher type by the given index
	 */
	 RawSearchControl.prototype.getSearcherTypeString = function(searcherTypeIndex){
			var s;
			switch(searcherTypeIndex){
				case 1:
					s = new Array("local", "http://www.google.ca/maps?");
					break;
				case 2:
					s = new Array("image", "http://www.google.ca/images?");
					break;
				case 3:
					s = new Array("video", "http://video.google.ca/videosearch?");
					break;
				case 4:
					s = new Array("news", "http://www.google.ca/news?");
					break;
				case 5:
					s = new Array("blog", "http://www.google.ca/blogsearch?");
					break;
				case 6:
					s = new Array("book", "http://www.google.ca/books?");
					break;
				case 0:
				default:
					s = new Array("web", "http://www.google.ca/search?");
					break;
			}
			return s;
	 }

	/**
	 * onSubmit - called when the search form is "submitted" meaning that
	 * someone pressed the search button or hit enter. The form is passed
	 * as an argument
	 */
	RawSearchControl.prototype.onSubmit = function(form) {
	  this.computeActiveSearcher();
	  if (form.input.value) {
		// if there is an expression in the form, call the active searcher's
		// .execute method
		this.query = form.input.value;
		this.searchers[this.activeSearcher].execute(form.input.value);
	  }
	  // always indicate that we handled the submit event
	  return false;
	}

	/**
	 * onClear - called when someone clicks on the clear button (the little x)
	 */
	RawSearchControl.prototype.onClear = function(form) {
	  this.clearResults();
	}

	/**
	 * searchComplete - called when a search completed. Note the searcher
	 * that is completing is passes as an arg because thats what we arranged
	 * when we called setSearchCompleteCallback
	 */
	RawSearchControl.prototype.searchComplete = function(searcher) {
	  // always clear old from the page
	  this.clearResults();

	  // if the searcher has results then process them
	  if (searcher.results && searcher.results.length > 0) {
		// now manually generate the html that we disabled
		// initially and display it
		var div = createDiv("", "");
		this.results.appendChild(div);
		for (var i=0; i<searcher.results.length; i++) {
		  var result = searcher.results[i];
		  searcher.createResultHtml(result);
		  if (result.html) {
			div = result.html.cloneNode(true);
		  } else {
			div = createDiv("** failure to create html **");
		  }
		  this.results.appendChild(div);
		}
		div = createDiv("");
		a_elem = createAnchor("More results >>", this.generateMoreResultsURL());
		div.appendChild(a_elem);
		this.results.appendChild(div);
	  } else {
		var div = createDiv("No results for: " + this.searchForm.input.value, "");
		this.results.appendChild(div);
	  }
	}

	/**
	 * This function returns the appropriate link for the "More results" at the bottom of a search.
	 * The "More results" should correspond to the category the search was performed in.(ie. maps, video, images...)
	 * @return	the URL described above.
	 */
	RawSearchControl.prototype.generateMoreResultsURL = function(){
		var s = "";
		var link = this.getSearcherTypeString(this.activeSearcher)[1];
		return (link + "hl=<?php echo $_SESSION['lang']?>&q=" + this.searchForm.input.value);
	}

	/**
	 * clearResults - clear out any old search results
	 */
	RawSearchControl.prototype.clearResults = function() {
	  removeChildren(this.results);
	}

	/**
	 * Static DOM Helper Functions
	 */
	function removeChildren(parent) {
	  while (parent.firstChild) {
		parent.removeChild(parent.firstChild);
	  }
	}
	function createDiv(opt_text, opt_className) {
	  var el = document.createElement("div");
	  if (opt_text) {
		el.innerHTML = opt_text;
	  }
	  if (opt_className) { el.className = opt_className; }
	  return el;
	}
	function createAnchor(a_text, a_href, a_className){
	  var el = document.createElement("a");
	  if(a_text){
		  el.innerHTML = a_text;
		  el.href = a_href;
		  el.target = "temp_google_search";
	  }
	  if (a_className){ a.className = a_className;}
	  return el;
	}

	/**
	  * CSS helper
	  */
	function cssSetClass(el, className) {
		el.className = className;
	}

	// register to be called at OnLoad when the page loads
	GSearch.setOnLoadCallback(OnLoad);
	//]]>
</script>

<form id="searcher" method="get" action="">
  <table border='0'>
	<tr><td colspan="2">
	<div class="selector">
	<ul id="navlist">
		<li><a id="webSelector" class="active" href="javascript:gsearch.activate(0);"><?php echo _AT('google_search'); ?></a></li>
		<li><a id="localSelector" href="javascript:gsearch.activate(1);"><?php echo _AT('google_search_local'); ?></a></li>
		<li><a id="imageSelector" href="javascript:gsearch.activate(2);"><?php echo _AT('google_search_images'); ?></a></li>
		<li><a id="videoSelector" href="javascript:gsearch.activate(3);"><?php echo _AT('google_search_videos'); ?></a></li>
		<li><a id="newsSelector" href="javascript:gsearch.activate(4);"><?php echo _AT('google_search_news'); ?></a></li>
		<li><a id="blogSelector" href="javascript:gsearch.activate(5);"><?php echo _AT('blogs'); ?></a></li>
		<li><a id="bookSelector" href="javascript:gsearch.activate(6);"><?php echo _AT('google_search_books'); ?></a></li>
	</ul></div></td>
	</tr>
	<tr>
	<td class="search-form">
	  <div id="searchform">Loading</div>
	</td>
	<!-- For clearing the results
	<td>
	  <input name="clearResult" value="clear" type="button" class="button" onclick="gsearch.clearResults();"/>
	</td>
	-->
	</tr>
  </table>
</form>
<div id="results"></div>
