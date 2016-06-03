<?php

class WfExploreCore {

	private $request;
	private $params;

	private $pageResultsLimit = 8;

	private $specialsFields = array(
			'Complete' => array('query' => '[[Complete::!none]]')
	);

	public function setRequest($request, $params = []) {
		$this->request = $request;
		$this->params = $params;
	}



	public function getQueryParam ($category, $valuesIds) {
		if ($category == 'Cost') {
			$fourchetteCout = [
				'0-10' => ['min' => 0, 'max' => 10],
				'10-50' => ['min' => 10, 'max' => 50],
				'50-100' => ['min' => 50, 'max' => 100],
				'100-inf' => ['min' => 100, 'max' => 'inf']
			];
			$min = null;
			$max = null;
			foreach ($valuesIds as $value) {
				if( $min === null || $min > $fourchetteCout[$value]['min']) {
					$min = $fourchetteCout[$value]['min'];
				}
				if( $max != 'inf' && ($max === null || $max < $fourchetteCout[$value]['max'] || $fourchetteCout[$value]['max'] == 'inf')) {
					$max = $fourchetteCout[$value]['max'];
				}
			}
			$result = '';
			if($min) {
				$result .= '[[' . $category . '::>' . $min. ']]';
			}
			if($max && $max != 'inf') {
				$result .= ' [[' . $category . '::<' . $max. ']]';
			}
			return $result;

		} else {
			return '[[' . $category . '::' . implode('||', $valuesIds) . ']]';
		}
	}

	private function getFilters() {

		//$property =new SMWDIProperty('Type');
		//var_dump($property);

		$type = array(
			wfMessage( 'wfexplore-category-name-creation' )->text() => wfMessage( 'wfexplore-category-name-creation' )->text(),
			wfMessage( 'wfexplore-category-name-technique' )->text() => wfMessage( 'wfexplore-category-name-technique' )->text(),
		);
		$categories = array(
			wfMessage( 'wfexplore-category-name-art' )->text() => wfMessage( 'wfexplore-category-name-art' )->text(),
			wfMessage( 'wfexplore-category-name-clothing-accessories' )->text() => wfMessage( 'wfexplore-category-name-clothing-accessories' )->text(),
			wfMessage( 'wfexplore-category-name-decoration' )->text() => wfMessage( 'wfexplore-category-name-decoration' )->text(),
			wfMessage( 'wfexplore-category-name-electronics' )->text() => wfMessage( 'wfexplore-category-name-electronics' )->text(),
			wfMessage( 'wfexplore-category-name-energy' )->text() => wfMessage( 'wfexplore-category-name-energy' )->text(),
			wfMessage( 'wfexplore-category-name-food-agriculture' )->text() => wfMessage( 'wfexplore-category-name-food-agriculture' )->text(),
			wfMessage( 'wfexplore-category-name-furniture' )->text() => wfMessage( 'wfexplore-category-name-furniture' )->text(),
			wfMessage( 'wfexplore-category-name-health-wellbeing' )->text() => wfMessage( 'wfexplore-category-name-health-wellbeing' )->text(),
			wfMessage( 'wfexplore-category-name-house' )->text() => wfMessage( 'wfexplore-category-name-house' )->text(),
			wfMessage( 'wfexplore-category-name-music-sound' )->text() => wfMessage( 'wfexplore-category-name-music-sound' )->text(),
			wfMessage( 'wfexplore-category-name-play-outside' )->text() => wfMessage( 'wfexplore-category-name-play-outside' )->text(),
			wfMessage( 'wfexplore-category-name-robotics' )->text() => wfMessage( 'wfexplore-category-name-robotics' )->text(),
			wfMessage( 'wfexplore-category-name-science-biology' )->text() => wfMessage( 'wfexplore-category-name-science-biology' )->text(),
			wfMessage( 'wfexplore-category-name-transport-mobility' )->text() => wfMessage( 'wfexplore-category-name-transport-mobility' )->text(),
		);
		$diff = array(
			wfMessage( 'wfexplore-category-name-very-easy' )->text() => wfMessage( 'wfexplore-category-name-very-easy' )->text(),
			wfMessage( 'wfexplore-category-name-easy' )->text() => wfMessage( 'wfexplore-category-name-easy' )->text(),
			wfMessage( 'wfexplore-category-name-medium' )->text() => wfMessage( 'wfexplore-category-name-medium' )->text(),
			wfMessage( 'wfexplore-category-name-hard' )->text() => wfMessage( 'wfexplore-category-name-hard' )->text(),
			wfMessage( 'wfexplore-category-name-very-hard' )->text() => wfMessage( 'wfexplore-category-name-very-hard' )->text(),
		);
		$cout = array(
			'1' => '€',
			'2' => '€€',
			'3' => '€€€'
		);
		$fourchetteCout = array(
			'0-10' => '0 - 10',
			'10-50' => '10 - 50',
			'50-100' => '50 - 100',
			'100-inf' => '100 - ∞'
		);
		return array (
			'Type' => $type,
			'area' => $categories,
			'Difficulty' => $diff,
			//'Cost' => $cout,
			'Cost' => $fourchetteCout
		);
	}

