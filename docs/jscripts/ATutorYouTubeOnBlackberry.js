var ATutor = ATutor || {};
ATutor.course = ATutor.course || {};

(function() {
	ATutor.course.showYouTubeOnBlackberry = function (data) {
	  var html = [''];
	  var entry = data.entry;
	  
	  // var to hold the rtsp link
	  var rtspUrl = '';
	  
	  // vars to hold the thumbnail info
	  var thumbnailUrl = '';
	  var thumbnailH = '10';
	  var thumbnailW = '10';
	  var idUrl = entry.id.$t;
	  var videoID = '';
	
	  /* http://gdata.youtube.com/feeds/mobile/videos/O7BXgT413i4 */
	  if (idUrl.length > 45) {
		videoID = idUrl.substring(45, idUrl.length);
	  }
	
	  // get the array with the rtsp links
	  var mediacontents = entry.media$group.media$content || [];
	  if (mediacontents.length > 1) {
		rtspUrl = mediacontents[1].url;
	  }
	  else if (mediacontents.length > 0) {
		rtspUrl = mediacontents[0].url;
	  }
	  
	  // get array with the thumbnail links and grab the first one
	  var thumbnails = entry.media$group.media$thumbnail || [];
	  if (thumbnails.length > 0) {
		thumbnailUrl = thumbnails[0].url;
		thumbnailH = (thumbnails[0].height * 2);
		thumbnailW = (thumbnails[0].width *2);
	  }
	
	  html.push('<a href="', rtspUrl ,'"><img alt="Thumbnail picture of YouTube video (link around the picture)" src="', thumbnailUrl ,'" width="', thumbnailW ,'" height="', thumbnailH ,'" /></a>');
	  
	  document.getElementById('blackberry_' + videoID).innerHTML = html.join('');
	}

})();
