<?php

include_once 'funcoes_genericas.php';

?>
<html>

    <head>
    <p style="color: red; font-weight: bold; text-align: center">
        <img src="Images/Logo_CEL.jpg" width="180" height="100"><br/><br/>
        Projetos Publicados</p>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

<?php
$database_recuperation = database_connect() or die("Erro ao conectar ao SGBD");

//Cen�rio - Escolher Projeto
//Objetivo:       Permitir ao Administrador/Usu�rio escolher um projeto.
//Contexto:       O Administrador/Usu�rio deseja escolher um projeto.
//Pr�-Condi��es:  Login, Ser Administrador
//Atores:         Administrador, Usu�rio
//Recursos:       Usu�rios cadastrados
//Epis�dios:      Caso o Usuario selecione da lista de projetos um projeto da qual ele seja administrador,
//                ver ADMINISTRADOR ESCOLHE PROJETO.
//                Caso contr�rio, ver USU�RIO ESCOLHE PROJETO.

$query_database_command = "SELECT * FROM publicacao";
$query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query de busca");
?>

    <?php
    while ($result = mysql_fetch_row($query_connecting_database))  {
        $id_project = $result[0];
        $data = $result[1];
        $version = $result[2];
        $XML = $result[3];

        $query_search_project_name = "SELECT * FROM projeto WHERE id_projeto = '$id_project'";
        $query_r_search = mysql_query($query_search_project_name) or die("Erro ao enviar a query de busca de projeto");
        $result_nome = mysql_fetch_row($query_r_search);
        $project_name = $result_nome[1];
        ?>
        <table border='0'>

            <tr>

                <th height="29" width="140"><a href="mostrarProjeto.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>"><?= $project_name ?></a></th>
                <th height="29" width="140">Data: <?= $data ?></th>
                <th height="29" width="100">Vers�o: <?= $version ?></th>

            </tr>


        </table>

    <?php
}
?>


</body>

</html>