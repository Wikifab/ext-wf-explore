<?php

class WfExploreCore {

	private $request;
	private $params;

	private $pageResultsLimit = 8;

	private $namespaces = false;

	private $formatter = null;
	private $searchPageTitle = null;
	private $filters = null;

	private $extractTags = null;
	private $extractedTags = null;

	private $message = array(
			'load-more' => 'wfexplore-load-more-tutorials',
			'load-more-previous' => 'wfexplore-load-more-tutorials-previous'
	);

	private $specialsFields = array(
			'Complete' => array('query' => '[[Complete::!none]]')
	);

	public function __construct() {
		if (isset($GLOBALS['wfexploreExtractTags'])) {
			$this->extractTags = $GLOBALS['wfexploreExtractTags'];
		}
	}

	public function setFormatter($formatter) {
		$this->formatter = $formatter;
	}

	public function getFormatter() {
		if ( ! $this->formatter) {
			$this->formatter = new WikifabExploreResultFormatter();
		}
		return $this->formatter;
	}
	public function setMessageKey($message, $key) {
		$this->message[$message] = $key;
	}
	public function setSearchPageTitle(\Title $page) {
		$this->searchPageTitle = $page;
	}
	public function getSearchPageTitle() {
		if ( ! $this->searchPageTitle) {
			$this->searchPageTitle = SpecialPage::getTitleFor( 'WfExplore' );
		}
		return $this->searchPageTitle;
	}

	public function setPageResultsLimit($nb) {
		$this->pageResultsLimit = $nb;
	}

	public function setRequest($request, $params = []) {
		$this->request = $request;
		$this->params = $params;
	}

	/**
	 * set namespace where to look
	 * @param string[] $namespaces
	 */
	public function setNamespace($namespaces) {
		$this->namespaces = $namespaces ;
	}



