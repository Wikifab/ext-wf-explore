<?php

class WfExploreCore {

	private $request;
	private $params;

	private $instanceId;

	private $pageResultsLimit = 8;

	private $namespaces = false;

	private $formatter = null;
	private $searchPageTitle = null;
	private $filters = null;

	private $isLocalised = false;
	private $extractTags = null;
	private $extractedTags = null;

	private $message = array(
			'load-more' => 'wfexplore-load-more-tutorials',
			'load-more-previous' => 'wfexplore-load-more-tutorials-previous'
	);

	private $specialsFields = array(
			'Complete' => array('query' => '[[Complete::!none]]'),
			'complete' => array('query' => '[[Complete::!none]]')
	);

	public function __construct() {
		static $instanceCount = 0;
		$instanceCount ++;

		$this->instanceId = 'wfe' . $instanceCount;

		if (isset($GLOBALS['wfexploreExtractTags'])) {
			$this->extractTags = $GLOBALS['wfexploreExtractTags'];
		}
		if (isset($GLOBALS['wgExploreIsLocalized'])) {
			$this->isLocalised = $GLOBALS['wgExploreIsLocalized'];
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

		} else if ($category == 'Category') {
			if($andCondition) {
				$result = '';
				foreach ($valuesIds as $valueId) {
					$result .= '[[' . $category . ':' .  $valueId . ']]';
				}
				return $result;
			} else {
				return '[[' . $category . ':' . implode('||', $valuesIds) . ']]';
			}
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

	private function getValuesForProperty($property, $translateKeyPrefix = null) {
		$store = PFUtils::getSMWStore();
		$values = PFValuesUtils::getSMWPropertyValues( $store, Title::newFromDBkey('Property:'.$property), "Allows value" );

		$result = [];
		foreach ($values as $value) {
			if($translateKeyPrefix) {
				$result[$value] = wfMessage($translateKeyPrefix . str_replace(' ','_', $value));
			}
		}
		return $result;
	}

	/**
	 * deprecated : for compatibility with previous versions
	 */
	private function getStaticFilters () {

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
				'Cost' => $fourchetteCout
		);
	}

	private function getFilters() {

		if ($this->filters !== null) {
			return $this->filters;
		}

		if (isset($GLOBALS['wfexploreCategories'])) {
			return $GLOBALS['wfexploreCategories'];
		}

		// for compatibility with old versions
		if ( ! $this->isLocalised ) {
			return $this->getStaticFilters();
		}

		$type = $this->getValuesForProperty('Type', "wf-propertyvalue-type-");
		$categories = $this->getValuesForProperty('Area', "wf-propertyvalue-area-");
		$diff = $this->getValuesForProperty('Difficulty', "wf-propertyvalue-difficulty-");

		$fourchetteCout = array(
			'0-10' => '0 - 10',
			'10-50' => '10 - 50',
			'50-100' => '50 - 100',
			'100-inf' => '100 - ∞'
		);
		$lang = array(
			'ALL' => wfMessage("wfexplore-language-all")
		);
		return array (
			'Type' => $type,
			'area' => $categories,
			'Difficulty' => $diff,
			'Cost' => $fourchetteCout,
			'Language' => $lang
		);
	}

	private function getFiltersData() {

		$categoriesNames = array(
			'Type' => wfMessage( 'wfexplore-type' )->text() ,
			'area' =>  wfMessage( 'wfexplore-category' )->text(),
			'Difficulty' => wfMessage( 'wfexplore-difficulty' )->text() ,
			'Cost' => wfMessage( 'wfexplore-cost' )->text() ,
			'Complete' => 'Complete',
			'Language' => wfMessage( 'wfexplore-language' )->text(),
		);

		if (isset($GLOBALS['wfexploreCategoriesNames'])) {
			$categoriesNames = $GLOBALS['wfexploreCategoriesNames'];
		}


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
				if($value) {
					$results[$field] = array(
							'value' => $value,
							'type' => 'text'
					);
				}
			}
		}

