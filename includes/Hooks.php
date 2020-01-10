<?php

namespace Explore;

use User;

class Hooks {

	static public function onExtension() {
		global $egWfExploreLayoutForm, $wgExploreDefaultsFieldsDisplayValues;

		if (! isset($egWfExploreLayoutForm)) {
			$egWfExploreLayoutForm= __DIR__ . '/../views/LayoutExploreForm.php';
		}

		if (! isset($wgExploreDefaultsFieldsDisplayValues)) {
			$wgExploreDefaultsFieldsDisplayValues = [
					'Main_Picture' => 'No-image-yet.jpg',
					'group-logo' => 'no-logo-group.gif'
			];
		}
		if (!isset($wfexploreCategoriesNames) ) {
			$wfexploreCategoriesNames = [];
		}

		$defaultCategoriesNames = [];
		$defaultCategoriesNames['Modification_date'] = wfMessage("wfexplore-filters-last-modified");
		$defaultCategoriesNames['Page_creator'] = wfMessage("wfexplore-filters-page-creator");
		$defaultCategoriesNames['Google_Analytics_Views'] = wfMessage('wfexplore-filters-views');
		$defaultCategoriesNames['I_did_it'] = wfMessage('wfexplore-filters-ididit');
		$defaultCategoriesNames['Favorites'] = wfMessage('wfexplore-filters-favorites');

		$wfexploreCategoriesNames = array_merge($defaultCategoriesNames, $wfexploreCategoriesNames);

	}

	static public function onParserFirstCallInit( $parser ) {
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


	static public function onPageRenderingHash( &$confstr, User $user, &$forOptions ) {
		global $wfExploreGlobalParsedFunction, $wgRequest;

		if($wgRequest->getMethod() == 'GET') {
			// if parse function has been used, we add query to cache hash key
			if (isset($wfExploreGlobalParsedFunction) && $wfExploreGlobalParsedFunction) {
				foreach ($wgRequest->getValues() as $key => $val) {
					if (is_array($val)) {
						$val = md5(json_encode($val));
					}
					$confstr .= '-' . $key . "-" . $val;
				}
			}
		}
		return true;
	}
}