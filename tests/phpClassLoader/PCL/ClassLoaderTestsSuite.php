<?php

// include all tests for the classloader
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SimpleLoggerTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ClassLoaderFlatfileTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ClassLoaderSQLiteTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ClassLoaderTest.php';

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
 * @since               1.0.0
 * 
 * @ignore
 */
class ClassLoaderTestsSuite extends PHPUnit_Framework_TestSuite {

    /**
     * Constructs the ClassLoader test suite handler.
     */
    public function __construct() {
        $this->setName('ClassLoaderTestsSuite');
        echo("--------------------------------------------------\n");
        echo("I ClassLoaderTestsSuite                          I\n");
        echo("--------------------------------------------------\n");

        echo("/ run SimpleLoggerTest ---------------------------\n");
        $this->addTestSuite('SimpleLoggerTest');
        echo("\ end SimpleLoggerTest ---------------------------\n");

        echo("/ run ClassLoaderFlatfileTest --------------------\n");
        $this->addTestSuite('ClassLoaderFlatfileTest');
        echo("\ end ClassLoaderFlatfileTest --------------------\n");

        echo("/ run ClassLoaderSQLLiteTest ---------------------\n");
        $this->addTestSuite('ClassLoaderSQLiteTest');
        echo("\ end ClassLoaderSQLLiteTest ---------------------\n");

        echo("/ run ClassLoaderTest ----------------------------\n");
        $this->addTestSuite('ClassLoaderTest');
        echo("\ end ClassLoaderTest ----------------------------\n");
    }

    /**
     * Creates the ClassLoader suite.
     */
    public static function suite() {
        return new self ( );
    }

}

