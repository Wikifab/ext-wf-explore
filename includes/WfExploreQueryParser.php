<?php
/**
 * class for include explore area
 *
 * @file
 * @ingroup Extensions
 *
 * @author Pierre Boutet
 */

class WfExploreQueryParser {



	public static function addSampleParser( $input, $query = '[Area::!none]', $limit = 4, $sort = 'editdate') {


		$input->getOutput ()->addModuleStyles( array(
			'mediawiki.special', 'mediawiki.special.search', 'mediawiki.ui', 'mediawiki.ui.button',
			'mediawiki.ui.input',
		) );
		$input->getOutput ()->addModules( 'ext.wikifab.wfExplore.js');

		$WfExploreCore = new WfExploreCore();

		$params = array();

		$params['limit'] = intval($limit);

		$params['query'] = $query;

		$WfExploreCore->executeSearch( $request = null , $params);

		$out = "";

		$paramsOutput = [
				'showPreviousButton' => true,
				'noLoadMoreButton' => true,
				'replaceClass' => 'exploreQueryResult',
				'isEmbed' => true
		];
		$out .= $WfExploreCore->getSearchResultsHtml($paramsOutput);

		return array( $out, 'noparse' => true, 'isHTML' => true );
	}

}