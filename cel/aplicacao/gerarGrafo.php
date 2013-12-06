<?php
session_start();

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';

check_user_authentication("index.php");

$XML = "";
?>
<html>
    <body>
    <head>
        <title>Gerar Grafo</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

    <?php
//Cen�rio -  Gerar Grafo 
//Objetivo:   Permitir ao administrador gerar o grafo de um projeto
//Contexto:   Gerente deseja gerar um grafo para uma das vers�es de XML
//Atores:     Administrador
//Recursos:   Sistema, XML, dados cadastrados do projeto, banco de dados.
//Epis�dios:  Restri��o: Possuir um XML gerado do projeto

    $id_project = $_REQUEST['id_projeto'];
    $database_recuperation = database_connect() or die("Erro ao conectar ao SGBD");
    $query_database_command = "SELECT * FROM publicacao WHERE id_projeto = '$id_project'";
    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query");
    ?>
    <h2>Gerar Grafo</h2><br>
    <?php
    while ($result = mysql_fetch_row($query_connecting_database)) {
        $data = $result[1];
        $version = $result[2];
        $XML = $result[3];
        ?>
        <table>
            <tr>
                <th>Vers�o:</th><td><?= $version ?></td>
                <th>Data:</th><td><?= $data ?></td>
                <th><a href="mostraXML.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>">XML</a></th>
                <th><a href="grafo\mostraGrafo.php?versao=<?= $version ?>&id_projeto=<?= $id_project ?>">Gerar Grafo</a></th>

            </tr>
        </table>

        <?php
    }
    ?>

    <br><i><a href="showSource.php?file=recuperarXML.php">Veja o c�digo fonte!</a></i>

</body>

</html>
