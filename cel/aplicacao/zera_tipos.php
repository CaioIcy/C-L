<?php

include_once 'bd.inc';

$database_conection = database_connect();


$query_database_command = "update lexico set tipo =  NULL;";
$result = mysql_query($query_database_command) or die("A consulta à BD falhou : " . mysql_error());

mysql_close($database_conection);
?>
