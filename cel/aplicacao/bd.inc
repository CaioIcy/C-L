<?php

include_once 'CELConfig/CELConfig.inc';

function database_connect() {

    $result = FALSE; //Result of the action connecting to database

    $ip_Value = CELConfig_ReadVar("BD_ip"); //Ip used to connect to the database
    $user_db = CELConfig_ReadVar("BD_user"); //User name used to connect to the database
    $password_db = CELConfig_ReadVar("BD_password"); //Password used to connect to the database
    $database = CELConfig_ReadVar("BD_database"); //Database used to query

    $connection_serverMySQL = mysql_connect($ip_Value, $user_db, $password_db) or die("ERROR! Database connection : " . mysql_error());
    $database_select = mysql_select_db($database) or die("ERROR! Table connection : " . mysql_error());

    if ($connection_serverMySQL && $database_select) {
        $result = $connection_serverMySQL;
    } else {
        $result = FALSE;
    }

    return $result;
}

?>
