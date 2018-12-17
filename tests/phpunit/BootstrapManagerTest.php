<?php

namespace Explore\Tests;

use WfTutorialUtils;

/**
 * @uses \Bootstrap\BootstrapManager
 *
 * @ingroup Test
 *
 * @group extension-bootstrap
 * @group mediawiki-databaseless
 *
 * @license GNU GPL v3+
 * @since 1.0
 *
 * @author mwjames
 */
class WfTutorialUtilsTest extends \PHPUnit_Framework_TestCase {

	protected $wgResourceModules = null;

	protected  $instance = null;

	protected function setUp() {
		parent::setUp();
		$this->wgResourceModules = $GLOBALS['wgResourceModules'];

		// Preset with empty default values to verify the initialization status
		// during invocation
		$GLOBALS['wgResourceModules'][ 'ext.bootstrap.styles' ] = array(
			'localBasePath'   => '',
			'remoteBasePath'  => '',
			'class'           => '',
			'dependencies'    => array(),
			'styles'          => array(),
			'variables'       => array(),
			'external styles' => array()
		);

		$GLOBALS['wgResourceModules'][ 'ext.bootstrap.scripts' ] = array(
			'dependencies'    => array(),
			'scripts'         => array()
		);

		$this->instance = new WfTutorialUtils();
	}

	protected function tearDown() {
		$GLOBALS['wgResourceModules'] = $this->wgResourceModules;
		parent::tearDown();
	}


	public function testJsonData( ) {

		$jsondata = "{\"version\":\"2.4.1\",\"objects\":[{\"type\":\"image\",\"version\":\"2.4.1\",\"originX\":\"left\",\"originY\":\"top\",\"left\":-342.65,\"top\":-146.37,\"width\":800,\"height\":450,\"fill\":\"rgb(0,0,0)\",\"stroke\":null,\"strokeWidth\":0,\"strokeDashArray\":null,\"strokeLineCap\":\"butt\",\"strokeLineJoin\":\"miter\",\"strokeMiterLimit\":4,\"scaleX\":2.49,\"scaleY\":2.49,\"angle\":0,\"flipX\":false,\"flipY\":false,\"opacity\":1,\"shadow\":null,\"visible\":true,\"clipTo\":null,\"backgroundColor\":\"\",\"fillRule\":\"nonzero\",\"paintFirst\":\"fill\",\"globalCompositeOperation\":\"source-over\",\"transformMatrix\":null,\"skewX\":0,\"skewY\":0,\"crossOrigin\":\"\",\"cropX\":0,\"cropY\":0,\"src\":\"http://demo-dokit.localtest.me/w/images/thumb/f/f7/Tuto_test_images_LB_Step_44.jpg/800px-Tuto_test_images_LB_Step_44.jpg\",\"filters\":[]},{\"type\":\"wfcircle\",\"version\":\"2.4.1\",\"originX\":\"center\",\"originY\":\"center\",\"left\":367.29,\"top\":253.29,\"width\":200,\"height\":200,\"fill\":\"rgba(255,0,0,0)\",\"stroke\":\"red\",\"strokeWidth\":2.36,\"strokeDashArray\":null,\"strokeLineCap\":\"butt\",\"strokeLineJoin\":\"miter\",\"strokeMiterLimit\":4,\"scaleX\":1.27,\"scaleY\":1.27,\"angle\":0,\"flipX\":false,\"flipY\":false,\"opacity\":1,\"shadow\":null,\"visible\":true,\"clipTo\":null,\"backgroundColor\":\"\",\"fillRule\":\"nonzero\",\"paintFirst\":\"fill\",\"globalCompositeOperation\":\"source-over\",\"transformMatrix\":null,\"skewX\":0,\"skewY\":0,\"radius\":100,\"startAngle\":0,\"endAngle\":6.283185307179586}],\"height\":450,\"width\":600}";

		$content = "{{ {{tntn|Tuto Details}}
|Type=Technique
|Main_Picutre=toto.jpg
|Main_Picutre_annotation=$jsondata
|Area=Clothing and Accessories
|Tags=test,
|Description=test de tuto en francais
|Difficulty=Very easy
|Cost=2
|Currency=EUR (€)
|Duration=3
|Duration-type=minute(s)
|Licences=Attribution (CC BY)
}}
{{ {{tntn|Introduction}}}}
{{ {{tntn|Materials}}}}
{{ {{tntn|Separator}}}}
{{ {{tntn|Tuto Step}}
|Step_Title=debrouille toi vite
}}
{{ {{tntn|Notes}}}}
{{ {{tntn|Tuto Status}}}}";
		$result = $this->instance->getArticleData($content);

		$this->assertEquals($result['Main_Picutre_annotation'], $jsondata);
	}

}
