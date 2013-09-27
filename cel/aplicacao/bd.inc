<?php

//include_once "CELConfig/CELConfig.inc";

function database_connect() {

    $ipValor = "localhost:3306";
    $BDuser = "root";
    $BDpassword = "";
    $BDdatabase = "cel";

    $r = mysql_connect($ipValor, $BDuser, $BDpassword) or die("Erro na conex�o ao BD : " . mysql_error());

    if ($r && mysql_select_db($BDdatabase))
        return($r);
    else
        return(FALSE);
}

?>
