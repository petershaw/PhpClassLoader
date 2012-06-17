<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . '../../file/File.class.php';
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'XmlSearch.class.php';

/**
 * XmlSearchTest
 * ============================
 * 
 *
 * @package		unittests
 * @subpackage          PhpClassLoader
 *
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLTestsSuite
 * @author		@peter_shaw and colleagues
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @ignore
 */
class XmlSearchTest extends PHPUnit_Framework_TestCase {

    /**
     * @var XmlSearch
     */
    private $XmlSearchFile;
    private $XmlSearchString;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $file = new File(dirname(__FILE__) . "/resources/test01.xml");
        $this->XmlSearchFile = new XmlSearch($file);
        $this->XmlSearchString = new XmlSearch($file->getContent());
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        $this->XmlSearchFile = null;
        $this->XmlSearchString = null;
        parent::tearDown();
    }

    /**
     * Tests attribute result from XmlSearch->query()
     */
    public function testQuery_Attr() {
        echo "| test testQuery_Attr \n";
        $result1 = $this->XmlSearchFile->queryArray('//elm/@id');
        $this->assertTrue(is_array($result1), "Query does not return a array.");
        $this->assertEquals($result1[0], 1, "Query does not return the correct value.");

        $result2 = $this->XmlSearchString->queryArray('//elm/@id');
        $this->assertTrue(is_array($result2), "Query does not return a array.");
        $this->assertEquals($result2[0], 1, "Query does not return the correct value.");

        $this->assertSame($result1, $result2);
    }

    /**
     * Tests text result from XmlSearch->query()
     */
    public function testQuery_Text() {
        echo "| test testQuery_Text \n";
        $result1 = $this->XmlSearchFile->queryArray('//elm/text()');
        $this->assertTrue(is_array($result1), "Query does not return a array.");
        $this->assertEquals($result1[0], 'foo bar', "Query does not return the correct value.");

        $result2 = $this->XmlSearchString->queryArray('//elm/text()');
        $this->assertTrue(is_array($result2), "Query does not return a array.");
        $this->assertEquals($result2[0], 'foo bar', "Query does not return the correct value.");

        $this->assertSame($result1, $result2);
    }

    public function testQueryElementAttributes() {
        echo "| test testQueryElementAttributes \n";
        $result1 = $this->XmlSearchFile->queryElementAttributes('//elm');
        $this->assertTrue(is_array($result1), "Query does not return a array.");
        $this->assertTrue(is_array($result1[0]), "Query does not return a inner-array.");
        $this->assertEquals($result1[0]['id'], 1, "Query does not return the correct value.");
    }

}

