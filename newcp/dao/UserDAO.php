<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of UserDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UserDAO {

    public static function FindById($id) {
        $conn = UDataSource::GetConnection();
        $id = intval($id);
        $rs = $conn->Execute("SELECT
            u.usr_id,
            u.usr_login,
            lu.id_local,
            l.nom_local,
            l.cod_local_pos,
            l.id_localizacion
            FROM usuarios u LEFT JOIN local_usr lu ON u.usr_id = lu.usr_id
            LEFT JOIN locales l ON lu.id_local = l.id_local
            WHERE u.usr_id = $id");
        if ($rs->EOF) {
            $rs->Close();
            return false;
        }
        else {
            $usr = new stdClass;
            $usr->id = $rs->fields['usr_id'];
            $usr->login = $rs->fields['usr_login'];
            $usr->storeId = $rs->fields['id_local'];
            $usr->storeIdPos = $rs->fields['cod_local_pos'];
            $usr->storeName = $rs->fields['nom_local'];
            $usr->storeLocationId = $rs->fields['id_localizacion'];
            $rs->Close();
            $moduleId = MODULE_ID;
            $rs = $conn->Execute("SELECT
                sum(p.pemo_insert) as p_insert,
                sum(p.pemo_update) as p_update,
                sum(p.pemo_delete) as p_delete,
                sum(p.pemo_select) as p_select
                FROM permisosxmodulo p
                WHERE p.pemo_mod_id = $moduleId
                AND (p.pemo_per_id = $usr->id OR
                p.pemo_per_id IN (SELECT pu.peus_per_id
                FROM perfilesxusuario pu WHERE pu.peus_usr_id = $usr->id))");
            if ($rs->EOF) {
                $rs->Close();
                return false;
            }
            else {
                $usr->canSelect = $rs->fields['p_select'] ? 1 : 0;
                $usr->canInsert = $rs->fields['p_insert'] ? 2 : 0;
                $usr->canUpdate = $rs->fields['p_update'] ? 4 : 0;
                $usr->canDelete = $rs->fields['p_delete'] ? 8 : 0;
                $usr->permissions = $usr->canSelect | $usr->canInsert | $usr->canUpdate | $usr->canDelete;
                $rs->Close();
                return $usr;
            }
        }
    }


}
?>
