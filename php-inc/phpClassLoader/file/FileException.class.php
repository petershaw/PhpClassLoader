<?php

/**
 * FileException<br />
 * ======================<br />
 * This exception occurs on file errors.<br />
 * 
 * @package		PCL
 * @subpackage          File
 *
 * @exception
 *  
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/File
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 *
 */
class FileException extends Exception {
	
	/** Errorcode */
	protected $code = 0;
	
	/** the file object */
	protected $file;
	
	/**
	 * Construct a new Exception
	 *@param file
	 *@param message
	 */
	public function __construct(File $file, $message) {
		$this->file = $file->getDirname () . "/" . $file->getFilename ();
		parent::__construct ( $message, $this->code );
	}

}

?>
