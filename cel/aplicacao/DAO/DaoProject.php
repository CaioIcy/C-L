<?php

require_once '/../bd.inc';

function projectNameDatabase($projectName){
    
    database_connect();
    
    $query = "SELECT * FROM projeto WHERE nome = '$projectName'";
    $executeQuery = mysql_query($query);
    $resultArray = mysql_fetch_array($executeQuery);
    
    return $resultArray;
}

function projectIdDatabase($projectId,$userId){
    
    database_connect();
    
    $query = "SELECT * FROM participa WHERE id_projeto = '$projectId' AND id_usuario = '$userId'";
    $executeQuery = mysql_query($query);
    $resultArray = mysql_fetch_array($executeQuery);
    
    return $resultArray;
    
}
?>
