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

		// new way to get function params, read dynamicaly params (params are separated by '|' )
		$options = self::extractOptions( array_slice(func_get_args(), 1) );

		// old way to reads params, all params are concatenated in the first one, separated by ','
		$filters = explode(',', $filters);
		if(count($filters > 1)) {
			// if only one params, it will set coreclty with the new way to extrct params
			foreach ($filters as $key) {
				$options[$key] = true;
			}
		}


		$input->getOutput ()->addModuleStyles( array(
			'mediawiki.special', 'mediawiki.special.search', 'mediawiki.ui', 'mediawiki.ui.button',
			'mediawiki.ui.input',
		) );
		$input->getOutput ()->addModules( 'ext.wikifab.wfExplore.js');

		$WfExploreCore = new WfExploreCore();

		$params = $_GET;

		if (isset($options['query'])) {
			$params['query'] = $options['query'];
		}
		if (isset($options['completeonly'])) {
			$params['complete'] = 'complete';
		}
		if (isset($options['layout'])) {
			$params['layout'] = $options['layout'];
		}
		if (isset($options['sort'])) {
			$params['sort'] = $options['sort'];
		}
		if (isset($options['order'])) {
			$params['order'] = $options['order'];
		}

		$WfExploreCore->executeSearch( $request = null , $params);

		$out = "";

		$out .= $WfExploreCore->getHtmlForm($params);

		$paramsOutput = ['showPreviousButton' => true];
		if (isset($options['layout'])) {
			$paramsOutput['layout'] = $options['layout'];
		}
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

		foreach ( $options as $option ) {
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) === 2 ) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				$results[$name] = $value;
			}

			if ( count( $pair ) === 1 ) {
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