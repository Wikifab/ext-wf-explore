[1mdiff --git a/WfExplore.magic.php b/WfExplore.magic.php[m
[1mindex b108fbf..9be253b 100644[m
[1m--- a/WfExplore.magic.php[m
[1m+++ b/WfExplore.magic.php[m
[36m@@ -1,13 +1,15 @@[m
 <?php[m
[31m- [m
[32m+[m
 $magicWords = array();[m
[31m- [m
[32m+[m
 /** English[m
  * @author Your Name (YourUserName)[m
  */[m
 $magicWords['en'] = array([m
    'displayExplore' => array( 0, 'displayExplore' ),[m
[32m+[m[32m   'exploreQuery' => array( 0, 'exploreQuery' ),[m
 );[m
 $magicWords['fr'] = array([m
    'displayExplore' => array( 0, 'displayExplore' ),[m
[32m+[m[32m   'exploreQuery' => array( 0, 'exploreQuery' ),[m
 );[m
[1mdiff --git a/WfExplore.php b/WfExplore.php[m
[1mindex 63a31ee..dff4f38 100644[m
[1m--- a/WfExplore.php[m
[1m+++ b/WfExplore.php[m
[36m@@ -13,6 +13,7 @@[m [m$wgExtensionCredits['specialpage'][] = array([m
 	'version' => '0.1.0',[m
 );[m
 $wgAutoloadClasses['SpecialWfExplore'] = __DIR__ . '/includes/SpecialWfExplore.php'; # Location of the SpecialWfSearch class (Tell MediaWiki to load this file)[m
[32m+[m[32m$wgAutoloadClasses['WfExploreQueryParser'] = __DIR__ . "/includes/WfExploreQueryParser.php";[m
 $wgAutoloadClasses['WfExploreTag'] = __DIR__ . "/includes/WfExploreTag.php";[m
 $wgAutoloadClasses['WfExploreCore'] = __DIR__ . "/includes/WfExploreCore.php";[m
 $wgAutoloadClasses['WikifabExploreResultFormatter'] = __DIR__ . '/includes/WikifabExploreResultFormatter.php'; # Location of the WikifabSearchResultFormatter class[m
[36m@@ -49,6 +50,7 @@[m [m$wgHooks['ParserFirstCallInit'][] = 'WfExploreParserFunctions';[m
 # Parser function to insert a link changing a tab.[m
 function WfExploreParserFunctions( $parser ) {[m
 	$parser->setFunctionHook( 'displayExplore', array('WfExploreTag', 'addSampleParser' ));[m
[32m+[m	[32m$parser->setFunctionHook( 'exploreQuery', array('WfExploreQueryParser', 'addSampleParser' ));[m
 	//$parser->setFunctionTagHook('displayTutorialsList', array('WfSampleDisplay', 'addSampleParser' ), array());[m
 	return true;[m
 }[m
[1mdiff --git a/includes/WfExploreCore.php b/includes/WfExploreCore.php[m
[1mindex cc0a576..8e43b7b 100644[m
[1m--- a/includes/WfExploreCore.php[m
[1m+++ b/includes/WfExploreCore.php[m
[36m@@ -388,6 +388,10 @@[m [mclass WfExploreCore {[m
 		$offset = ($page - 1 ) * $limit;[m
 [m
 		$query = '';[m
[32m+[m
[32m+[m		[32mif (isset($params['query'])) {[m
[32m+[m			[32m$query = $params['query'];[m
[32m+[m		[32m}[m
 		foreach ($this->specialsFields as $key => $specialField) {[m
 			if (isset($selectedOptions[$key])) {[m
 				unset($selectedOptions[$key]);[m
[36m@@ -540,7 +544,14 @@[m [mclass WfExploreCore {[m
 [m
 	public function getSearchResultsHtml($param = []) {[m
 [m
[31m-		$out = "<div class='searchresults'>\n";[m
[32m+[m		[32m$defaultParams = [[m
[32m+[m				[32m'noLoadMoreButton' => false,[m
[32m+[m				[32m'replaceClass' => 'searchresults'[m
[32m+[m		[32m];[m
[32m+[m
[32m+[m
[32m+[m		[32m$param = array_merge($defaultParams, $param);[m
[32m+[m		[32m$out = "<div class='".$param['replaceClass']."'>\n";[m
 [m
 		$out .= '<a id="explore-page'.$this->page . '" name="page'.$this->page . '"></a>';[m
 [m
[36m@@ -557,7 +568,7 @@[m [mclass WfExploreCore {[m
 		$out .= $wikifabExploreResultFormatter->render();[m
 [m
 		// load More button[m
[31m-		if(count($this->results) >= $this->pageResultsLimit) {[m
[32m+[m		[32mif(count($this->results) >= $this->pageResultsLimit && ! $param['noLoadMoreButton']) {[m
 			$out .= '<div class="load-more">'.wfMessage( $this->message['load-more'] )->text(). '</div>';[m
 		}[m
 [m
