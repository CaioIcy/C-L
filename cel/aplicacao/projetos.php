<?php
include_once 'funcoes_genericas.php';
?>
<html>

    <head>
    <p style="color: red; font-weight: bold; text-align: center">
        <img src="Images/Logo_CEL.jpg" width="180" height="100"><br/><br/>
        Published Projects</p>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

    <?php
    $database_recuperation = database_connect() or die("Error while connecting to the database");

    /*
     * Scenario - Choose project
     * Objective:   Allow the user/administrator to choose a project.
     * Context:     The user/administrator wisehs to choose a project.
     *              Pre-conditions: logged in, be registered as an administrator
     * Actors:      Administrator, user
     * Resources:   Registered users
     * Episodes:    1- User selects a project from the project list
     *              2- If the user selected a project that he administrates, see
     *                  ADMINISTRATOR CHOOSES PROJECT
     *              3- Else, see USER CHOOSES PROJECT
     */

    $query_database_command = "SELECT * FROM publicacao";
    $query_connecting_database = mysql_query($query_database_command) or die("Error while sending the search query");
    ?>

    <?php
    while ($result = mysql_fetch_row($query_connecting_database)) {
        $id_project = $result[0];
        $data = $result[1];
        $version = $result[2];
        $XML = $result[3];

        $query_search_project_name = "SELECT * FROM projeto WHERE id_projeto = '$id_project'";
        $query_search_result = mysql_query($query_search_project_name) or die("Error while sending the search project query");
        $result_name = mysql_fetch_row($query_search_result);
        $project_name = $result_name[1];
        ?>
        <table border='0'>
            <tr>
                <th height="29" width="140"><a href="mostrarProjeto.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>"><?= $project_name ?></a></th>
                <th height="29" width="140">Date: <?= $data ?></th>
                <th height="29" width="100">Version: <?= $version ?></th>
            </tr>
        </table>
        <?php
    }
    ?>

</body>
</html>
