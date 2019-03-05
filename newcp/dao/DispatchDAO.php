<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */

/**
 * Description of DispatchDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class DispatchDAO {
    public static function GetDispatchTypes($ids = false) {
        $conn = UDataSource::GetConnection();
        $where = '';
        if (is_array($ids)) {
            $sids = array();
            foreach ($ids as $id) { $sids[] = intval($id); }
            $where = "WHERE id_tipodespacho IN (" . implode(',', $sids) . ")";
        }
        $rs = $conn->Execute("SELECT
            id_tipodespacho as id, nombre as description
            FROM tipos_despacho $where
            ORDER BY id_tipodespacho");
        $rows = $rs->GetRows();
        $rs->Close();
        return $rows;
    }
    public static function FindById($id) {
        $conn = UDataSource::GetConnection();
        $id = intval($id);
        $rs = $conn->Execute("SELECT
            id_tipodespacho as id, nombre as description
            FROM tipos_despacho
            WHERE id_tipodespacho = $id");
        if ($rs->EOF) return null;
        return $rs->FetchObj();
    }
}
?>
