<?php

/**
 * XmlSearch<br />
 * ======================<br />
 * Search easily throw a xml-document<br />
 * 
 * @package		PCL
 * @subpackage          XmlSearch
 * 
 * @link		https://github.com/petershaw/PhpClassLoader/wiki/XmlSearch
 * @author		@peter_shaw
 *
 * @version		1.0.0
 * @since               1.0.0
 * 
 */
class XmlSearch extends DOMDocument {

    private $xpath;

    /**
     * Pass a File or a valid xml-string to the constructor. 
     *
     * @param string xml
     */
    public function __construct($xml) {
        parent::__construct();
        if ($xml instanceof File) {
            $validator = new XmlFileValidator($xml);
            if ($validator->getResult() == true) {
                $this->load($xml->toString());
            } else {
                throw new XmlSearchException($xml, "Parameter is not a valid xml file.");
            }
        } elseif (file_exists($xml)) {
            $xml = file_get_contents($xml);
            $validator = new XmlStringValidator($xml);
            if ($validator->getResult() == true) {
                $this->loadXML($xml);
            } else {
                throw new XmlSearchException($xml, "Parameter is not a valid xml string.");
            }
        } elseif (is_string($xml)) {
            $validator = new XmlStringValidator($xml);
            if ($validator->getResult() == true) {
                $this->loadXML($xml);
            } else {
                throw new XmlSearchException($xml, "Parameter is not a valid xml string.");
            }
        } else {
            throw new XmlSearchException($xml, "Parameter is not a file or a string.");
        }

        $this->xpath = new DOMXPath($this);
    }

    /**
     * Lets search on a namespace
     * @see registerNamespace in XPath
     * 
     * @param string $a
     * @param string $b 
     */
    public function registerNamespace($a, $b) {
        $this->xpath->registerNamespace($a, $b);
    }

    /**
     * Returns a simple resultarray from a xpath-query.
     *
     * @throws XMLSearchException
     * @param string $xpath
     * @return array
     */
    public function queryArray($xpath) {
        $arr = array();
        $result = $this->xpath->query($xpath);
        foreach ($result as $r) {
            if ($r instanceof DOMAttr) {
                array_push($arr, $r->value);
            } elseif ($r instanceof DOMText) {
                array_push($arr, trim($r->textContent));
            } else {
                throw new XMLSearchException($xpath, "Unsupported resulttype: " . get_class($r));
            }
        }
        return $arr;
    }

    /**
     * Query a xml and get a traversal array 
     * 
     * @param string $xpath
     * @return array
     * @throws XMLSearchException 
     */
    public function queryElementAttributes($xpath) {
        $arr = array();
        $result = $this->xpath->query($xpath);
        foreach ($result as $r) {
            if ($r instanceof DOMElement) {
                $attrs = $r->attributes;
                $thisAttrStack = array();
                foreach ($attrs as $a) {
                    $thisAttrStack[$a->nodeName] = $a->nodeValue;
                }
                array_push($arr, $thisAttrStack);
            } else {
                throw new XMLSearchException($xpath, "Unsupported resulttype: " . get_class($r));
            }
        }
        return $arr;
    }

}

?>
