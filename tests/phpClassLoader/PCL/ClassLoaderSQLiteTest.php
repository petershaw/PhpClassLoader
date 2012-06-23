<?php

$equivalentSrcDir = str_replace("tests", "php-inc", dirname(__FILE__));
require_once $equivalentSrcDir . DIRECTORY_SEPARATOR . 'ClassLoader.class.php';

/**
 * ClassLoaderSQLiteTest
 * ============================
 * This suite tests the ClassLoader in sqlite mode.
 * The sqlite mode is for productive while it is not possible to use a in 
 * memory storage. 
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
class ClassLoaderSQLiteTest extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {

    /**
     * Classloader object
     * @var type 
     */
    protected $classcache;

    /**
     * Called by PHPUnit as kinda consturctor for all methods!
     */
    protected function setUp() {
        echo "---- setup sqllite test\n";
        $this->classcache = ClassLoader::getInstance('classloadersqlitetest', dirname(__FILE__), true);

        ClassLoader::$mode = 'sqllite';
        $file = $this->classcache->getCacheFile();
        echo "created cache file: " . $file . "\n";
    }

    /**
     * Called by PHPUnit as kinda desturctor for all methods!
     */
    protected function tearDown() {
        unset($this->classcache);
        if (file_exists(ClassLoader::getCacheFile())) {
            unlink(ClassLoader::getCacheFile());
        }
    }

    /**
     * Test: Load a Class
     */
    public function testSQLLoad() {
        echo "| test testSQLLoad\n";

        $testObj = new yyyxxxgibtsnicht();
        $this->assertEquals("for testing", $testObj->reason, "Could not get public property from testcalss-");

        $result = $this->classcache->getAllKnownClasses();
        $this->assertTrue(array_key_exists("yyyxxxgibtsnicht", $result), 'Testclass not found.');

        unset($testObj);
    }

}