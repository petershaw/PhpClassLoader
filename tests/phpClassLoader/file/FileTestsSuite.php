<?php

// include all needed File Classes, because there is no running classloader.
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'FileTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IncFileTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'FileExceptionTest.php';

/**
 * FileTestsSuite
 * ============================
 * This suite tests the File-Object. 
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
class FileTestsSuite extends PHPUnit_Framework_TestSuite {

    /**
     * Constructs the File test suite handler.
     */
    public function __construct() {
        $this->setName('FileTestsSuite');
        echo("--------------------------------------------------\n");
        echo("I FileTestsSuite                                 I\n");
        echo("--------------------------------------------------\n");

        echo("/ run FileTest -----------------------------------\n");
        $this->addTestSuite('FileTest');
        echo("\ end FileTest -----------------------------------\n");

        echo("/ run IncFileTest --------------------------------\n");
        $this->addTestSuite('IncFileTest');
        echo("\ end IncFileTest --------------------------------\n");

        echo("/ run FileExceptionTest --------------------------\n");
        $this->addTestSuite('FileExceptionTest');
        echo("\ end FileExceptionTest --------------------------\n");
    }

    /**
     * Creates the suite.
     */
    public static function suite() {
        return new self ( );
    }

}

