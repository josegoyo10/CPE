<?php
/*
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
defined('_APP_BOOTSTRAP_') or die('Bootstrap Exception: Access denied.');

/**
 * Application datasources configuration.
 */
$APP_DATASOURCES = array(
    'default' => array(
        'host'       => 'localhost',
        'port'       => '3306',
        'driver'     => 'mysql',
        'dbname'     => 'cpprod',
        'username'   => 'root',
        'password'   => '3des*.',
        'persistent' => false
    )
);

?>
