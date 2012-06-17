<?php

// include all tests for the configuration
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SelfConfigurationTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigurationTest.php';

/**
 * ConfigurationTestsSuite
 * ============================
 * This suite tests the Configuration facility. 
 * 
 * 
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
class ConfigurationTestsSuite extends PHPUnit_Framework_TestSuite {

    /**
     * Constructs the test suite handler.
     */
    public function __construct() {
        $this->setName('ConfigurationTestsSuite');
        echo("--------------------------------------------------\n");
        echo("I ConfigurationTestsSuite                        I\n");
        echo("--------------------------------------------------\n");

        echo("/ run SelfConfigurationTest ----------------------\n");
        $this->addTestSuite('SelfConfigurationTest');
        echo("\ end SelfConfigurationTest ----------------------\n");

        echo("/ run ConfigurationTest --------------------------\n");
        $this->addTestSuite('ConfigurationTest');
        echo("\ end ConfigurationTest --------------------------\n");
    }

    /**
     * Creates the suite.
     */
    public static function suite() {
        return new self ( );
    }

}

