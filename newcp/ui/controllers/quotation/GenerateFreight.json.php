 <?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');
require_once includePath('business/Quotation.php');
require_once includePath('business/Location.php');
require_once includePath('sdo/FreightService.php');
require_once includePath('dao/QuotationDAO.php');

$session = USession::GetInstance();
$loggedUser = $session->loggedUser;
$request =  URequest::GetInstance();

$sourceLocation = new Location($loggedUser->storeLocationId);
$targetLocation = new Location($request->locationId);

$quotation = QuotationDAO::GetQuotation($request->quotationId);
$freightService = FreightService::GetInstance();
$freights = $freightService->getFreightFare(
    $sourceLocation,
    $targetLocation,
    $request->addressId,
    2,
    $quotation->getTotalWeight(),
    $loggedUser->storeIdPos,
    0);

$response = new stdClass;
$response->success = false;
if ($freights) {
    QuotationDAO::AddFreightItems(
            $quotation->id,
            $quotation->storeId,
            $freights,
            $quotation->estimatedDispatchDate);
    $response->success = true;
}
print json_encode($response);
?>