<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'XmlSearchTest.php';

/**
 * XmlSearchTestsSuite
 * ============================
 * T
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @ignore
 */
class XmlSearchTestsSuite extends PHPUnit_Framework_TestSuite {

    /**
     * Constructs the XmlSearch test suite handler.
     */
    public function __construct() {
        $this->setName('XmlSearchTestsSuite');
        echo("--------------------------------------------------\n");
        echo("I XmlSearchTestsSuite                            I\n");
        echo("--------------------------------------------------\n");

        echo("/ run XmlSearchTest ------------------------------\n");
        $this->addTestSuite('XmlSearchTest');
        echo("\ end XmlSearchTest ------------------------------\n");
    }

    /**
     * Creates the suite.
     */
    public static function suite() {
        return new self ( );
    }

}

