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
    * Parsea la Respuesta de la Busqueda del cliente
    *
    * @param String $response: XML Respuesta de WS
    * @return unknown
    */
	
	public function parseSearchXML($response){
		$enc = array();
		$type =  array();
		
		$xmlObj = new SimpleXMLParser;
		
		$tagResponse = $xmlObj->Parse($response, "response");
		$customerTag = $xmlObj->Parse($tagResponse, "customer");
		
		$IdGroup = $xmlObj->Parse($customerTag, "IdGroup");
		$enc['IdGroup'] = $IdGroup;
		
		$AddressTag = $xmlObj->Parse($customerTag, "Address");
		$enc['Address'] = $AddressTag;
		//print "Direccion : ".$AddressTag;
		
		$AgeRangeTag = $xmlObj->Parse($customerTag, "AgeRange");
		$enc['AgeRange'] = $AgeRangeTag;
		//print "Age Range : ".$AgeRangeTag;
		
		$ContactTag = $xmlObj->Parse($customerTag, "Contact");
		$enc['Contact'] =$ContactTag;
		//print "Contact : ".$ContactTag;
		
		$EmailTag = $xmlObj->Parse($customerTag, "Email");
		$enc['Email'] =$EmailTag;
		//print "Email : ".$EmailTag;
		
		$FaxTag = $xmlObj->Parse($customerTag, "Fax");
		$enc['Fax'] =$FaxTag;
		//print "Fax : ".$FaxTag;
		
		$FirstNameTag = $xmlObj->Parse($customerTag, "FirstName");
		$enc['FirstName'] =$FirstNameTag;
		//print "FirstName : ".$FirstNameTag;
		
		$GenderTag = $xmlObj->Parse($customerTag, "Gender");
		$enc['Gender'] =$GenderTag;
		//print "Gender : ".$GenderTag;
		
		$IdCategoryTag = $xmlObj->Parse($customerTag, "IdCategory");
		$enc['IdCategory'] =$IdCategoryTag;
		//print "IdCategory : ".$IdCategoryTag;
		
		$IdCustomerTag = $xmlObj->Parse($customerTag, "IdCustomer");
		$enc['IdCustomer'] = $IdCustomerTag;
		//print "IdCustomer : ".$IdCustomerTag;
		
		$IdDocTag = $xmlObj->Parse($customerTag, "IdDoc");
		$enc['IdDoc'] =$IdDocTag;
		//print "IdDoc : ".$IdDocTag;
		
		$IdTypeContribuyenteTag = $xmlObj->Parse($customerTag, "IdTypeContribuyente");
		$enc['IdTypeContribuyente'] =$IdTypeContribuyenteTag;
		//print "IdTypeContribuyente : ".$IdTypeContribuyenteTag;
		
		$LocationTag = $xmlObj->Parse($customerTag, "Location");
		$enc['Location'] =$LocationTag;
		//print "Location : ".$LocationTag;
		
		$OccupationTag = $xmlObj->Parse($customerTag, "Occupation");
		$enc['Occupation'] =$OccupationTag;
		//print "Occupation : ".$OccupationTag;
		
		$PhoneTag = $xmlObj->Parse($customerTag, "Phone");
		$enc['Phone'] =$PhoneTag;
		//print "Phone : ".$PhoneTag;
		
		$Phone2Tag = $xmlObj->Parse($customerTag, "Phone2");
		$enc['Phone2'] =$Phone2Tag;
		//print "Phone2 : ".$Phone2Tag;
		
		$QuotaTag = $xmlObj->Parse($customerTag, "Quota");
		$enc['Quota'] =$QuotaTag;
		//print "Quota : ".$QuotaTag;
		
		$ReteFuenteTag = $xmlObj->Parse($customerTag, "ReteFuente");
		$enc['ReteFuente'] =$ReteFuenteTag;
		//print "ReteFuente : ".$ReteFuenteTag;
		
		$ReteIcaTag = $xmlObj->Parse($customerTag, "ReteIca");
		$enc['ReteIca'] =$ReteIcaTag;
		//print "ReteIca : ".$ReteIcaTag;
		
		$ReteIvaTag = $xmlObj->Parse($customerTag, "ReteIva");
		$enc['ReteIva'] =$ReteIvaTag;
		//print "ReteIva : ".$ReteIvaTag;
		
		$StateTag = $xmlObj->Parse($customerTag, "State");
		$enc['State'] =$StateTag;
		//print "State : ".$StateTag;
		
		$Surname1Tag = $xmlObj->Parse($customerTag, "Surname1");
		$enc['Surname1'] =$Surname1Tag;
		//print "Surname1 : ".$Surname1Tag;
		
		$Surname2Tag = $xmlObj->Parse($customerTag, "Surname2");
		$enc['Surname2'] =$Surname2Tag;
		//print "Surname2 : ".$Surname2Tag;
		
		$descTag = $xmlObj->Parse($customerTag, "desc");
		$enc['desc'] =$descTag;
		//print "desc : ".$descTag;
		
		$DepartmentTag = $xmlObj->Parse($customerTag, "Department");
		$enc['Department'] =$DepartmentTag;
		//print "Department : ".$DepartmentTag;
		
		$ProvinceTag = $xmlObj->Parse($customerTag, "Province");
		$enc['Province'] =$ProvinceTag;
		//print "Province : ".$ProvinceTag;
		
		$ExenIvaTag = $xmlObj->Parse($customerTag, "ExenIva");
		$enc['ExenIva'] =$ExenIvaTag;
		//print "ExenIva : ".$ExenIvaTag;
		
		$OtrIvaTag = $xmlObj->Parse($customerTag, "OtrIva");
		$enc['OtrIva'] =$OtrIvaTag;
		//print "OtrIva : ".$OtrIvaTag;
		
		$StatusTag = $xmlObj->Parse($customerTag, "Status");
		$enc['Status'] =$StatusTag;
		//print "Status : ".$StatusTag;
				
		$Profession = $xmlObj->Parse($customerTag, "Profession");
		$enc['Profession'] =$Profession;
				
		$RewardPoints = $xmlObj->Parse($customerTag, "RewardPoints");
		$enc['RewardPoints'] =$RewardPoints;
		
		//Revisa numero de TYPECUSTOMER
		$xmlTypeCustomer = $xmlObj->Parse($customerTag, "TypeCustomer");
		$enc['TypeCustomer'] =$xmlTypeCustomer;
		//echo "TypeCustomer: " , $xmlTypeCustomer, "<br/>";
		
		$maxType = $xmlObj->MaxElements($xmlTypeCustomer, "IdTypeCustomer");
		
		// Recupera los Tipos de clientes del XML
		$i=0;
		while($i<$maxType){
				$type[$i]['IdTypeCustomer'] = $xmlObj->Parse($xmlTypeCustomer, "IdTypeCustomer", $i);
				$i++;
		}
				
		
		$CustomerLoyality = $xmlObj->Parse($customerTag, "CustomerLoyality");
  		$maxProgram = $xmlObj->MaxElements($CustomerLoyality, "Program");
		//echo "maxProgram: ", $maxProgram, "<br/>";
  
		$i=0;
  		while($i<$maxProgram){
  				$Program[$i]['IdProgram'] = $xmlObj->Parse($CustomerLoyality, "IdProgram", $i);
  				$Program[$i]['Flag'] = $xmlObj->Parse($CustomerLoyality, "Flag", $i);
  				$Program[$i]['Amount'] = $xmlObj->Parse($CustomerLoyality, "Amount", $i);
  				$i++;
  		}
		
		if($maxProgram>0)
			$arr = array_merge($enc, $type, $Program);
		else
			$arr = array_merge($enc, $type);

		return $arr;
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