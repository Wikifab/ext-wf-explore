<?php
/**
 * class for include explore area
 *
 * @file
 * @ingroup Extensions
 *
 * @author Pierre Boutet
 */

class WfExploreTag {



	public static function addSampleParser( $input, $filters = 'completeonly') {

		$filters = explode(',', $filters);

		$input->getOutput ()->addModuleStyles( array(
			'mediawiki.special', 'mediawiki.special.search', 'mediawiki.ui', 'mediawiki.ui.button',
			'mediawiki.ui.input',
		) );
		$input->getOutput ()->addModules( 'ext.wikifab.wfExplore.js');

		$WfExploreCore = new WfExploreCore();

		$params = $_GET;

		if (false !== array_search('completeonly', $filters)) {
			$params['complete'] = 'complete';
		}

		$WfExploreCore->executeSearch( $request = null , $params);

		$out = "";

		$out .= $WfExploreCore->getHtmlForm($params);

		$paramsOutput = ['showPreviousButton' => true];
		$out .= $WfExploreCore->getSearchResultsHtml($paramsOutput);

		return array( $out, 'noparse' => true, 'isHTML' => true );
	}

}