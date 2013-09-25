<?php

include_once "CELConfig/CELConfig.inc";

function database_connect() {

    $ipValor = "localhost";
    $BDuser = "";
    $BDpassword = "";
    $BDdatabase = "test_holding";

    $r = mysql_connect($ipValor, $BDuser, $BDpassword) or die("Erro na conex�o ao BD : " . mysql_error());

    if ($r && mysql_select_db($BDdatabase))
        return($r);
    else
        return(FALSE);
}

?>
