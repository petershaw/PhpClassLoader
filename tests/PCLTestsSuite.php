<?php

/**
 * PCLTestsSuite
 * ===================
 * This is the main test suite for the whole PHPCarpet Project. It's calling 
 * all suites inside the Utilites, Root, Environment, Components and 
 * Applications folder. 
 * 
 * 
 * @example             phpunit PCLTestsSuite.php
 *
 * @package		unittests
 * @subpackage          main
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw
 *
 * @version		1.1.0
 * @since               1.0.0
 * 
 * @ignore
 */

echo("\n");
echo("--------------------------------------------------\n");
echo("I Welcome to the PhpClassLoader testsuite        I\n");
echo("--------------------------------------------------\n");
echo("I This suites will test all classes inside this  I\n");
echo("I package.                                       I\n");
echo("I                                                I\n");
echo("I PCL - A PhpClassLoader                         I\n");
echo("I File                                           I\n");
echo("I XmlSearch                                      I\n");
echo("I Validation                                     I\n");
echo("I Configuration                                  I\n");
echo("--------------------------------------------------\n");
echo("You are running: ". PHP_MAJOR_VERSION .".". PHP_MINOR_VERSION ."\n");
echo("PHP version: ". PHP_VERSION ." (". PHP_VERSION_ID .")\n");
echo("On OS: ". PHP_OS ."\n");
echo("--------------------------------------------------\n");

require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'phpClassLoader/PhpVersionTest.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'phpClassLoader/xml/xmlSearch/XmlSearchTestsSuite.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'phpClassLoader/file/FileTestsSuite.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'phpClassLoader/configuration/validation/ValidationTestsSuite.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'phpClassLoader/configuration/ConfigurationTestsSuite.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'phpClassLoader/PCL/ClassLoaderTestsSuite.php';

/**
 * Main test suite.
 * @ignore
 */
class PCLTestsSuite extends PHPUnit_Framework_TestSuite {

    /*
     * Constructs the test suite handler.
     * Runs the whole testbundle from the bottom layer to the top.
     */
    public function __construct() {
        $this->setName('PCLTestsSuite');
        echo("\n");
        echo("--------------------------------------------------\n");
        echo("I PCLTestsSuite                                  I\n");
        echo("--------------------------------------------------\n");

        echo "Testing Version\n";
        echo("--------------------------------------------------\n");
        $this->addTestSuite('PhpVersionTest');
        echo("--------------------------------------------------\n");
        
        // 
        echo("/ run FileTestsSuite -----------------------------\n");
        $this->addTestSuite('FileTestsSuite');
        echo("\ end FileTestsSuite -----------------------------\n");        
        
        // 
        echo("/ run XmlSearchTestsSuite ------------------------\n");
        $this->addTestSuite('XmlSearchTestsSuite');
        echo("\ end XmlSearchTestsSuite ------------------------\n");
        
        // 
        echo("/ run ValidationTestsSuite -----------------------\n");
        $this->addTestSuite('ValidationTestsSuite');
        echo("\ end ValidationTestsSuite -----------------------\n");
        
        // 
        echo("/ run ConfigurationTestsSuite --------------------\n");
        $this->addTestSuite('ConfigurationTestsSuite');
        echo("\ end ConfigurationTestsSuite --------------------\n");

        // 
        echo("/ run ClassLoaderTestsSuite ----------------------\n");
        $this->addTestSuite('ClassLoaderTestsSuite');
        echo("\ end ClassLoaderTestsSuite ----------------------\n");
        
        echo("--------------------------------------------------\n");
        echo("I ALL TEST INITALISATION IS DONE                 I\n");
        echo("==================================================\n");
    }

    /**
     * Creates the main test suite.
     */
    public static function suite() {
        return new self();
    }

}

