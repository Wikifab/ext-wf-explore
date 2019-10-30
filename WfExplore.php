<?php

wfLoadExtension('Explore');

/** OLD STUF
# Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'WfExplore',
	'author' => 'Pierre Boutet',
	//'url' => 'https://www.mediawiki.org/wiki/Extension:WfExplore',
	'descriptionmsg' => 'wfexplore-desc',
	'version' => '0.1.0',
);
$wgAutoloadClasses['Explore\\InputBox'] = __DIR__ . '/includes/InputBox.php';
$wgAutoloadClasses['SpecialWfExplore'] = __DIR__ . '/includes/SpecialWfExplore.php'; # Location of the SpecialWfSearch class (Tell MediaWiki to load this file)
$wgAutoloadClasses['WfExploreQueryParser'] = __DIR__ . "/includes/WfExploreQueryParser.php";
$wgAutoloadClasses['WfExploreTag'] = __DIR__ . "/includes/WfExploreTag.php";
$wgAutoloadClasses['WfExploreCore'] = __DIR__ . "/includes/WfExploreCore.php";
$wgAutoloadClasses['ApiGetPropertyValues'] = __DIR__ . '/includes/ApiGetPropertyValues.php';
$wgAutoloadClasses['WikifabExploreResultFormatter'] = __DIR__ . '/includes/WikifabExploreResultFormatter.php'; # Location of the WikifabSearchResultFormatter class
$wgAutoloadClasses['Skins\\Chameleon\\Components\\ExploreSearchBar'] = __DIR__ . '/includes/ChameleonComponents/ExploreSearchBar.php'; # Location of the WikifabSearchResultFormatter class
$wgAutoloadClasses['WfTutorialUtils'] = __DIR__ . '/includes/WfTutorialUtils.php'; # tools for using tutorial forms pages


$wgMessagesDirs['WfExplore'] = __DIR__ . "/i18n"; # Location of localisation files (Tell MediaWiki to load them)
//$wgExtensionMessagesFiles['WfExploreAlias'] = __DIR__ . '/WfExplore.alias.php'; # Location of an aliases file

// Allow translation of the parser function name
$wgExtensionMessagesFiles['WfExploreMagic'] = __DIR__ . '/WfExplore.magic.php';
$wgSpecialPages['WfExplore'] = 'SpecialWfExplore'; # Tell MediaWiki about the new special page and its class name
$egWfExploreLayoutForm= __DIR__ . '/views/LayoutExploreForm.php';
//$GLOBALS['egChameleonLayoutFileSearchResult'];

$wgExploreDefaultsFieldsDisplayValues = [
		'Main_Picture' => 'No-image-yet.jpg',
		'group-logo' => 'no-logo-group.gif'
];

$wgAPIModules['exploregetpropertyvalues'] = 'ApiGetPropertyValues';

$wgResourceModules['ext.wikifab.wfExplore.js'] = array(
		'scripts' => array(
			'js/jquery.dynatree.js',
			'js/selectize.js',
			'wf-explore.js',
			'js/form-fulltext-autocompletion.js'
		),
		'styles' => array(
			'css/ui.dynatree.css',
			'css/normalize.css',
			'wf-explore.css'
		),
		'localBasePath' => __DIR__,
		'remoteExtPath' => 'Explore',
		'messages' => array(
				'wfexplore-load-more-tutorials-previous'
		),
		'dependencies' => array(
				'jquery.ui.datepicker',
				'jquery.ui.autocomplete'
		)
);

$wgResourceModules['ext.wikifab.wfexplore.css'] = array(
	'styles' => array(
		'wf-explore.css',
		'css/normalize.css'
	),
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'Explore'
);

$wgResourceModules['ext.wikifab.wfexplore'] = array(
	'dependencies' => array(
			'ext.wikifab.wfexplore.css',
			'ext.wikifab.wfExplore.js'
	),
);

$wfexploreExtractTags = true;
$wgExploreIsLocalized = false;

$wgExploreResultsLayouts = [
		'event' => __DIR__ . '/views/layout-event.html'
];

$wgHooks['ParserFirstCallInit'][] = 'WfExploreParserFunctions';
$wgHooks['PageRenderingHash'][] = 'wfExploreOnPageRenderingHash';

// this global var is used to record is parser has been called
// it should be move inside a class, with the 2 functions below
$wfExploreGlobalParsedFunction = false;

# Parser function to insert a link changing a tab.
function WfExploreParserFunctions( $parser ) {
	global $wgOut, $wfExploreGlobalParsedFunction;

	// we record that parsing function has been used to chang cache hash
	$wfExploreGlobalParsedFunction = true;

	$wgOut->addModuleStyles(
		array(
				'ext.wikifab.wfexplore.css'
		)
	);
	$wgOut->addModules( array( 'ext.wikifab.wfexplore' ) );
	$parser->setFunctionHook( 'displayExplore', array('WfExploreTag', 'addSampleParser' ));
	$parser->setFunctionHook( 'exploreQuery', array('WfExploreQueryParser', 'addSampleParser' ));
	$parser->setFunctionHook( 'exploreinputbox', array( 'Explore\\InputBox', 'render' ) );
	return true;
}

function wfExploreOnPageRenderingHash( &$confstr, User $user, &$forOptions ) {
	global $wfExploreGlobalParsedFunction, $wgRequest;

	if($wgRequest->getMethod() == 'GET') {
		// if parse function has been used, we add query to cache hash key
		if (isset($wfExploreGlobalParsedFunction) && $wfExploreGlobalParsedFunction) {
			foreach ($wgRequest->getValues() as $key => $val) {
				 $confstr .= '-' . $key . "-" . $val;
			}
		}
	}
	return true;
}

**/


