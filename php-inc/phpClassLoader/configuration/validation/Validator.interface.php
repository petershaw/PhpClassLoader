<?php

/**
 * ValidatorInterface<br />
 * ======================<br />
 * This interface describes all validator-classes. A Validator has to implement
 * all functions descriped here. This part is taken from PHPCarpet's more 
 * generalized Validationsuite.<br />
 * 
 * @package		PCL
 * @subpackage          Configuration/Validation
 * 
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLConfiguration_Validation
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @interface
 */
interface ValidatorInterface {

    /**
     * Returns a boolean value about the validation process
     * 
     * @return boolean
     */
    public function getResult();
}

?>