<?php

function database_connect() {

    $result = false; //Represents the result of the connection to the database

    $ip_Value = "localhost"; //Ip used to connect to the database
    $user_db = "root"; //User name used to connect to the database
    $password_db = ""; //Password used to connect to the database
    $database = "cel"; //Database used to query


    try {
        $database_select = mysql_select_db($database);
        $connection_serverMySQL = mysql_connect($ip_Value, $user_db, $password_db);       
    } catch (Exception $e) {
        echo $e->getMessage();
        die;
    }
}

?>
