<?php

require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'Validator.interface.php';

/**
 * XmlStringValidator<br />
 * ======================<br />
 * Validates a xml-string with or without a xsd with the ValidatorInterface<br />
 * 
 * @package		PCL
 * @subpackage          PCLConfiguration/Validation
 * 
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/PCLConfiguration_Validation
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 * @implementation 
 */
class XmlStringValidator implements ValidatorInterface {
	
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
	 * @param string XmlString
         * @oaram @optional File xsd
	 */
	function __construct( $xml, File $xsd = null) {
		$this->checkStringPresent ( $xml );
		$this->validateXmlStructure ( $xml );
		if ($xsd !== null) {
			$this->checkStringPresent ( $xsd );
			$this->validateXmlStructure ( $xsd);
			$this->validateSchema ( $xml, $xsd );
		}
	}
	
	/**
	 * check if file is present and readable.
	 *
         * @param string xml
	 */
	private function checkStringPresent($xml) {
		if (is_string ( $xml )) {
			$this->result = true;
		}
	}
	
	/**
	 * check the structure of a xml-file.
	 * 
         * @param string xml
	 */
	private function validateXmlStructure( $xml) {
		try {
			libxml_clear_errors();
			$doc = new DOMDocument ( );
			@$doc->loadXml ( $xml );
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
         * @param string xml
         * @param File xsdfile
	 */
	private function validateSchema( $xml, File $fileXsd) {
		try {
			$doc = new DOMDocument ( );
			$doc->loadXML ( $xml );
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