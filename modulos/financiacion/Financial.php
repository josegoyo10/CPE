<?php
$SIN_PER = 1;
include_once('../../includes/aplication_top.php');

	// Baja las variables enviadas por GET
	$cuotaInit = floatval($_GET['cuotaInit']); 
	$total = floatval($_GET['total']); 
	$cheques = intval($_GET['cheques']);  
	$ivaCheq = CHEQ_POS_IVA;

	// Consulta el interes
	$interes = consulta_Interes($cheques);
	
	$amount = $total - $cuotaInit;
    if ($amount < 0) { $amount = 0.0; }
    
	if($interes == 0){
	  // Calcula el Interes
		$quota1 = ($amount + $interest) / $cheques;
	}
	else{
		$quota1 = round($amount * $interes) * (pow(1+$interes, $cheques)/(pow(1+$interes, $cheques)-1));
	}
    	
    	$interest = round(($quota1 * $cheques) - $amount);
    	//Completa los Valores a retornar
   	 	$baseIvaInt = number_format((($interest) / ($ivaCheq + 1)), 0, '', '.'); 
    	$IvaInt = number_format((round(((round($interest) / ($ivaCheq + 1))) * $ivaCheq)), 0, '', '.');
    	$totalFin = number_format(($total + round($interest)), 0, '', '.');   
    	$interest = number_format($interest, 0, '', '.');    
    	$cuotaInit = number_format($cuotaInit, 0, '', '.');
    	$amount = number_format($amount, 0, '', '.');
    	$interes = $interes*100;
    	$quota1 = number_format($quota1, 0, '', '.');
    
   	echo $totalFin."|".$cuotaInit."|".$cheques."|".$amount."|".$interes."|".$quota1."|".$ivaCheq."|".$interest."|".$IvaInt."|".$baseIvaInt;

?>
