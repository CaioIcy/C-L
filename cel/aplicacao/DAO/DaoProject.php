<?php

require_once '../bd.inc';

function projectDatabase($projectName){
    
    database_connect();
    
    $query = "SELECT * FROM projeto WHERE nome = '$projectName'";
    $result = mysql_query($query);
    $resultArray = mysql_fetch_array($result);
    
    return $resultArray;
}
?>
