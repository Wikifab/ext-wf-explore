<?php

use ApiBase;

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

		$property = SMWPropertyValue::makeUserProperty( $propname );

		$options = new SMWRequestOptions();
		$options->limit = $limit;
		$options->offset = 0;
		$options->sort = true;

		if ($query) {
			$options->addStringCondition( $query, SMWStringCondition::STRCOND_MID);
		}

		$results = \SMW\StoreFactory::getStore()->getPropertyValues( null, $property->getDataItem(), $options );

		foreach ( $results as $di ) {

			$dv = \SMW\DataValueFactory::getInstance()->newDataValueByItem( $di, $property->getDataItem() );
			$res[] = $dv->getLongHTMLText( null );
		}

		return $res;
	}
}