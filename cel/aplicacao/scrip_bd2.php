<html> 

    <head> 
        <title></title> 
    </head> 

    <body> 

        <?php
        include 'auxiliar_bd.php';
        include_once("bd.inc");
        include_once("CELConfig/CELConfig.inc");

        $database_conection = database_connect() or die("Erro na conexão à BD : " . mysql_error() . __LINE__);

        if ($database_conection && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXÃO À BD <br>";
        else
            echo "ERRO NA CONEXÃO À BD <br>";

        $query = "alter table conceito add namespace varchar(250) NULL after descricao;";
        $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

        echo "<br>FIM !!!";

        mysql_close($database_conection);
        ?> 

    </body> 

</html> 
