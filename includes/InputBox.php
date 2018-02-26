<?php
namespace Explore;

use SpecialPage;
use Xml;
/**
 * Hooks for InputBox
 *
 * @file
 * @ingroup Extensions
 */

// InputBox hooks
class InputBox {

	// Render the input box
	public static function render( $parser, $buttonlabel = '', $placeholder = '') {

		static $idCounter = 1;

		if( ! $buttonlabel) {
			$buttonlabel = Xml::openElement( 'span',['class' => "glyphicon glyphicon-search"]);
		}

		$url = SpecialPage::getTitleFor( 'WfExplore' )->getLinkURL();

		// start rendering the page
		$out = Xml::openElement(
				'form',
				array(
						'id' => 'wfExpInput-' . $idCounter,
						'class' => 'wfExploreSearch',
						'method' => 'get',
						'action' => $url,
						'data-exploreId' => 'wfExpInput-' . $idCounter
				)
				);

		$out .= Xml::openElement( 'div', array( 'class' => 'wfExpInput-container' ) );
		$out .= Xml::openElement( 'input', array(
				'id' => "wf-expl-fulltext-fulltext-cloned",
				'class' => 'fulltext-search',
				'name' => "wf-expl-fulltext-fulltext",
				'placeholder' => $placeholder,
				'type' => "text",
				'value' => ''
		) );
		$out .= Xml::closeElement( 'input' ) ;

		$out .= Xml::openElement( 'div', array( 'class' => 'input-group-btn' ) );
		$out .= Xml::openElement( 'button', array(
				'class' => 'mw-searchButton btn btn-default webfonts-changed',
				'name' => "fulltext",
				'title' => wfMessage("tooltip-search-fulltext"),
				'value' => wfMessage("search"),
				'type' => "submit",
		) ) . $buttonlabel;
		$out .= Xml::closeElement( 'button');
		$out .= Xml::closeElement( 'div' ); //close input-group-btn

		$out .= Xml::closeElement( 'div' ); //close wfExpInput-container

		$out .= Xml::closeElement( 'form' )	;

		$idCounter ++;

		return array( $out, 'noparse' => true, 'isHTML' => true );
	}

}
