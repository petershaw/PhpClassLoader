<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'SimpleLogger.class.php';

/**
 * ClassLoaderTestsSuite
 * ============================
 * This suite tests all classes inside PhpClassLoader. 
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.1.0
 * @since               1.1.0
 * 
 * @ignore
 */
class SimpleLoggerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var SimpleLogger
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testLogIntoTempFile() {
        echo "| test testLogIntoTempFile \n";
        $object = new SimpleLogger();

        $this->assertNotNull(
                $object, "Logger without a File failed initialisation."
        );

        $logFile = $object->getLogFile();

        $this->assertNotNull(
                $logFile, "Logger without a File should create a tempfile successfully."
        );

        $this->assertInstanceOf(
                'File', $logFile, "Logger without a File should return a File type temporary file."
        );

        $this->assertFileExists(
                $logFile->getDirname() . DIRECTORY_SEPARATOR . $logFile->getFilename(), "Logfile in temppath do not exist."
        );

        $object->log(SimpleLogger::INFO, "This is just a testLog");
        $this->assertGreaterThan(0, strlen(strstr($object->getLogContent(), "This is just a testLog")), "Logstring not found");
    }

}

?>
