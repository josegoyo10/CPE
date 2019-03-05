<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Quotation class.
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class Quotation {

    /** id_os */
    public $id = 0;

    /** id_estado */
    public $statusId = 'SA';
    public $statusName;

    /** type_ */
    public $typeId = 'PSE';

    /** id_proyecto */
    public $projectId = 0;
    public $projectName;

    /** id_local */
    public $storeId;
    public $storeName;

    /** id_direccion */
    public $addressId;

    /** clie_rut */
    public $customerId;

    /** os_fechacreacion */
    public $creationDate;

    /** os_fechacotizacion */
    public $quotationDate;

    /** os_fechaestimada */
    public $estimatedDispatchDate;

    /** os_fechaestimacion (vencimiento)*/
    public $overdueDate;

    /** os_comentarios */
    public $comments;

    /** os_descripcion */
    public $description;

    /** usuario REDUNDANTE CON USR_ID */
    public $login;

    /** USR_ID REDUNDANTE CON usuario */
    public $userId;
    public $userName;

    /** origen */
    public $source = 'C';

    /** USR_ORIGEN = 1 (Centro de proyectos) Ver tabla OrigenUsr */
    public $sourceUser = 1;

    /** os_codbarras */
    public $barcode;

    /** Cuota inicial osexth.min_ */
    public $initialQuota;

    /** Motivo de anulacion osexth.void_reason_ */
    public $voidReason = null;

    /** Arras osexth.downpayment_ */
    public $downpayment = null;

    /** Detalle de productos */
    public $items = array();

    /**
     * Constructor
     */
    public function __construct() {
    }

    public function addItem(QuotationItem $item) {
        $this->items[] = $item;
    }

    public function getItem($row) {
        if (isset ($this->items[$row])) {
            return $this->items[$row];
        }
    }

    public function setItem($row, QuotationItem $item) {
        $this->items[$row] = $item;
    }

    public function removeItem($row) {
        if (array_key_exists($row, $this->items)) {
            unset($this->items[$row]);
            $tmp = array();
            foreach ($this->items as $item) {
                $tmp[] = $item;
            }
            $this->items = $tmp;
        }
    }

    public function removeAll() {
        $this->items = array();
    }

    public function getNumItems() {
        return count($this->items);
    }

    public function getTotal() {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total = $total + $item->getSubTotal();
        }
        return $total;
    }

    public function getTotalWeight() {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total = $total + $item->productWeight;
        }
        return $total/1000; // return weight in Kg
    }

    public function hasFreight() {
        $has = false;
        foreach ($this->items as $item) {
            $has |= ($item->productType == 'SV' && $item->productSubType = 'CE');
        }
        return $has; // return weight in Kg
    }
}

/**
 * Quotation class.
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class QuotationItem {

    public $dispatchTypeId;
    public $dispatchTypeDescription;
    public $productId;
    public $productSAPId;
    public $productDescription;
    public $productType;
    public $productSubType;
    public $productWeight = 0.0;
    public $productBarcode;
    public $allowDecimals = false;
    public $install = false;
    public $price = 0.0;
    public $cost = 0.0;
    public $quantity = 0;
    public $discount = 0.0;
    public $specification = null;
    public $editablePrice = false;
    public $deliveryDate = null;

    public function getSubTotal() {
        return floatval($this->quantity) * (floatval($this->price) - floatval($this->discount));
    }

    public function getAsArray($i) {
        return array(
            $i,
            $this->productBarcode,
            $this->productDescription,
            $this->dispatchTypeDescription,
            $this->install ? true : false,
            $this->quantity,
            $this->price,
            $this->discount ? ULang::FormatCurrency($this->discount) : '-',
            ULang::FormatCurrency($this->getSubTotal()),
            $this->specification,
            $this->editablePrice
        );
    }

}
?>