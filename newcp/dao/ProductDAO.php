<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of ProductDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class ProductDAO {

    public static function searchProduct1($criteria, $filter, $storeId, $limit = 20, $types = array('PS','PE')) {
        $conn = UDataSource::GetConnection();
        $custSQL = '';
        switch ($criteria) {
            case 'sap':
                $custSQL = ' AND p.cod_prod1 = ' . $conn->qstr($filter) . ' ORDER BY p.cod_prod1';
                break;
            case 'upc':
                $custSQL = ' AND cb.cod_barra = ' . $conn->qstr($filter) . ' ORDER BY cb.cod_barra';
                break;
            case 'description':
            $custSQL = ' AND p.des_corta LIKE \'%' . str_replace("'", "\\'", $filter) .
                '%\' ORDER BY p.des_corta';
                break;
            default:
                return array();
        }
        $storeId = intval($storeId);
        $limit = intval($limit);
        if ($limit <= 0) $limit = 20;
        $qtypes = array();
        foreach ($types as $t) { $qtypes[] = $conn->qstr($t); }
        $qtypes = implode(',', $qtypes);
        $sql = "SELECT distinct
            p.id_producto as id,
            p.cod_prod1 as sap,
            des_corta as description,
            pre.prec_valor as sellPrice
            FROM productos p
            JOIN precios pre
                ON pre.id_producto = p.id_producto
                AND id_local = $storeId
            LEFT JOIN codbarra cb
                ON cb.id_producto = p.id_producto
            WHERE prod_tipo IN ($qtypes) $custSQL
            LIMIT 0, $limit";
        $rs = $conn->Execute($sql);
        return $rs->GetRows();
    }

    public static function searchProductByProvider($criteria, $filter, $storeId, $limit = 20, $types = array('PS','PE')) {
        $conn = UDataSource::GetConnection();
        $custSQL = '';
        switch ($criteria) {
            case 'name':
                $custSQL = ' AND prov.nom_prov LIKE ' . $conn->qstr("%$filter%") . ' ORDER BY p.des_corta';
                break;
            case 'id':
                $custSQL = ' AND prov.cod_prov = ' . $conn->qstr($filter) . ' ORDER BY p.des_corta';
                break;
            default:
                return array();
        }
        $storeId = intval($storeId);
        $limit = intval($limit);
        if ($limit <= 0) $limit = 20;
        $qtypes = array();
        foreach ($types as $t) { $qtypes[] = $conn->qstr($t); }
        $qtypes = implode(',', $qtypes);
        $sql = "SELECT distinct
            p.id_producto as id,
            p.cod_prod1 as sap,
            des_corta as description,
            pre.prec_valor as sellPrice
            FROM productos p
            JOIN precios pre
                ON pre.id_producto = p.id_producto
                AND id_local = $storeId
            LEFT JOIN codbarra cb
                ON cb.id_producto = p.id_producto
            LEFT JOIN prodxprov pxp
                ON p.id_producto = pxp.id_producto
            LEFT JOIN proveedores prov
                ON pxp.id_proveedor = prov.id_proveedor
            WHERE prod_tipo IN ($qtypes) $custSQL
            LIMIT 0, $limit";
        $rs = $conn->Execute($sql);
        return $rs->GetRows();
    }


    public static function FindProduct($code, $type, $storeId) {
        $conn = UDataSource::GetConnection();
        $code = trim($code);
        $storeId = intval($storeId);
        if ($type == "UPC") { //cod_barra//UPC//EAN
            $query = "SELECT
                prod.id_producto as id,
                prod.cod_prod1 as sapId,
                prod.des_corta as description,
                prod.prod_tipo as type,
                prod.prod_subtipo as subType,
                if (prod.prod_tipo = 'PE' and prod.prod_subtipo = 'GE', 0, prec.prec_valor) as price,
                prec.prec_costo as cost,
                cb.cod_barra as barcode,
                cb.unid_med as unit,
                tst.precio_req as requiredPrice,
                tst.precio_mod as editablePrice,
                um.ind_decimal as allowDecimals
                FROM codbarra cb
                    LEFT JOIN productos prod ON prod.cod_prod1 = cb.cod_prod1
                    LEFT JOIN precios prec ON prec.cod_prod1 = prod.cod_prod1 AND prec.id_local = $storeId
                    LEFT JOIN unidmed um ON cb.unid_med = um.unid_med
                    INNER JOIN tipo_subtipo tst ON tst.prod_tipo = prod.prod_tipo AND tst.prod_subtipo = prod.prod_subtipo
                    WHERE cb.unid_med not in ('PAL','TON','T') and cb.cod_barra = " . $conn->qstr($code);
        }
        else { //SKU//SAP//COD_PROD1
            $query = "SELECT
                prod.id_producto as id,
                prod.cod_prod1 as sapId,
                prod.des_corta as description,
                prod.prod_tipo as type,
                prod.prod_subtipo as subType,
                if (prod.prod_tipo = 'PE' and prod.prod_subtipo = 'GE', 0, prec.prec_valor) as price,
                prec.prec_costo as cost,
                cb.cod_barra as barcode,
                cb.unid_med as unit,
                tst.precio_req as requiredPrice,
                tst.precio_mod as editablePrice,
                um.ind_decimal as allowDecimals
                FROM productos prod
                    LEFT JOIN codbarra cb ON prod.cod_prod1 = cb.cod_prod1
                    LEFT JOIN unidmed um ON cb.unid_med = um.unid_med
                    LEFT JOIN precios prec ON prec.cod_prod1 = prod.cod_prod1 AND prec.id_local = $storeId
                    INNER JOIN tipo_subtipo tst ON tst.prod_tipo = prod.prod_tipo AND tst.prod_subtipo = prod.prod_subtipo
                    WHERE  cb.unid_med not in ('PAL','TON','T') and prod.cod_prod1 = " . $conn->qstr($code) . "
                    ORDER BY cb.cod_ppal desc, cb.unid_med";
        }
        $rs = $conn->Execute($query);
        return $rs->GetRows();
    }
}
?>
