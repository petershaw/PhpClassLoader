<?php

/**
 * PhpVersionTest
 * ============================
 * Testing the minimal PHP Version and required modules
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
class PhpVersionTest extends PHPUnit_Framework_TestCase {

    public function testMinimalVersion() {
        echo "| test testMinimalVersion";
        $this->assertGreaterThanOrEqual(50217, PHP_VERSION_ID, "PHP minimal version 5.2 is not matched.");
    }

}

