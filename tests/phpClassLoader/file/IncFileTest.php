<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'IncFile.class.php';

/**
 * IncFileTest
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
class IncFileTest extends PHPUnit_Framework_TestCase {

    /**
     * @var IncFile
     */
    private $IncFile;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        /**
         * The only file we know exactly on this point is the PHPUnit class
         * file. It has to be inside the php_inc folders, somewhre. Otherwise
         * this test will not run anywhere.
         */
        $this->IncFile = new IncFile("/PHPUnit/Framework/TestSuite.php");
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        $this->IncFile = null;
        parent::tearDown();
    }

    public function testIsFile() {
        echo "| test testIsFile \n";
        $this->assertTrue(is_file($this->IncFile->toString()), "The lookupfile is not a file.");
        $this->assertTrue(file_exists($this->IncFile->toString()), "The lookupfile does not exist.");
    }

}

