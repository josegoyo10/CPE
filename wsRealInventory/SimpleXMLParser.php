<?php
class SimpleXMLParser {

   // Find the max number of specific nodes in the XML
   function MaxElements($XMLSource, $XMLName) {
           $MaxElements = 0;
           $XMLTag = "<" . $XMLName . ">";
           $Y = $this->instr($XMLSource, $XMLTag);
           while($Y>=0) {
                   $MaxElements = $MaxElements + 1;
                   $Y = $this->instr($XMLSource, $XMLTag, $Y + strlen($XMLTag));
           }
       return $MaxElements;
   }

   // Parse xml to retrieve a specific element
   // Instance number is a zero based index.
   function Parse($XMLSource, $XMLName, $aInstance = 0, $Default = "") {
           $XMLLength = strlen($XMLSource);
           $XMLTag = "<" . $XMLName . ">";
           $XMLTagEnd = "</" . $XMLName . ">";
           $Instance = $aInstance + 1;

       /* Find the start of the requested instance... */
           $XMLStart = 0;

           for($x = 1; $x < $Instance + 1; $x++) {
                   $Y = $this->instr($XMLSource, $XMLTag, $XMLStart);

                   if ($Y >= $XMLStart) {
                           $XMLStart = $Y + strlen($XMLTag);
                   }
                   else {
                           return $Default;
                   }
           }

       /* Find the end of the instance... */
           $XMLEnd = $XMLStart;
           $XMLMatch = 1;

           while($XMLMatch) {
                   $c = substr($XMLSource, $XMLEnd, strlen($XMLTagEnd));
                   if($c == $XMLTagEnd) {
                           $XMLMatch = $XMLMatch - 1;
                   }
                   else {
                       if (substr(c, 0, 1) == $XMLTag) {
                           $XMLMatch = $XMLMatch + 1;
                       }
                   }
                   $XMLEnd = $XMLEnd + 1;
                   if ($XMLEnd == $XMLLength) {
                           return $Dufault;
                   }
           }
           return substr($XMLSource, $XMLStart, $XMLEnd - $XMLStart - 1);
   }

   // Helper function for finding substrings
   function instr($haystack, $needle, $pos = 0) {
       $thispos = strpos($haystack, $needle, $pos);
       if ($thispos===false)
           $thispos = -1;
       return $thispos;
   }
   
   
   /**
    * Parsea la Respuesta de la Busqueda de Inventario Realizada a Bopos y Flejes
    *
    * @param String $response: XML Respuesta de WS
    * @return unknown
    */
	  		
   
	public function parseSearchInventoryXML($response){
		$resultado = array();
		$inventario =  array();
		$result =  array();
		
		//echo "Entro a Parsear", "<br>";
		
		$xmlObj = new SimpleXMLParser;
				
		
		$tagResposeInv = $xmlObj->Parse($response, "response");
		
		$tagException = $xmlObj->Parse($tagResposeInv, "exception");
		
		//======== Parseo Mensaje ==================================
		$messageTag = $xmlObj->Parse($tagException, "message");
		$resultado['message'] = $messageTag;
		
		$stateTag = $xmlObj->Parse($tagException, "state");
		$resultado['state'] = $stateTag;
		
		//======== Parseo de Inventario ============================
		//$inventarioTag = $xmlObj->Parse($tagResposeInv, "inventario");		
		
		$InventoryTag = $xmlObj->Parse($tagResposeInv, "inventory");		
		$maxInventory = $xmlObj->MaxElements($tagResposeInv, "inventory");
		
		$resultado['maxInventory'] = $maxInventory;
		
		//echo "maxInventory: ", $maxInventory, "<br>";
		
		$i=0;
  		while($i < $maxInventory){		
		
		$inventario[$i]['EAN'] = $xmlObj->Parse($tagResposeInv, "ean", $i);
		$inventario[$i]['SAP'] = $xmlObj->Parse($tagResposeInv, "sap", $i);		 
		$inventario[$i]['store'] = $xmlObj->Parse($tagResposeInv, "store", $i);
		$inventario[$i]['posStoreCode'] = $xmlObj->Parse($tagResposeInv, "posStoreCode", $i);		
		$inventario[$i]['amount'] = $xmlObj->Parse($tagResposeInv, "amount", $i);
		
		$i++;		
		}
		
		$result = array_merge($resultado, $inventario);
		
		return $result;
	}
	
	/**
	 * Parse la Respuesta de Creacion y Actalizacion
	 *
	 * @param unknown_type $response
	 */
	public function parseResponseXML($response){
		$enc = array();
		
		$xmlObj = new SimpleXMLParser;
		
		$tagResponse = $xmlObj->Parse($response, "response");
				
		$descTag     = $xmlObj->Parse($tagResponse, "desc");
		$enc['desc'] =$descTag;
		
		$tagResponse = $xmlObj->Parse($tagResponse, "state");
		$enc['state'] =$tagResponse;
		
		return $enc;
	}
}
?>
