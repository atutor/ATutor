<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Properties</h2>
	<p>In the properties tab, you can move the content page, select a Release Date, enter keywords for easier searching, and specify its related topics.</p>

	<dl>
		<dt>Move</dt>
		<dd>In the left column of the Properties screen in the Content Editor choose the 'up arrow' to move the current content <em>Before</em> another item. Choose the 'down arrow' to move the content <em>After</em> that item. Choose the 'plus sign' to make the current content a <em>Child of</em>, or sub-topic, for that item. </dd>

		<dt>Release Date</dt>
		<dd>The release date specifies when the content page will be visible to students. Content can be scheduled for release by specifying date in the future. Specifying a release date that has past will release the content immediately. The release date of a page affects all of its sub-pages as well, such that a sub-page is released only when the most distant release date of all its parent pages has passed. By default, the Release Date is set as the current date and time.</dd>
		
		<dt>Keywords</dt>
		<dd>Words entered into the Keywords area are given greater emphasis during searching. Therefore they will be placed higher in a list of search results than if there were no keywords. Keywords are also used as Learning Object Metadata when a content package is generated.</dd>
		
		<dt>Related Topics</dt>
		<dd><p>For each content page in the course, it is possible to specify other content pages as being related. Related topics can appear in the side menu, allowing students to quickly jump to a topic. Related topics are cross-refrenced meaning the content page chosen to be related will also be related to the current page.</p>
		</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>