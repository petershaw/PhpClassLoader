<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'XmlFileValidator.class.php';
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . '../../file/File.class.php';

/**
 * XmlFileValidatorTest
 * ============================
 * Tests various xml files.
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
class XmlFileValidatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @var XmlValidator
     */
    private $XmlValidator;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        $this->XmlValidator = null;
        parent::tearDown();
    }

    /**
     * Tests XmlValidator simple xml
     */
    public function test_Simple() {
        echo "| test test_Simple \n";
        $this->XmlValidator = new XmlFileValidator(
                        new File(dirname(__FILE__) . "/resources/true/test01.xml")
        );
        $this->assertTrue(
                $this->XmlValidator->getResult(), "A correct xml is not valid."
        );
        $this->XmlValidator = new XmlFileValidator(
                        new File(dirname(__FILE__) . "/resources/false/test01.xml")
        );
        $this->assertFalse(
                $this->XmlValidator->getResult(), "A false xml is valid."
        );
    }

    /**
     * Tests XmlValidator with umlaut in xml
     */
    public function test_Umlaut() {
        echo "| test test_Umlaut \n";
        $this->XmlValidator = new XmlFileValidator(new File(dirname(__FILE__) . "/resources/true/test02.xml"));
        $this->assertTrue($this->XmlValidator->getResult(), "A correct xml with umlaut is not valid.");
    }

    /**
     * Tests XmlValidator with valid xsd (Structuretest)
     */
    public function test_SimpleXsdStructure() {
        echo "| test test_SimpleXsdStructure \n";
        $this->XmlValidator = new XmlFileValidator(new File(dirname(__FILE__) . "/resources/true/test.xsd"));
        $this->assertTrue($this->XmlValidator->getResult(), "A correct xsd is not a valid document.");
    }

    /**
     * Tests XmlValidator simple valid xml
     */
    public function test_SimpleXsd() {
        echo "| test test_SimpleXsd \n";
        $this->XmlValidator = new XmlFileValidator(new File(dirname(__FILE__) . "/resources/true/test01.xml"), new File(dirname(__FILE__) . "/resources/true/test.xsd"));
        $this->assertTrue($this->XmlValidator->getResult(), "A correct xml is not valid against a proper xsd.");
        $this->XmlValidator = new XmlFileValidator(new File(dirname(__FILE__) . "/resources/true/test01.xml"), new File(dirname(__FILE__) . "/resources/false/test.xsd"));
        $this->assertFalse($this->XmlValidator->getResult(), "A correct xml is valid against a broken xsd.");
        $this->XmlValidator = new XmlFileValidator(new File(dirname(__FILE__) . "/resources/true/test01.xml"), new File(dirname(__FILE__) . "/resources/false/test02.xsd"));
        $this->assertFalse($this->XmlValidator->getResult(), "A invalid xml is valid against a proper xsd.");
    }

    /**
     * Tests XmlValidator simple valid xml
     */
    public function test_SimpleXsdUmlaut() {
        echo "| test test_SimpleXsdUmlaut \n";
        $this->XmlValidator = new XmlFileValidator(new File(dirname(__FILE__) . "/resources/true/test02.xml"), new File(dirname(__FILE__) . "/resources/true/test.xsd"));
        $this->assertTrue($this->XmlValidator->getResult(), "A correct xml with umlaut is not valid against a proper xsd.");
    }

}

