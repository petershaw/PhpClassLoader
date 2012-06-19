<?php

/*
 * PhpClassLoader Example
 *
 * This example demonstrates, how the classloader is used to laod 
 * classes inside the lib directory.
 * 
 * @url https://github.com/petershaw/PhpClassLoader/wiki/Example
 */
class class_A {
    
    function __construct(){
        
    }
    
    public function getB(){
        $b = new class_B();
        return $b->getMessage();
    }

   public function getAMessage(){
	return "I am class A";
   }

}

?>
