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
		$query = $this->getMain()->getVal( 'query' ) ? $this->getMain()->getVal( 'query' ) : '';

		$propertyValues = $this->getPropertyValues( $propname, $query, $offset );


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
		
		$requestOptions = new SMWRequestOptions();
		$requestOptions->sort = true;
		$requestOptions->setOffset( $offset );

		$results = \SMW\StoreFactory::getStore()->getPropertyValues( null, $property->getDataItem(), $requestOptions );

		foreach ( $results as $result ) {

			$dv = SMWDataValueFactory::getInstance()->newDataValueByItem( $result, $property->getDataItem() );
			$propertyValue = $dv->getLongHTMLText( null );
			if($propname === 'Page creator'){
				$propertyValue = explode(':', $propertyValue)[1];
			}
			if(preg_match('/.*'.$query.'.*/i', $propertyValue)) {
				$res[] = $propertyValue;
			}
		}

		return $res;
	}
}