	private function getFiltersData() {

		$categoriesNames = array(
			'Type' => wfMessage( 'wfexplore-type' )->text() ,
			'area' =>  wfMessage( 'wfexplore-category' )->text(),
			'Difficulty' => wfMessage( 'wfexplore-difficulty' )->text() ,
			'Cost' => wfMessage( 'wfexplore-cost' )->text() ,
			'Complete' => 'Complete',
		);


		$filters = $this->getFilters();
		$result = array();
		foreach ($filters as $filtersKey => $values) {
			$filter = array(
				'id' => $filtersKey,
				'name' => $categoriesNames[$filtersKey],
				'values' => array()
			);
			foreach ($values as $key => $value) {
				$filter['values'][$key] = array(
					'id' => $key,
					'name' => $value
				);
			}
			$result[$filtersKey] = $filter;
		}
		return $result;
	}

	private function addHiddenFields($filters) {

		global $wgWfExploreCompleteField;

		if(isset($wgWfExploreCompleteField) && $wgWfExploreCompleteField) {
			$fieldComplete = array(
					'id' => 'complete',
					'name' => 'Complete',
					'values' => array(
							'1' => array(
							'id' => 1,
							'name' => 'Complete',
						)
					)
			);
			$filters['complete'] = $fieldComplete;
		}
		return $filters;
	}

	/**
	* return selected Options
	*/
	private function getSelectedAdvancedSearchOptions($request, $params = []) {
		$filtersData = $this->getFiltersData();
		$filtersData = $this->addHiddenFields($filtersData);

		if( !$request && ! $params) {
			return array();
		}

		$results = array();

		foreach ($filtersData as $category => $values) {

			foreach ($values['values'] as $key => $value) {
				$fieldName = "wf-expl-$category-" . $value['id'];
				$fieldName = str_replace(' ', '_', $fieldName);
				if ( ($request && $request->getCheck( $fieldName )) || isset($params[$category])) {
					if( ! isset($results[$category])) {
						$results[$category] = array();
					}
					$results[$category][$value['id']] = array(
						'category' => $category,
						'valueName' => $value['name'],
						'valueId' => $value['id']
						);
				}
			}
		}
		return $results;
	}


	public function getHtmlForm() {
		$page = SpecialPage::getTitleFor( 'WfExplore' );
		$url = $page->getLinkURL();

		// start rendering the page
		$out = Xml::openElement(
				'form',
				array(
					'id' => 'wfExplore',
					'method' => 'get',
					'action' => $url,
				)
		);

		$out .= Xml::openElement( 'div', array( 'id' => 'mw-search-top-table' ) ) .
			Html::hidden( 'title', $page->getPrefixedText() ).
			Html::hidden( 'page', 1 ).
			Xml::closeElement( 'div' ) ;

		$out .= Xml::openElement( 'div', array( 'class' => 'mw-search-formheader' ) );
		$out .= Xml::element( 'div', array( 'style' => 'clear:both' ), '', false );
		$out .= Xml::closeElement( 'div' );


		$out .= $this->getSearchForm($this->request);

		$out .= Xml::closeElement( 'form' )	;

		$out .= '<div class="loader_container"><div class="loader" style="display:none"><i class="fa fa-spinner fa-pulse"></i></div></div>';
		return $out;
	}

