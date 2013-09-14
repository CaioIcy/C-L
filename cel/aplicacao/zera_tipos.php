<?php

include 'bd.inc';

$db_conection = bd_connect();


$query = "update lexico set tipo =  NULL;";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error());

mysql_close($db_conection);
?>
