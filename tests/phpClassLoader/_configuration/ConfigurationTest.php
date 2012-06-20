<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir. DIRECTORY_SEPARATOR .'PCLConfiguration.class.php';

/**
 * ConfigurationTest
 * ============================
 * Testing the configuration 
 * 
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.1.0
 * @since               1.0.0
 * 
 * @ignore
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Configuration
     */
    private $Configuration;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $this->Configuration = new PCLConfiguration();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        $this->Configuration = null;
        parent::tearDown();
    }

    public function testGetConfigurationFilename() {
        echo "| test testGetConfigurationFilename\n";
        $expected = dirname(__FILE__) . "/" . strtolower(__CLASS__) . ".config.xml";
        $result = $this->Configuration->getConfigurationFilename();
        $this->assertSame($expected, $result, "Wrong configurationfilename is returned.");
    }

    public function testGetOtherConfigurationFilename() {
        echo "| test testGetOtherConfigurationFilename\n";
        $cnf = new PCLConfiguration("test", dirname(__FILE__) . "/resources");
        $expected = dirname(__FILE__) . "/resources/test.config.xml";
        $result = $cnf->getConfigurationFilename();
        $this->assertSame($expected, $result, "Wrong configurationfilename is returned.");
    }

    public function testIsConfiguration() {
        echo "| test testIsConfiguration\n";
        $this->assertTrue($this->Configuration->isConfiguration(), "Component configuration file is not available.");
    }

    public function testIsOtherConfiguration() {
        echo "| test testIsOtherConfiguration\n";
        $cnf = new PCLConfiguration("test", dirname(__FILE__) . "/resources");
        $this->assertTrue($cnf->isConfiguration(), "Userspace configuration file is not available.");
    }

    /**
     * Tests Configuration->getSetupParameter()
     */
    public function testGetSetupParameter() {
        echo "| test testGetSetupParameter\n";
        $this->assertEquals($this->Configuration->getSetupParameter('testdefine'), "foo", "Variable testdefine is not foo.");
        $this->assertEquals($this->Configuration->getSetupParameter('secondtestdefine'), "bar", "Variable secondtestdefine is not foo.");
        $this->assertEquals($this->Configuration->getSetupParameter('thirdtestdefine'), "foo bar test", "Variable thirdtestdefine is not foo bar test.");
    }

    public function testGetNullSetupParameter() {
        echo "| test testGetNullSetupParameter\n";
        $this->assertNull($this->Configuration->getSetupParameter('null'), "Variable null is not a define.");
    }

    /**
     * Tests Configuration->getSetupParameterKeys()
     */
    public function testGetSetupParameterKeys() {
        echo "| test testGetSetupParameterKeys\n";
        $result = $this->Configuration->getSetupParameterKeys();
        $this->assertTrue(is_array($result), "a array is expected.");
        $this->assertTrue(in_array("testdefine", $result), "testdefine is not found.");
    }

    /**
     * Tests Configuration->getSetupParameters()
     */
    public function testGetSetupParameters() {
        echo "| test testGetSetupParameters\n";
        $result = $this->Configuration->getSetupParameters();
        $this->assertTrue(is_array($result), "a array is expected.");
        $this->assertEquals($result['testdefine'], "foo", "wrong value.");
        $this->assertEquals($result['thirdtestdefine'], "foo bar test", "wrong value.");
    }
    
    /**
     * Tests Configuration->getSetupParameters()
     */
    public function testGetArrayParameters() {
        echo "| test testGetArrayParameters";
        $result = $this->Configuration->getSetupParameter('anarrayentry');
        $this->assertTrue(is_array($result), "a array is expected.");

        $this->assertContains(1, $result, "The Setuppparamenter did not return all integers from a array.");
        $this->assertContains(2, $result, "The Setuppparamenter did not return all integers from a array.");
        $this->assertContains(3, $result, "The Setuppparamenter did not return all integers from a array.");
        $this->assertNotContains(4, $result, "The Setuppparamenter return a array element that is not set.");
    }

}

