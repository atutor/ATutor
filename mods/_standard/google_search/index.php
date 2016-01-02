<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
//require(AT_INCLUDE_PATH.'../mods/_standard/google_search/SOAP_Google.php');
$_custom_css = $_base_path . 'mods/_standard/google_search/module.css'; // use a custom stylesheet
//$search_key = $_config['gsearch'];

$_custom_head .= "<script src=\"https://www.google.com/jsapi\"
        type=\"text/javascript\"></script>
    <script language=\"Javascript\" type=\"text/javascript\">
    //<!
    google.load('search', '1');

    function OnLoad() {
      // Create a search control
      var searchControl = new google.search.SearchControl();

      // Add in a full set of searchers
      var localSearch = new google.search.LocalSearch();
      searchControl.addSearcher(localSearch);
      searchControl.addSearcher(new google.search.WebSearch());
      searchControl.addSearcher(new google.search.VideoSearch());
      searchControl.addSearcher(new google.search.BlogSearch());
      searchControl.addSearcher(new google.search.NewsSearch());
      searchControl.addSearcher(new google.search.ImageSearch());
      searchControl.addSearcher(new google.search.BookSearch());
      searchControl.addSearcher(new google.search.PatentSearch());

      // Set the Local Search center point
      localSearch.setCenterPoint(\"\");

      // tell the searcher to draw itself and tell it where to attach
      searchControl.draw(document.getElementById(\"searchcontrol\"));

      // execute an inital search
      searchControl.execute(\"".$stripslashes(htmlspecialchars($_GET['q']))."\");
    }
    google.setOnLoadCallback(OnLoad);

    //]]>
    </script>
";


require(AT_INCLUDE_PATH.'header.inc.php');
?>
<div id="searchcontrol" style="width:100%"><?php echo _AT('loading'); ?></div>
<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>