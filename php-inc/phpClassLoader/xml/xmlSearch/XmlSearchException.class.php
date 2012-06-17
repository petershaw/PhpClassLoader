<?php

/**
 * XmlSearchException<br />
 * ======================<br />
 * This exception occurs on file errors that a search on a xml file throw.<br />
 * 
 * @package		PCL
 * @subpackage          XmlSearch
 *
 * @exception
 *  
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/XmlSearch
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class XmlSearchException extends Exception {
	
	/** Errorcode */
	protected $code = 0;
	
	/** the file object */
	protected $file;
	
	/**
	 * 
	 * @param xml-object
	 * @param message
	 */
	public function __construct($xmlOrQuery, $message ) {
		$message = $message ."\nDump: ". var_export($xmlOrQuery); 
		parent::__construct ( $message, $this->code );
	}

}

?>
