<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir. DIRECTORY_SEPARATOR .'Configuration.class.php';

/**
 * ConfigurationTest
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
        $this->Configuration = new Configuration();
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
        $cnf = new Configuration("test", dirname(__FILE__) . "/resources");
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
        $cnf = new Configuration("test", dirname(__FILE__) . "/resources");
        $this->assertTrue($cnf->isConfiguration(), "Userspace configuration file is not available.");
    }

    /**
     * Tests Configuration->getLoggers()
     */
    public function testGetLoggers() {
        echo "| test testGetLoggers\n";
        $loggers = $this->Configuration->getLoggers();
        $this->assertTrue(is_array($loggers), "getLoggers has to be return a array.");
        $this->assertTrue(count($loggers) == 1, "The test configuration has only one logger.");
        $this->assertTrue(is_array($loggers[0]), "The logger element has to be a array.");
        $this->assertTrue(isset($loggers[0]['handle']), "The logger has no handle.");
        $this->assertTrue(isset($loggers[0]['mode']), "The logger has no mode.");
        $this->assertSame($loggers[0]['handle'], "Logger::default", "The loggerhandle has to be 'Logger::default'.");
        $this->assertSame($loggers[0]['mode'], "info", "The loggermode has to be 'info'.");
    }

    /**
     * Tests Configuration->getModeFilterdLoggers()
     */
    public function testGetModeFilterdLoggers() {
        echo "| test testGetModeFilterdLoggers\n";
        $loggers = $this->Configuration->getModeFilterdLoggers('info');
        $this->assertTrue(is_array($loggers), "getLoggers has to be return a array.");
        $this->assertTrue(count($loggers) == 1, "The test configuration has only one logger.");
        $this->assertTrue(is_array($loggers[0]), "The logger element has to be a array.");
        $this->assertTrue(isset($loggers[0]['handle']), "The logger has no handle.");
        $this->assertTrue(isset($loggers[0]['mode']), "The logger has no mode.");
        $this->assertSame($loggers[0]['handle'], "Logger::default", "The loggerhandle has to be 'Logger::default'.");
        $this->assertSame($loggers[0]['mode'], "info", "The loggermode has to be 'info'.");
    }

    public function testGetModeHigherFilterdLoggers() {
        echo "| test testGetModeHigherFilterdLoggers\n";
        $loggers = $this->Configuration->getModeFilterdLoggers('fatal');
        $this->assertTrue(is_array($loggers), "getLoggers has to be return a array.");
        $this->assertTrue(count($loggers) == 1, "The test configuration has no fatal logger, but a info logger, that must match.");
        $this->assertTrue(is_array($loggers[0]), "The logger element has to be a array.");
        $this->assertTrue(isset($loggers[0]['handle']), "The logger has no handle.");
        $this->assertTrue(isset($loggers[0]['mode']), "The logger has no mode.");
        $this->assertSame($loggers[0]['handle'], "Logger::default", "The loggerhandle has to be 'Logger::default'.");
        $this->assertSame($loggers[0]['mode'], "info", "The loggermode has to be 'info'.");
    }

    public function testGetModeLowerFilterdLoggers() {
        echo "| test testGetModeLowerFilterdLoggers\n";
        $loggers = $this->Configuration->getModeFilterdLoggers('debug');
        $this->assertTrue(is_array($loggers), "getLoggers has to be return a array.");
        $this->assertTrue(count($loggers) == 0, "The test configuration has no debug logger.");
    }

    /**
     * Tests Configuration->getSetupParameter()
     */
    public function testGetSetupParameter() {
        echo "| test testGetSetupParameter\n";
        $this->assertSame($this->Configuration->getSetupParameter('testdefine'), "foo", "Variable testdefine is not foo.");
        $this->assertSame($this->Configuration->getSetupParameter('secondtestdefine'), "bar", "Variable secondtestdefine is not foo.");
        $this->assertSame($this->Configuration->getSetupParameter('thirdtestdefine'), "foo bar test", "Variable thirdtestdefine is not foo bar test.");
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
        $this->assertSame($result['testdefine'], "foo", "wrong value.");
        $this->assertSame($result['thirdtestdefine'], "foo bar test", "wrong value.");
    }

    /**
     * testmethod for the esb-test.
     *
     * @param string $a
     */
    public function esbTest($a) {
        return array('ret' => $a);
    }

}