	/**
	* return html code for draw form
	*
	*/
	private function getSearchForm($request, $params = []) {

		// get form options :
		$filtersData = $this->getFiltersData();
		// get selected Options
		$selectedOptions = $this->getSelectedAdvancedSearchOptions($request);
		$params = $this->params;

		ob_start();
		include ($GLOBALS['egWfExploreLayoutForm']);
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	public  function executeSearch($request, $params = []) {
		$this->setRequest( $request, $params);
		$selectedOptions = $this->getSelectedAdvancedSearchOptions($request, $params);
		$offset = 0;

		if($request) {
			$page = max(1,$request->getVal( 'page', 1));
		} else {
			$page = 1;
		}


		$offset = ($page - 1 ) * $this->pageResultsLimit;

		$query = '';

		foreach ($this->specialsFields as $key => $specialField) {
			if (isset($selectedOptions[$key])) {
				unset($selectedOptions[$key]);
				$query .= $specialField['query'];
			}
		}

		foreach ($selectedOptions as $category => $values) {
			$valuesIds  = array();
			foreach ($values as $value) {
				$valuesIds[] = $value['valueId'];
			}
			$query .= ' ' . $this->getQueryParam($category, $valuesIds);
			//$query .= ' [[' . $category . '::' . implode('||', $valuesIds) . ']]';
		}
		if( ! $query ) {
			$query = '[[Area::*]]';
		}
		$this->results = $this->processSemanticQuery($query, $this->pageResultsLimit, $offset);

		return $this->results;
	}

	public function getNbResults() {
		return $this->queryCount;
	}

	/**
	 * returns an array of pages that are result of the semantic query
	 * @param $rawQueryString string - the query string like [[Category:Trees]][[age::>1000]]
	 * @return array of SMWDIWikiPage objects representing the result
	 */
	private function processSemanticQuery($rawQuery, $limit = 20, $offset = 0, $sort = null) {
		global $wfeSortField;

		$rawQueryArray = array( trim($rawQuery) );
		if($limit) {
			$rawQueryArray['limit'] = $limit;
		}
		if($offset) {
			$rawQueryArray['offset'] = $offset;
		}
		if($sort) {
			$rawQueryArray['sort'] = $sort;
			$rawQueryArray['order'] = 'desc';
		} else if (isset($wfeSortField)  && $wfeSortField) {
			$rawQueryArray['sort'] = $wfeSortField;
			$rawQueryArray['order'] = 'desc';
		}
		SMWQueryProcessor::processFunctionParams( $rawQueryArray, $queryString, $processedParams, $printouts );
		SMWQueryProcessor::addThisPrintout( $printouts, $processedParams );
		$processedParams = SMWQueryProcessor::getProcessedParams( $processedParams, $printouts );
		$queryCount = SMWQueryProcessor::createQuery( $queryString,
			$processedParams,
			SMWQueryProcessor::SPECIAL_PAGE, 'count', $printouts );
		$this->queryCount = SFUtils::getSMWStore()->getQueryResult( $queryCount );

		$queryObj = SMWQueryProcessor::createQuery( $queryString,
			$processedParams,
			SMWQueryProcessor::SPECIAL_PAGE, '', $printouts );
		$queryObj->setLimit($limit);
		$res = SFUtils::getSMWStore()->getQueryResult( $queryObj );
		$pages = $res->getResults();

		return $pages;
	}

	public function getSearchResultsHtml() {

		$out = "<div class='searchresults'>\n";
		//$out = "<div class='searchresults'>\n";

		// prev/next links
		/*$prevnext = null;
		if ( $num || $this->offset ) {
			// Show the create link ahead
			if ( $totalRes > $this->limit || $this->offset ) {
				$prevnext = $this->getLanguage()->viewPrevNext(
					$this->getPageTitle(),
					$this->offset,
					$this->limit,
					$this->advancedSearchOptions() + array( 'wfsearch' => $term ),
					$this->limit + $this->offset >= $totalRes
				);
			}
		}*/


		$this->wikifabExploreResultFormatter = new WikifabExploreResultFormatter();

		$this->wikifabExploreResultFormatter->setResults($this->results);

		$out .= $this->wikifabExploreResultFormatter->render();

		// load More button
		if(count($this->results) >= $this->pageResultsLimit) {
			$out .= '<div class="load-more">'.wfMessage( 'wfexplore-load-more-tutorials' )->text(). '</div>';
		}

		$out .= "</div>\n" ;

		return $out;
	}
}