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



	public static function addSampleParser( $input, $query = '[Area::!none]', $limit = 4, $sort = 'editdate', $layout = null) {

		// new way to get function params, read dynamicaly params (params are separated by '|' )
		$options = self::extractOptions( array_slice(func_get_args(), 1) );

		// old way to reads params, all params are concatenated in the first one, separated by ','
		$oldparams = [
				$query , $limit, $sort , $layout
		];


		$input->getOutput ()->addModuleStyles( array(
			'mediawiki.special', 'mediawiki.special.search', 'mediawiki.ui', 'mediawiki.ui.button',
			'mediawiki.ui.input',
		) );
		$input->getOutput ()->addModules( 'ext.wikifab.wfExplore.js');

		$WfExploreCore = new WfExploreCore();

		$params = array();

		$params['limit'] = intval($options['limit']);

		$params['query'] = $options['query'];

		$paramsOutput = [
				'showPreviousButton' => true,
				'noLoadMoreButton' => true,
				'replaceClass' => 'exploreQueryResult',
				'isEmbed' => true
		];

		if(isset($options['layout'])) {
			$params['layout'] = $options['layout'];
			$paramsOutput['layout'] = $paramsOutput['layout'];
		}

		$WfExploreCore->executeSearch( $request = null , $params);

		$out = "";

		$out .= $WfExploreCore->getSearchResultsHtml($paramsOutput);

		return array( $out, 'noparse' => true, 'isHTML' => true );
	}

	/**
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value. If no = is provided,
	 * true is assumed like this: [name] => true
	 *
	 * @param array string $options
	 * @return array $results
	 */
	static function extractOptions( array $options ) {
		$results = array();
		$hasNamedParams = false;
		// first param may be not named, in that case the first one is query, and second is the limit

		foreach ( $options as $key => $option ) {
			if (substr($option, 0,1) == '[') {
				// first param query may be not named
			}
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) === 2 && strpos('[', $pair[0]) === false) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				$results[$name] = $value;
				$hasNamedParams = true;
			}

			if ( ! $hasNamedParams && count($results) < 2) {
				if (count($results) == 0) {
					$results['query'] = trim($option);
				} else if(is_numeric($option)){
					$results['limit'] = trim($option);
				} else {
					$name = trim( $pair[0] );
					$results[$name] = true;
				}
			} else if ( count( $pair ) === 1 ) {
				$name = trim( $pair[0] );
				$results[$name] = true;
			}
		}
		//Now you've got an array that looks like this:
		//  [foo] => "bar"
		//	[apple] => "orange"
		//	[banana] => true

		return $results;
	}

}