<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Financial.php');
require_once includePath('dao/ConfigDAO.php');

$finConfig = ConfigDAO::GetGroupConfiguration(CONFIG_GROUP_CHEQ_POS);
$request = URequest::GetInstance();

// validation
$result = new stdClass;
$result->success = false;
$result->messages = array();
if (!isset($finConfig->CHEQ_POS_INTERES)) {
    $result->messages[] = 'Error: No se ha definido el parámetro "CHEQ_POS_INTERES".'; // TODO: i18n
}
if (!isset($finConfig->CHEQ_POS_MAX_NUM)) {
    $result->messages[] = 'Error: No se ha definido el parámetro "CHEQ_POS_MAX_NUM".'; // TODO: i18n
}
if (!isset($finConfig->CHEQ_POS_MIN_INICIAL)) {
    $result->messages[] = 'Error: No se ha definido el parámetro "CHEQ_POS_MIN_INICIAL".'; // TODO: i18n
}

$minQuota = floatval($request->total) * $finConfig->CHEQ_POS_MIN_INICIAL;
if (floatval($request->initialQuota) < $minQuota) {
    $result->messages[] = "La cuota inicial debe ser por lo menos de " . ULang::FormatCurrency($minQuota); // TODO: i18n
}
if (floatval($request->initialQuota) >= floatval($request->total)) {
    $result->messages[] = "La cuota inicial debe ser inferior al valor total"; // TODO: i18n
}
if (intval($request->numQuotas) > $finConfig->CHEQ_POS_MAX_NUM) {
    $result->messages[] = "El máximo número de cheques autorizados es: $finConfig->CHEQ_POS_MAX_NUM"; // TODO: i18n
}

if (count($result->messages) == 0) {
    $result = calculateCreditWithFixedQuota($request->total, $request->initialQuota, $finConfig->CHEQ_POS_INTERES, $request->numQuotas, $finConfig->CHEQ_POS_IVA);
    $result->success = true;
    if ($result->quota == 0) {
        $result->messages = array('El valor de la cuota es demasiado bajo para este medio de pago');
    }
}
print json_encode($result);

?>