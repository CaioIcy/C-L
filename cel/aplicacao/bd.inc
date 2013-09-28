<?php

//include_once "CELConfig/CELConfig.inc";

function database_connect() {

    $result = false; //Represents the result of the connection to the database
    
    $ip_Value = "localhost"; //Ip used to connect to the database
    $user_db = "root"; //User name used to connect to the database
    $password_db = ""; //Password used to connect to the database
    $database = "cel"; //Database used to query

    $connection_serverMySQL = mysql_connect($ip_Value,$user_db,$password_db); //FALSE in case of connection failure
    $database_select = mysql_select_db($database); //Boolean
    
    // Indicates server MySQL connection
    if(!$connection_serverMySQL){
        
        die("Error! No connection to Database: ".mysql_error());
        $result = false;
        
    }else{
        
        // Indicates database selected
        if($database_select){
            $result = $connection_serverMySQL;
        }else{
            die("Error! Not connected to $database: ".mysql_error());
            $result = false;
        }
        
    }
    
    return ($result);
}

?>
