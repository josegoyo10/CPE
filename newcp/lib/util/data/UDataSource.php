<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Provides access to the current database connections.
 *
 * @author Frank D. Martinez <mnesarco@gmail.com>
 */
class UDataSource {

    public static function GetConnection($name = 'default') {

        global $APP_CONNECTIONS, $APP_DATASOURCES;

        if (!isset($APP_CONNECTIONS)) {
            $APP_CONNECTIONS = array();
        }

        if (!isset($APP_CONNECTIONS[$name])) {
            $ds = $APP_DATASOURCES[$name];
            $connection =& NewADOConnection($ds['driver']);
            if (isset($ds['persistent']) && $ds['persistent'] === true) {
                $connection->PConnect(
                    $ds['host'], $ds['username'], $ds['password'], $ds['dbname']);
            }
            else {
                $connection->Connect(
                    $ds['host'], $ds['username'], $ds['password'], $ds['dbname']);
            }
            $connection->SetFetchMode(ADODB_FETCH_ASSOC);
            $APP_CONNECTIONS[$name] =& $connection;
        }

        return $APP_CONNECTIONS[$name];
    }

}
?>
