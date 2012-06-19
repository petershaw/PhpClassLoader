<?php

/*
 * PhpClassLoader Example
 *
 * This example demonstrates, how the classloader is used to laod 
 * classes inside the lib directory.
 * 
 * @url https://github.com/petershaw/PhpClassLoader/wiki/Example
 */
class class_B {

    function __construct(){
        
    }
    
    public function getMessage(){
        return "Hello ClassLoader (from class B)";
    }

}

?>