		if ($this->isLocalised && ! isset($results['Language'])) {
			// default : use user language :
			global $wgLang;
			$lang = $wgLang->getCode();

			// if no language set, (not a spécial one, nor 'ALL') we set current language
			// use current language
			$results['Language'] = array(
				$lang => array(
					'category' => $category,
					'valueName' => $lang,
					'valueId' => $lang
				)
			);
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
					'id' => 'wfExplore-' . $this->instanceId,
					'class' => 'wfExplore',
					'method' => 'get',
					'action' => $url,
					'data-exploreId' => $this->instanceId
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
		global $wgLang;
		$currentLanguage = $wgLang->getCode();

		// get form options :
		$filtersData = $this->getFiltersData();
		// get selected Options
		$selectedOptions = $this->getSelectedAdvancedSearchOptions($request, $params);

		$params = $this->params;

		// those two vars could be parametized in Localsettings
		// for now, this is hard-coded
		$wgExploreCategoriesUsingSwitchButtons = [
				'Language' => "Language"
		];
		$wgExploreSwitchButtons = [
				'Language-ALL' => 'wfexplore-display-all-language'
		];

		$tags = $this->getTags();

		$exploreId = $this->instanceId;

		ob_start();
		include ($GLOBALS['egWfExploreLayoutForm']);
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	private function getQueryParamsWithType($category, $values) {

		if($category == 'Language') {
			return '';
		}

		$type = isset($values['type']) ? $values['type'] : 'checkbox';
		$andCondition = false;

		switch ($type) {
			case 'text' :
				$valuesIds = explode(',',$values['value']);
				foreach ($valuesIds as $key => $val) {
					$valuesIds[$key] = "~*" . $val . "*";
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

		if (isset($params['query'])) {
			$query = $params['query'];
		}
		foreach ($this->specialsFields as $key => $specialField) {
			if (isset($selectedOptions[$key])) {
				unset($selectedOptions[$key]);
				$query .= $specialField['query'];
			}
		}
		// language conditions :
		$lang = null;
		if ( isset($selectedOptions['Language'])) {
			foreach ($selectedOptions['Language'] as $value) {
				$lang = $value['valueId'];
				if ($lang == 'ALL') {
					$lang = null;
				}
			}
		}

		foreach ($selectedOptions as $category => $values) {
			if ($category != 'Language') {
				$query .= ' ' . $this->getQueryParamsWithType($category, $values);
			}
			//$query .= ' [[' . $category . '::' . implode('||', $valuesIds) . ']]';
		}

		if ($this->namespaces) {
			$query .= '[[' . implode(':+||',$this->namespaces) . ':+]]';
			//$query .= '[[group-type::*]]';
			//$query .= '[[Group:*]]';
		}

		if ($lang) {
			$query = "$query [[Language::none]] OR $query [[Language::$lang]][[isTranslation::0]] OR $query [[Language::$lang]][[isTranslation::1]][[SourceLanguage::!$lang]]";
		}


		if( ! trim($query) ) {

			if (isset($GLOBALS['wfexploreDefaultQuery'])) {
				$query = $GLOBALS['wfexploreDefaultQuery'];
			} else {
				$query = '[[area::!none]]';
			}
		}

		//var_dump($query);
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

	public function extractTags($request, $params) {
		$tags = [];

		if ($this->extractTags == false) {
			return [];
		}
		if(isset($this->extractedTags)) {
			return array_keys($this->extractedTags);
		}

		$params = array_merge($params, ['limit' => 200, 'page'=> 1]);

		$results = $this->executeSearch($request, $params, false);


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

	/**
	 * get result in HTML
	 *
	 * available params :
	 * - noLoadMoreButton : if set to true, do not display the 'load more' button
	 * - replaceClass : set a classname on container div (default : 'searchresults')
	 * - isEmbed : by default, set to display full width, if set to true, do not insert div class 'container'
	 *
	 * @param array $param
	 * @return string
	 */
	public function getSearchResultsHtml($param = []) {

		$defaultParams = [
				'noLoadMoreButton' => false,
				'replaceClass' => 'searchresults',
				'isEmbed' => false
		];
		if( ! isset($this->page)) {
			$this->page = 1;
		}

		$param = array_merge($defaultParams, $param);
		$out = "<div class='".$param['replaceClass']."'  id='result-" . $this->instanceId . "'>\n";

		$out .= '<a id="explore-page'.$this->page . '" name="page'.$this->page . '"></a>';


		// load More button
		if($this->page > 1 ) {
			$out .= '<div class="load-more-previous">'.wfMessage( $this->message['load-more-previous'] )->text(). '</div>';
		}

		$wikifabExploreResultFormatter = $this->getFormatter();

		if(isset($param['layout'])) {
			$wikifabExploreResultFormatter->setLayout($param['layout']);
		}

		$wikifabExploreResultFormatter->setResults($this->results);

		$renderParams = [];
		if( $param['isEmbed']) {
			$renderParams['isEmbed'] = true;
		}
		$out .= $wikifabExploreResultFormatter->render($renderParams);

		// load More button
		if(count($this->results) >= $this->pageResultsLimit && ! $param['noLoadMoreButton']) {
			$out .= '<div class="load-more">'.wfMessage( $this->message['load-more'] )->text(). '</div>';
		}

		$out .= "</div>\n" ;

		return $out;
	}
}