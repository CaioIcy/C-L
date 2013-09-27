<?php

//include_once "CELConfig/CELConfig.inc";

function database_connect() {

    $ipValor = "127.0.0.1";
    $BDuser = "root@localhost";
    $BDpassword = "";
    $BDdatabase = "cel";

    $r = mysql_connect($ipValor, $BDuser, $BDpassword) or die("Erro na conex�o ao BD : " . mysql_error());

    if ($r && mysql_select_db($BDdatabase))
        return($r);
    else
        return(FALSE);
}

?>
