<?php

class WikifabExploreResultFormatter {


	private $out;

	private $template;

	private function getTemplate() {
		if( ! $this->template) {
			$this->template = $GLOBALS['egChameleonLayoutFileSearchResult'];
		}
		return $this->template;
	}

	public function setTemplate($template) {
		$this->template = $template;
	}

	public function setResults(&$results) {
		$this->results = $results;
	}

	public function render() {
		$this->out = '';

		if (count($this->results) > 0) {
			$this->openResultsContainer();
			$this->out .= $this->showMatches( $this->results );
			$this->closeResultsContainer();
		}


		if ( count($this->results) === 0 ) {
			$this->displayNoResultMessage();
		}
		return $this->out;
	}

	private function displayNoResultStatusMessage($textStatus) {
		$this->out .= '<div class="error">' . $textStatus->getMessage( 'search-error' ) . '</div>' ;
	}

	private function displayNoResultMessage( ) {
		$this->out .=  '<div class="container">';
		$this->out .=  "<p class=\"mw-search-nonefound\">\n ".wfMessage( 'wfexplore-noResultMessage'  )->parse() ."</p>";
		$this->out .=  '</div>';
	}

	private function openResultsContainer() {
		//$this->out .=  "\n <!-- Begin of Results container-->\n";
		$this->out .=  '<div class="container"><div class="row">';
	}
	private function closeResultsContainer() {
		$this->out .=  '</div></div>';
		//$this->out .=  "\n <!-- End of Results container-->\n";
	}

	/**
	 * Show whole set of results
	 *
	 * @param SearchResultSet $matches
	 *
	 * @return string
	 */
	protected function showMatches( &$results ) {
		global $wgContLang;

		$out = "";
		foreach ($results as $key => $result) {
			$out .= $this->showHit( $result );
		}

		// convert the whole thing to desired language variant
		$out = $wgContLang->convert( $out );

		return $out;
	}

	/**
	 * Format a single hit result
	 *
	 * @param SearchResult $result
	 *
	 * @return string
	 */
	public function showHit( $result ) {


		$title = $result->getTitle();

		return $this->getPageDetails($result);

	}


	/**
	 * retreived all page content
	 *
	 */
	public function getPageDetails($result) {
		global $sfgFormPrinter;

		$mTitle = $result->getTitle();

		$page = WikiPage::factory( $mTitle );

		if($page->getContent()) {
			$preloadContent = $page->getContent()->getWikitextForTransclusion();
		} else {
			$preloadContent = '';
		}
		$text = $page->getText();
		$creator = $page->getCreator();


		// remplace template :
		$preloadContent  = str_replace('{{Tuto Details', '{{Tuto SearchResult', $preloadContent);


		// get the form content
		$formTitle = Title::makeTitleSafe( SF_NS_FORM, 'Template:Tuto_Details' );

		$data = WfTutorialUtils::getArticleData( $preloadContent);

		if( ! $data ) {
			return '';
		}

		$data['title'] =$mTitle->getText();
		$data['creatorId'] = $creator->getId();
		$data['creatorUrl'] = $creator->getUserPage()->getLinkURL();
		$data['creatorName'] = $creator->getName();

		$avatar = new wAvatar( $data['creatorId'], 'm' );
		$data['creatorAvatar'] = $avatar->getAvatarURL();

		$data['creator'] = $creator->getRealName();
		if( ! $data['creator']) {
			$data['creator'] = $creator->getName();
		}
		$data['url'] = $mTitle->getLinkURL();

		return $this->formatResult($data);

	}

	public function formatResult($content) {
		$wgScriptPath = $GLOBALS['wgScriptPath'];
		$out = file_get_contents($this->getTemplate());
		$content['ROOT_URL'] = $wgScriptPath . '/';

		$defaultFields = $GLOBALS['wgExploreDefaultsFieldsDisplayValues'];

		$content2 = array_merge($defaultFields, $content);

		foreach ($content2 as $key => $value) {

			$imageKeywords = array(
					'picture',
					'image',
					'img',
					'logo'
			);
			foreach ($imageKeywords as $imageKeyword) {
				if (strpos(strtolower($key), $imageKeyword)) {
					$file = wfFindFile( $value );
					if($file) {
						$fileUrl = $file->getUrl();
						if ( isset($GLOBALS['wgExploreUseThumbs']) &&  $GLOBALS['wgExploreUseThumbs']) {
							// if possible, we use thumbnail
							$params = ['width' => 400];

							$mto = $file->transform( $params );
							if ( $mto && !$mto->isError() ) {
								// thumb Ok, change the URL to point to a thumbnail.
								$fileUrl = wfExpandUrl( $mto->getUrl(), PROTO_RELATIVE );
							}
						}
						$out = str_replace("{{" . $key . "::url}}", $fileUrl, $out);
					}
				}
			}
			$out = str_replace("{{" . $key . "}}", $value, $out);
		}
		return $out;
	}

}