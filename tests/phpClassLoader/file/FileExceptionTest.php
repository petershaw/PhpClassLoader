<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'FileException.class.php';

/**
 * FileExceptionTest
 * ============================
 * Testing the File-Object 
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
class FileExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var FileException
     */
    private $FileException;

    /**
     * This is a mesage that will be used to test the Exception
     *
     * @var String
     */
    private $testmsg = "Test";

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $this->FileException = new FileException(new File(__FILE__), $this->testmsg);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        $this->FileException = null;
        parent::tearDown();
    }

    /**
     * Tests FileException->getFile()
     */
    public function testGetFile() {
        echo "| test testGetFile \n";
        $result = $this->FileException->getFile();
        $this->assertSame(__FILE__, $result, "getFile do not return the right String.");
    }

    /**
     * Tests FileException->getFile()
     */
    public function testGetMessage() {
        echo "| test testGetMessage \n";
        $result = $this->FileException->getMessage();
        $this->assertSame("Test", $result, "getMessage do not return the right String.");
    }

}

