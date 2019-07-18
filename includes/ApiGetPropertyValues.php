<?php

class ApiGetPropertyValues extends ApiBase {

	public function __construct($query, $moduleName) {
		parent::__construct ( $query, $moduleName );
	}
	public function getAllowedParams() {
		return array (
			'offset' => array (
					ApiBase::PARAM_TYPE => 'integer',
					ApiBase::PARAM_REQUIRED => false
			),
			'limit' => array (
					ApiBase::PARAM_TYPE => 'integer',
					ApiBase::PARAM_REQUIRED => false
			),
			'query' => array ( //user input
					ApiBase::PARAM_TYPE => 'string',
					ApiBase::PARAM_REQUIRED => false
			),
			'propname' => array (
					ApiBase::PARAM_TYPE => 'string',
					ApiBase::PARAM_REQUIRED => true
			)
		);
	}
	public function getParamDescription() {
		return [ ];
	}
	public function getDescription() {
		return false;
	}
	public function execute() {

		$propname = $this->getMain()->getVal( 'propname' );
		$offset = $this->getMain()->getVal( 'offset' ) ? $this->getMain()->getVal( 'offset' ) : 0;
		$limit = $this->getMain()->getVal( 'limit' ) ? $this->getMain()->getVal( 'limit' ) : 5;
		$query = $this->getMain()->getVal( 'query' ) ? $this->getMain()->getVal( 'query' ) : '';

		$propertyValues = $this->getPropertyValues( $propname, $query, $offset, $limit );


		$this->getResult()->addValue ( null, $this->getModuleName(), $propertyValues );
	}

	public function needsToken() {
		return 'csrf';
	}

	private function getPropertyValues( $propname, $query = '', $offset = 0, $limit = 5 ) {

		$res = [];

		$property = SMWDataValueFactory::getInstance()->newPropertyValueByLabel(
			str_replace( [ '_' ], [ ' ' ], $propname )
		);

		$applicationFactory = SMW\ApplicationFactory::getInstance();

		$requestOptions = [
			'limit'    => $limit,
			'offset'   => $offset,
			'property' => $propname,
			'search'    => $query,
			'nearbySearchForType' => $applicationFactory->getSettings()->get( 'smwgSearchByPropertyFuzzy' )
		];

		$requestOptions = new SMWRequestOptions();
		$requestOptions->sort = true;
		$requestOptions->setLimit( $limit );
		$requestOptions->setOffset( $offset );

		if ($query) {
			$requestOptions->addStringCondition($query, SMWStringCondition::COND_MID);
		}

		$results = $applicationFactory->getStore()->getPropertyValues(
			null,
			$pageRequestOptions->property->getDataItem(),
			$requestOptions
		);

		foreach ( $results as $result ) {

			$dv = SMWDataValueFactory::getInstance()->newDataValueByItem( $result, $property->getDataItem() );
			$res[] = $dv->getLongHTMLText( null );
		}

		return $res;
	}
}