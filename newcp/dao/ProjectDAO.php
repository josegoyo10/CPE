<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of ProjectDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class ProjectDAO {

    public static function GetProjectsByCustomer($customerId) {
        $conn = UDataSource::GetConnection();
        $rs = $conn->Execute('SELECT
            p.id_proyecto AS id,
            p.proy_nombre AS description
            FROM proyectos p
            WHERE p.clie_rut = ' . intval($customerId) .
            ' ORDER BY p.id_proyecto');
        return $rs->GetRows();
    }

    public static function CreateProject($customerId, $projectName) {
        $conn = UDataSource::GetConnection();
        $customerId = intval($customerId);
        $projectName = $conn->qstr(trim(strtoupper($projectName)));
        $rs = $conn->Execute("SELECT id_proyecto FROM proyectos
            WHERE clie_rut=$customerId AND upper(proy_nombre)=$projectName");
        $newId = null;
        if ($rs->EOF) {
            $conn->Execute("INSERT INTO proyectos (clie_rut,proy_nombre)
                VALUES ($customerId,$projectName)");
            $newId = $conn->Insert_ID();
        }
        else {
            $newId = $rs->fields['id_proyecto'];
        }
        $rs->Close();
        return $newId;
    }

}
?>
