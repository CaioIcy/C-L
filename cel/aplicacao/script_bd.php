<html>

    <head>
        <title></title>
    </head>

    <body>

        <?php
        
        include_once 'bd.inc';
        include_once 'CELConfig/CELConfig.inc';

        $database_conection = database_connect() or die("Erro na conexão à BD : " . mysql_error() . __LINE__);
        if ($database_conection && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXÃO À BD <br>";
        else
            echo "ERRO NA CONEXÃO À BD <br>";

// query para criar tabela de conceitos. __JERONIMO__
        $query = "create table conceito (id_conceito int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        descricao varchar(250) not null,
										pai int(11),
                                        unique key(nome),
                                        primary key(id_conceito)
                                        );";
        $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

        $query = "create table relacao_conceito (id_conceito int(11) not null,
                                        id_relacao int(11) not null,
                                        predicado varchar(250) not null
                                        );";
        $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

        $query = "create table relacao (id_relacao int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_relacao)
                                        );";
        $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

        $query = "create table axioma (id_axioma int(11) not null AUTO_INCREMENT,
                                        axioma varchar(250) not null ,
                                        unique key(axioma),
                                        primary key(id_axioma)
                                        );";
        $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

        $query = "create table algoritmo (id_variavel int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
										valor varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_variavel)
                                        );";
        $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

        mysql_close($database_conection);
        ?>

    </body>

</html>
