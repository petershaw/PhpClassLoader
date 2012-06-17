<?php

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
        $this->classcache = new ClassLoader(__CLASS__, dirname(__FILE__));
        $this->classcache->mode = 'sqllite';
        $this->classcache->clear();
    }

    /**
     * Called by PHPUnit as kinda desturctor for all methods!
     */
    protected function tearDown() {
        unset($this->classcache);
    }

    /**
     * Test: Load a Class
     */
    public function testSQLLoad() {
        echo "| test testSQLLoad\n";
        $testObj = new uuuxxxgibtsnicht();
        $result = $this->classcache->getAllKnownClasses();
        $this->assertTrue(array_key_exists("uuuxxxgibtsnicht", $result), 'Testclass not found.');
        unset($testObj);
    }

}