	public function getQueryParam ($category, $valuesIds, $andCondition) {
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
			if($andCondition) {
				$result = '';
				foreach ($valuesIds as $valueId) {
					$result .= '[[' . $category . '::' .  $valueId . ']]';
				}
				return $result;
			} else {
				return '[[' . $category . '::' . implode('||', $valuesIds) . ']]';
			}
		}
	}

	public function setFilters($filters) {
		$this->filters = $filters;
	}

	private function getFilters() {

		//$property =new SMWDIProperty('Type');
		//var_dump($property);

		if ($this->filters !== null) {
			return $this->filters;
		}

		if (isset($GLOBALS['wfexploreCategories'])) {
			return $GLOBALS['wfexploreCategories'];
		}

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
			wfMessage( 'wfexplore-category-name-play-recreation' )->text() => wfMessage( 'wfexplore-category-name-play-recreation' )->text(),
			wfMessage( 'wfexplore-category-name-house' )->text() => wfMessage( 'wfexplore-category-name-house' )->text(),
			wfMessage( 'wfexplore-category-name-machines-tools' )->text() => wfMessage( 'wfexplore-category-name-machines-tools' )->text(),
			wfMessage( 'wfexplore-category-name-music-sound' )->text() => wfMessage( 'wfexplore-category-name-music-sound' )->text(),
			wfMessage( 'wfexplore-category-name-play-outside' )->text() => wfMessage( 'wfexplore-category-name-play-outside' )->text(),
			wfMessage( 'wfexplore-category-name-recycling-upcycling' )->text() => wfMessage( 'wfexplore-category-name-recycling-upcycling' )->text(),
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

		// manage checkbox filters :
		foreach ($filtersData as $category => $values) {

			foreach ($values['values'] as $key => $value) {
				$fieldName = "wf-expl-$category-" . $value['id'];
				$fieldName = str_replace(' ', '_', $fieldName);
				if ( ($request && $request->getCheck( $fieldName )) || isset($params[$category]) || isset($params[$fieldName])) {
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

		// manage full text filters :
		$fullTextFields = array('Tags');
		foreach ($fullTextFields as $field) {
			$fieldName = "wf-expl-" . $field;
			if ($request && $request->getValues( $fieldName ) || isset($params[$fieldName])) {
				$value = isset($params[$fieldName]) ? $params[$fieldName] : $request->getValues( $fieldName )[$fieldName];
				//var_dump($value);
				if($value) {
					$results[$field] = array(
							'value' => $value,
							'type' => 'text'
					);
				}
			}
		}
		return $results;
	}


	public function getHtmlForm($params = []) {
		$page = $this->getSearchPageTitle();
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

		$out .= $this->getSearchForm($this->request, $params);

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
		$selectedOptions = $this->getSelectedAdvancedSearchOptions($request, $params);
		$params = $this->params;

		//$tags = array('CNC', 'Jeux', 'Impression 3D');
		$tags = $this->getTags();

		ob_start();
		include ($GLOBALS['egWfExploreLayoutForm']);
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	private function getQueryParamsWithType($category, $values) {

		$type = isset($values['type']) ? $values['type'] : 'checkbox';
		$andCondition = false;

		switch ($type) {
			case 'text' :
				$valuesIds = explode(',',$values['value']);
				foreach ($valuesIds as $key => $val) {
					$valuesIds[$key] = "~" . $val;
				}
				$andCondition = true;
				break;
			case 'checkbox' :
			default :
				$valuesIds  = array();
				foreach ($values as $value) {
					$valuesIds[] = $value['valueId'];
				}
				break;
		}
		//var_dump($values);

		return $this->getQueryParam($category, $valuesIds, $andCondition);
	}

	public  function executeSearch($request, $params = [], $save = true) {
		$this->setRequest( $request, $params);
		$selectedOptions = $this->getSelectedAdvancedSearchOptions($request, $params);
		$offset = 0;

		if($request) {
			$page = max(1,$request->getVal( 'page', 1));
		} else {
			$page = 1;
		}
		if (isset($params['page'])) {
			$page = $params['page'];
		}

		$limit = $this->pageResultsLimit;
		if (isset($params['limit'])) {
			$limit = $params['limit'];
		}

		if($save) {
			$this->page = $page;
		}

		$offset = ($page - 1 ) * $limit;

		$query = '';

		foreach ($this->specialsFields as $key => $specialField) {
			if (isset($selectedOptions[$key])) {
				unset($selectedOptions[$key]);
				$query .= $specialField['query'];
			}
		}

		foreach ($selectedOptions as $category => $values) {
			$query .= ' ' . $this->getQueryParamsWithType($category, $values);
			//$query .= ' [[' . $category . '::' . implode('||', $valuesIds) . ']]';
		}

		if ($this->namespaces) {
			$query .= '[[' . implode(':+||',$this->namespaces) . ':+]]';
			//$query .= '[[group-type::*]]';
			//$query .= '[[Group:*]]';
		}
		if( ! $query ) {
			$query = '[[Area::*]]';
		}
		$results = $this->processSemanticQuery($query, $limit, $offset);
		if($save) {
			$this->results = $results;
		}
		return $results;
	}


	public function getNbResults() {
		return $this->queryCount;
	}

	/**
	 * return tags :
	 * - get all possible tags according to search results,
	 * - remove tags already selected,
	 * - return 10 of them,
	 */
	public function getTags() {
		if( ! $this->extractedTags) {
			return [];
		}
		if( isset($this->extractedTagsSelection)) {
			return $this->extractedTagsSelection;
		}
		$selectedOptions = $this->getSelectedAdvancedSearchOptions($this->request);
		$selectedTags = isset($selectedOptions['Tags']['value']) ?
				explode(',', $selectedOptions['Tags']['value']) : [];
		$selectedTags = array_flip($selectedTags);

		$extractedTags = $this->extractedTags;

		//remove tags already selected,
		$tagsCounters = array_diff_key($extractedTags, $selectedTags);

		// sort to get most populars first
		arsort($tagsCounters);

		$tags = array_keys($tagsCounters);
		// get only 10 of them
		$tags = array_slice ( $tags, 0, 10 );

		$this->extractedTagsSelection = $tags;
		return $tags;
	}

	public function extractTags($request) {
		$tags = [];

		if ($this->extractTags == false) {
			return [];
		}
		if(isset($this->extractedTags)) {
			return array_keys($this->extractedTags);
		}

		$results = $this->executeSearch($request, ['limit' => 200, 'page'=> 1], false);


		foreach ($results as $result){
			$page = WikiPage::factory( $result->getTitle() );

			if($page->getContent()) {
				$preloadContent = $page->getContent()->getWikitextForTransclusion();
			} else {
				$preloadContent = '';
			}
			// remplace template :
			$preloadContent  = str_replace('{{Tuto Details', '{{Tuto SearchResult', $preloadContent);

			$data = WfTutorialUtils::getArticleData( $preloadContent);
			if (isset($data['Tags']) && $data['Tags'] ) {
				$pageTags = explode(',',$data['Tags']);
				foreach ($pageTags as $tag) {
					$tag = trim($tag);
					if (!$tag) {
						continue;
					}
					if(isset($tags[$tag])) {
						$tags[$tag] ++;
					} else {
						$tags[$tag] = 1;
					}
				}
			}
		}
		$this->extractedTags = $tags;
		return array_keys($tags);
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
		$this->queryCount = PFUtils::getSMWStore()->getQueryResult( $queryCount );

		$queryObj = SMWQueryProcessor::createQuery( $queryString,
			$processedParams,
			SMWQueryProcessor::SPECIAL_PAGE, '', $printouts );
		$queryObj->setLimit($limit);
		$res = PFUtils::getSMWStore()->getQueryResult( $queryObj );
		$pages = $res->getResults();

		return $pages;
	}

	public function getSearchResultsHtml($param = []) {

		$out = "<div class='searchresults'>\n";

		$out .= '<a id="explore-page'.$this->page . '" name="page'.$this->page . '"></a>';


		// load More button
		if($this->page > 1 ) {
			$out .= '<div class="load-more-previous">'.wfMessage( $this->message['load-more-previous'] )->text(). '</div>';
		}

		$wikifabExploreResultFormatter = $this->getFormatter();

		$wikifabExploreResultFormatter->setResults($this->results);

		$out .= $wikifabExploreResultFormatter->render();

		// load More button
		if(count($this->results) >= $this->pageResultsLimit) {
			$out .= '<div class="load-more">'.wfMessage( $this->message['load-more'] )->text(). '</div>';
		}

		$out .= "</div>\n" ;

		return $out;
	}
}