<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir. DIRECTORY_SEPARATOR .'Validator.interface.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'XmlFileValidatorTest.php';
require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'XmlStringValidatorTest.php';

/**
 * ValidationTestsSuite
 * ============================
 * Validator testsuite
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		colleagues of @peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @ignore
 */
class ValidationTestsSuite extends PHPUnit_Framework_TestSuite {

    /**
     * Constructs the Validator test suite handler.
     */
    public function __construct() {
        $this->setName('ValidationTestsSuite');
        echo("--------------------------------------------------\n");
        echo("I ValidationTestsSuite                           I\n");
        echo("--------------------------------------------------\n");

        echo("/ run XmlFileValidatorTest -----------------------\n");
        $this->addTestSuite('XmlFileValidatorTest');
        echo("\ end XmlFileValidatorTest -----------------------\n");

        echo("/ run XmlStringValidatorTest ---------------------\n");
        $this->addTestSuite('XmlStringValidatorTest');
        echo("\ end XmlStringValidatorTest ---------------------\n");
    }

    /**
     * Creates the suite.
     */
    public static function suite() {
        return new self ( );
    }

}

