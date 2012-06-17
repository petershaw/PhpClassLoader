<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir. DIRECTORY_SEPARATOR .'Validation/XmlFileValidator.class.php';
require_once $equivalentSrcDir. DIRECTORY_SEPARATOR .'../File/File.class.php';

/**
 * SelfConfigurationTest
 * ============================
 * Testing the configuration 
 * 
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		%projecturl%/%articles%/PhpClassLoaderTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @ignore
 */
class SelfConfigurationTest extends PHPUnit_Framework_TestCase {

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * Tests XmlFileValidator simple xml
     */
    public function test_Valid() {
        echo "| test test_Valid\n";
        $SCConfigurationIncDir = preg_replace("/\/test\//", "/php-inc/", dirname(__FILE__));
        $XmlValidator = new XmlFileValidator(new File($SCConfigurationIncDir . "/configuration.config.xml"), new File($SCConfigurationIncDir . "/configuration.xsd"));
        $this->assertTrue($XmlValidator->getResult(), "The connfiguration of the configuration component is not valid..");
    }

}

