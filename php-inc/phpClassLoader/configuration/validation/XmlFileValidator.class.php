<?php

/**
 * XmlFileValidator<br />
 * ======================<br />
 * Validates a xml with or without a xsd with the ValidatorInterface
 * This part is taken from PHPCarpet's more generalized Validationsuite.
 * This Class is a strip down for the standalone cassloader, because someone ask
 * for it. <br />
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
 * @implementation 
 */
class XmlFileValidator implements ValidatorInterface {
	
	/**
	 * The result is the overall test result of the validator.
	 *
	 * @var boolean
	 */
	private $result = false;
	
	/**
	 * Create a new XmlFileValidator
	 * The first parameter is the xml, the second a optional xsd
         * 
	 * @param File xmlfile
         * @param @optional File xsdFile
	 */
	function __construct(File $xml, File $xsd = null) {
		$this->checkFilePresent ( $xml );
		$this->validateXmlStructure ( $xml );
		if ($xsd !== null) {
			$this->checkFilePresent ( $xsd );
			$this->validateXmlStructure ( $xsd);
			$this->validateSchema ( $xml, $xsd );
		}
	}
	
	/**
	 * check if file is present and readable.
         * sets result and return a value.
         * result is not set to false if file is not present, but this method 
         * returns false. 
         * 
	 * @param File file
         * @return boolean
	 */
	private function checkFilePresent(File $file) {	
		if (is_file ( $file->toString () ) && file_exists ( $file->toString () ) && is_readable ( $file->toString () )) {
			$this->result = true;
                        return true;
		}
                return false;
	}
	
	/**
	 * check the structure of a xml-file.
	 *
	 */
	private function validateXmlStructure(File $file) {
		try {
			libxml_clear_errors();
			$doc = new DOMDocument ( );
			@$doc->loadXml ( $file->getContent() );
			$errors = libxml_get_errors ();
			if (empty ( $errors ) && count(explode("\n", trim($doc->saveXML()))) >= 2) {
				$this->result = true;
			} else {
				$this->result = false;
			}
		} catch ( Exception $e ) {
			$this->result = false;
		}
	}
	
	/**
	 * validates a xml against a xsd.
	 *
	 */
	private function validateSchema(File $fileXml, File $fileXsd) {
		try {
			$doc = new DOMDocument ( );
			$doc->load ( $fileXml->toString () );
			@$this->result = $doc->schemaValidate ( $fileXsd->toString () );
		} catch ( Exception $e ) {
			$this->result = false;
		}
	}
	
	/**
	 * Get the result of this validation
         * 
	 * @return boolean 
	 * @see ValidatorInterface::getResult()
	 */
	public function getResult() {
		return $this->result;
	}
}

?>