<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of QuotationDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class QuotationDAO {

    public static function searchCustomerQuotations($customerId, $projectId = 0, $statusId = '') {
        $conn = UDataSource::GetConnection();
        $customerId = intval($customerId);
        $projectId = intval($projectId);
        $filter = '';
        if ($projectId) {
            $filter .= " AND os.id_proyecto = $projectId ";
        }
        if (trim($statusId) != '') {
            $filter .= ' AND os.id_estado = ' . $conn->qstr($statusId);
        }
        $sql = "SELECT
            os.id_os as id,
            os.id_estado as statusId,
            os.os_descripcion as description,
            os.os_fechacreacion as dateCreated,
            pr.proy_nombre as projectName,
            st.esta_nombre as statusName
            FROM os LEFT JOIN proyectos pr ON os.id_proyecto = pr.id_proyecto
                LEFT JOIN estados st ON os.id_estado = st.id_estado
            WHERE os.clie_rut = $customerId $filter
            ORDER BY os.os_fechacreacion DESC";
        $rs = $conn->Execute($sql);
        return $rs->GetRows();
    }

    public static function SaveQuotation(Quotation $q) {

        require_once includePath('business/dv.php');

        $session = USession::GetInstance();
        $psConfig = $session->psConfig;

        if (!intval($q->id)) {
            $q->creationDate = time();
            $q->quotationDate = time();
        }

        $days = $psConfig->PLAN_SEPARE_DIAS;
        // +1 day if dispatch type is D.Programado(1)
        if ($q->items[0]->dispatchTypeId == 1) {
            $days++;
        }
        $q->estimatedDispatchDate = $q->quotationDate + ($days * 86400);
        $q->overdueDate = $q->estimatedDispatchDate;

        $osSQL = "INSERT INTO os (id_estado,id_proyecto,id_local,id_direccion,clie_rut,
            os_fechacreacion,os_fechacotizacion,os_fechaestimada,os_comentarios,
            os_descripcion,usuario,usr_id,os_fechaestimacion,origen,usr_origen,
            os_cotizaciones_cruzadas,os_fechaboleta,os_numboleta,os_codbarras,
            os_terminal_pos,zona,type_)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $osexSQL = "INSERT INTO osexth(id_,min_,total_,last_payment_)
            VALUES(?,?,?,?)";

        $osexDocSQL = "INSERT INTO osextd_document(order_id_,doc_id_)
            VALUES(?,?)";

        $conn = UDataSource::GetConnection();
        $osdeSQL = $conn->Prepare("INSERT INTO os_detalle (id_origen,id_tipodespacho,
            id_producto,id_os,osde_tipoprod,osde_subtipoprod,osde_instalacion,
            osde_precio,osde_cantidad,osde_descuento,osde_preciocosto,cod_sap,
            osde_descripcion,cod_barra,ind_dec,osde_fecha_entrega)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $conn->StartTrans();
        $conn->Execute($osSQL, array(
            $q->statusId,
            $q->projectId,
            $q->storeId,
            $q->addressId,
            $q->customerId,
            date("Y-m-d H:m:s", $q->creationDate),
            date("Y-m-d H:m:s", $q->quotationDate),
            date("Y-m-d H:m:s", $q->estimatedDispatchDate), // TODO: os_fechaestimada, Verificar campo
            $q->comments,
            $q->description,
            $q->login,
            $q->userId,
            date("Y-m-d H:m:s", $q->overdueDate), // TODO: os_fechaestimacion, Verificar campo
            $q->source,
            $q->sourceUser,
            null, // cotizaciones_cruzadas, No aplica para Plan Separe
            null, // os_fechaboleta, Lo pone el POS
            null, // os_numboleta, Lo pone el POS
            $q->barcode,
            null, // terminal_pos, Lo pone el POS
            null, // zona, la devuelve el WS de fletes
            $q->typeId
        ));
        $q->id = $conn->Insert_ID();

        // Quotation's Barcode
        $barcode = '23' . str_pad($q->storeId, 3, '0', STR_PAD_LEFT) . str_pad($q->id, 7, '0', STR_PAD_LEFT);
        $barcode .= dv($barcode);
        $conn->Execute("UPDATE os SET os_codbarras = " . $conn->qstr($barcode) .
            " WHERE id_os = " . $q->id);

        // Extended quotation
        $total = $q->getTotal();
        $min = round($total * $psConfig->PLAN_SEPARE_CINICIAL);
        $conn->Execute($osexSQL, array($q->id, $min, $total, 0));

        // Related documents
        $conn->Execute($osexDocSQL, array($q->id, 'FINIQUITO'));

        // Quotation's detail
        foreach ($q->items as $item) {
            $item->deliveryDate = $q->estimatedDispatchDate;
            $conn->Execute($osdeSQL, array(
                4, // id_origen = 4 ???
                $item->dispatchTypeId,
                $item->productId,
                $q->id,
                $item->productType,
                $item->productSubType,
                $item->install,
                $item->price > 0 ? $item->price : null,
                $item->quantity,
                $item->discount,
                $item->cost,
                $item->productSAPId,
                $item->specification,
                $item->productBarcode,
                $item->allowDecimals,
                date("Y-m-d", $item->deliveryDate)
            ));
        }

        $conn->CompleteTrans();
    }

    public static function GetSimplifiedQuotation($id) {
        $id = intval($id);
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute("SELECT
            os.id_os as id,
            os.os_codbarras as barcode,
            os.os_fechacotizacion as quotationDate,
            os.os_fechaestimacion as overdueDate,
            os.clie_rut as customerId,
            sto.dir_local as storeAddress,
            sto.ciu_local as storeCity,
            concat(c.clie_nombre, ' ', c.clie_paterno, ' ', c.clie_materno) as customerName
            FROM os
            LEFT JOIN locales sto ON os.id_local = sto.id_local
            LEFT JOIN clientes c ON os.clie_rut = c.clie_rut
            WHERE os.id_os = $id");
        return $rs->FetchObj();
    }

    public static function GetQuotation($id) {
        require_once includePath('business/Quotation.php');
        $id = intval($id);
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute("SELECT
            os.id_os as id,
            os.id_estado as statusId,
            os.os_descripcion as description,
            os.os_fechacreacion as dateCreated,
            os.clie_rut as customerId,
            os.type_ as typeId,
            os.id_local as storeId,
            os.id_direccion as addressId,
            os.os_fechacotizacion as quotationDate,
            os.os_fechaestimada as estimatedDispatchDate,
            os.os_fechaestimacion as overdueDate,
            os.os_comentarios as comments,
            os.usuario as login,
            os.usr_id as userId,
            os.origen as source,
            os.os_codbarras as barcode,
            pr.proy_nombre as projectName,
            st.esta_nombre as statusName,
            sto.nom_local as storeName,
            concat(usr.usr_nombres, ' ', usr.usr_apellidos) as userName,
            x.min_ as initialQuota
            FROM os 
                LEFT JOIN proyectos pr ON os.id_proyecto = pr.id_proyecto
                LEFT JOIN estados st ON os.id_estado = st.id_estado
                LEFT JOIN locales sto ON os.id_local = sto.id_local
                LEFT JOIN usuarios usr ON os.usr_id = usr.usr_id
                LEFT JOIN osexth x ON os.id_os = x.id_
            WHERE os.id_os = $id");
        $row = $rs->FetchRow();
        $quotation = new Quotation();
        foreach ($row as $k => $v) {
            $quotation->$k = $v;
        }
        $rs->Close();
        $rs = $conn->Execute("SELECT
            d.id_os_detalle as id,
            d.cod_barra as productBarcode,
            d.osde_tipoprod as productType,
            d.osde_subtipoprod as productSubType,
            d.cod_sap as productSAPId,
            d.id_producto as productId,
            d.id_tipodespacho as dispatchTypeId,
            d.osde_instalacion as install,
            d.osde_cantidad as quantity,
            d.osde_precio as price,
            d.osde_preciocosto as cost,
            d.osde_fecha_entrega as deliveryDate,
            d.ind_dec as allowDecimals,
            p.des_corta as productDescription,
            p.peso as productWeight,
            t.nombre as dispatchTypeDescription
            FROM os_detalle d
                LEFT JOIN productos p ON d.id_producto = p.id_producto
                LEFT JOIN tipos_despacho t ON d.id_tipodespacho = t.id_tipodespacho
            WHERE d.id_os = $id
            ORDER BY d.id_os_detalle");
        foreach($rs as $row) {
            $item = new QuotationItem();
            foreach ($row as $k => $v) {
                $item->$k = $v;
            }
            $quotation->addItem($item);
        }
        return $quotation;
    }

    public static function AddFreightItems($quotationId, $storeId, $freights, $deliveryDate) {
        require_once includePath('dao/ProductDAO.php');
        $conn = UDataSource::GetConnection();
        $osdeSQL = $conn->Prepare("INSERT INTO os_detalle (id_origen,id_tipodespacho,
            id_producto,id_os,osde_tipoprod,osde_subtipoprod,osde_instalacion,
            osde_precio,osde_cantidad,osde_descuento,osde_preciocosto,cod_sap,
            osde_descripcion,cod_barra,ind_dec,osde_fecha_entrega)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $count = 0;
        foreach ($freights as $freight) {
            $product = ProductDAO::FindProduct($freight['codSAP'], 'SAP', $storeId);
            if ($product) {
                $conn->Execute($osdeSQL, array(
                    4, // id_origen = 4 ???
                    1, // domicilio programado
                    $product[0]['id'],
                    $quotationId,
                    $product[0]['type'],
                    $product[0]['subType'],
                    0, // install = false
                    $freight['valorFlete'],
                    $freight['cantidad'],
                    0, // discount = 0
                    $product[0]['cost'],
                    $product[0]['sapId'],
                    '', // specs
                    $product[0]['barcode'],
                    $product[0]['allowDecimals'],
                    $deliveryDate
                ));
                $count++;
            }
        }

        $session = USession::GetInstance();
        $psConfig = $session->psConfig;
        $sql = "update osexth
            set min_ = round((
                select sum(d.osde_precio * d.osde_cantidad)
                from os_detalle d
                where d.id_os = $quotationId) * $psConfig->PLAN_SEPARE_CINICIAL),
            total_ = round((
                select sum(d.osde_precio * d.osde_cantidad)
                from os_detalle d
                where d.id_os = $quotationId))
            where id_ = $quotationId";
        $conn->Execute($sql);
        
        return $count == count($freights);
    }

    public static function GetIVA($quotationId) {
        $quotationId = intval($quotationId);
        $sql = "select
            round(x.tax_rate,2) as tax_rate,
            round(x.total / (1 + x.tax_rate/100)) as base,
            x.total - round(x.total / (1 + x.tax_rate/100)) as tax
            from (
                select
                    pr.iva as tax_rate,
                    sum(d.osde_precio * d.osde_cantidad) as total
                from os_detalle d
                left join os h on d.id_os = h.id_os
                left join productos p on d.id_producto = p.id_producto
                left join precios pr on p.cod_prod1 = pr.cod_prod1 and pr.id_local = h.id_local
                where d.id_os = $quotationId
                group by pr.iva
            ) x";
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute($sql);
        return $rs->GetRows();
    }

}
?>