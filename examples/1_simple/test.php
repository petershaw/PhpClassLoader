<?php

/*
 * PhpClassLoader Example
 * 1_simple
 *
 * This example demonstrates, how the classloader is used to laod 
 * classes inside the lib directory.
 * 
 * @url https://github.com/petershaw/PhpClassLoader/wiki/Example
 */

// First of all, include the classloader:
require_once dirname(__FILE__).'/../lib/PhpClassLoader.phar.gz';

// just use a class
$a = new class_A();
// call to a method 
echo $a->getAMessage() ."\n";

// call to a subclass method. (nasted loading)
echo $a->getB() ."\n";

?>
