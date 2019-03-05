<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of CachedListsDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class CachedListsDAO {

    public static function GetGenders() {
        $session = USession::GetInstance();
        if (!isset($session->CACHE_LIST_GENDERS)) {
            $conn = UDataSource::GetConnection();
            $rs = $conn->Execute('SELECT
                id_sexo_cliente as id, sexo_cliente as description
                FROM sexo_cliente order by id_sexo_cliente');
            $session->CACHE_LIST_GENDERS = $rs->GetRows();
            $rs->Close();
        }
        return $session->CACHE_LIST_GENDERS;
    }

    public static function GetCustomerTypes() {
        $session = USession::GetInstance();
        if (!isset($session->CACHE_LIST_CUSTTYPE)) {
            $conn = UDataSource::GetConnection();
            $rs = $conn->Execute('SELECT
                id_tipo_cliente as id, nombre_tipo_cliente as description
                FROM tipo_cliente order by id_tipo_cliente');
            $session->CACHE_LIST_CUSTTYPE = $rs->GetRows();
            $rs->Close();
        }
        return $session->CACHE_LIST_CUSTTYPE;
    }

    public static function GetCustomerCategories() {
        $session = USession::GetInstance();
        if (!isset($session->CACHE_LIST_CUSTCAT)) {
            $conn = UDataSource::GetConnection();
            $rs = $conn->Execute('SELECT
                id_categoria as id, nombre_categoria as description
                FROM tipo_categoria_cliente order by id_categoria');
            $session->CACHE_LIST_CUSTCAT = $rs->GetRows();
            $rs->Close();
        }
        return $session->CACHE_LIST_CUSTCAT;
    }

    public static function GetTaxContributionTypes() {
        $session = USession::GetInstance();
        if (!isset($session->CACHE_LIST_TAXCTYPES)) {
            $conn = UDataSource::GetConnection();
            $rs = $conn->Execute('SELECT
                abreviatura_cal_tri as abbr, nombre_cal_tri as description
                FROM calidad_tributaria order by id_cal_tri');
            $session->CACHE_LIST_TAXCTYPES = $rs->GetRows();
            $rs->Close();
        }
        return $session->CACHE_LIST_TAXCTYPES;
    }

    public static function GetStates($type) {
        $session = USession::GetInstance();
        $cacheVarName = "CACHE_LIST_STATE_$type";
        if (!isset($session->$cacheVarName)) {
            $conn = UDataSource::GetConnection();
            $types = array();
            foreach($type as $v) {
                $types[] = $conn->qstr($v);
            }
            $type = implode(',', $types);
            $rs = $conn->Execute("SELECT
                id_estado as id, esta_nombre as description
                FROM estados
                WHERE esta_tipo in ($type)
                ORDER BY orden");
            $session->$cacheVarName = $rs->GetRows();
            $rs->Close();
        }
        return $session->$cacheVarName;
    }
    
}
?>
