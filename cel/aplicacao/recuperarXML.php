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
        <title>Recover XML log</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

    <?php
    /*
     * Scenario - Generate XML logs
     * Objective:   Allow the administrator to generate XML logs of a project, identified by date
     * Context:     Manager wishes to generate a log for one of the projects, on which he is an administrator.
     *              Pre-condition: Manager is logged in, project is registered
     * Actors:      Administrator
     * Resources:   System, log data, registered project data, database
     * Episodes:    1- Recover the data from the database, and parse them to XML.
     *              2- Display a XSL
     */

    $database_recuperation = database_connect() or die("Error while connecting to the database");
    if (isset($erase)) {
        if ($erase) {
            $query_erase = "DELETE FROM publicacao WHERE id_projeto = '$id_project' AND versao = '$version' ";
            $query_r_erase = mysql_query($query_erase);
        }
    }
    $query_database_command = "SELECT * FROM publicacao WHERE id_projeto = '$id_project'";
    $query_connecting_database = mysql_query($query_database_command) or die("Error while sending the query");
    ?>
    <h2>Recover XML/XSL</h2><br>
    <?php
    while ($result = mysql_fetch_row($query_connecting_database)) {
        $data = $result[1];
        $version = $result[2];
        $XML = $result[3];
        ?>
        <table>
            <tr>
                <th>Version:</th><td><?= $version ?></td>
                <th>Data:</th><td><?= $data ?></td>
                <th><a href="mostraXML.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>">XML</a></th>
                <th><a href="recuperarXML.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>&apaga=true">Erases XML</a></th>
            </tr>
        </table>

        <?php
    }
    ?>

    <br><i><a href="showSource.php?file=recuperarXML.php">See the source code!</a></i>

</body>

</html>
