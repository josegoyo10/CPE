<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Description of ConfigDAO
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class ConfigDAO {

    public static function GetGroupConfiguration($group = CONFIG_GROUP) {
        $config = new stdClass;
        $conn = UDataSource::GetConnection();
        $group = intval($group);
        $rs = $conn->Execute("SELECT VAR_LLAVE as varKey, VAR_VALOR as value
            FROM glo_variables WHERE VAR_GLO_ID = $group");
        foreach ($rs as $row) {
            $config->$row['varKey'] = $row['value'];
        }
        return $config;
    }

}
?>
