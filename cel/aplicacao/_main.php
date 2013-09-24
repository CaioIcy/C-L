<?php
session_start();

include("funcoes_genericas.php");

check_user_authentication("index.php"); // Checks if the user was authenticated
?>

<html>
    <head>
        <script language="javascript1.3">

            /* 
             * Functions that will be used when the script is called
             * through himself or the tree
             */
            function reload_URL(URL) {
                document.location.replace(URL);
            }

            function changes_scene(cenario) {
                var url = 'alt_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function removes_scene(cenario) {
                var url = 'remove_scenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function changes_lexicon(lexico) {
                var url = 'alt_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function removes_lexicon(lexico) {
                var url = 'remove_lexicon.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            /*
             * Functions that will be used when the script
             * is called through heading.php
             */
            function request_scene() {
                var url = 'ver_pedido_cenario.php?id_projeto=' + '<?= $id_project ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function request_lexicon() {
                var url = 'ver_pedido_lexico.php?id_projeto=' + '<?= $id_project ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function add_user() {
                var url = 'add_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=270,width=490,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function relUsuario() {
                var url = 'rel_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function generates_XML() {
                var url = 'xml_gerador.php?id_projeto=' + '<?= $id_project ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }
        </script>
        <script type="text/javascript" src="mtmtrack.js">
        </script>
    </head>
    <body>

        <?php
        include("frame_inferior.php");

        if (isset($id) && isset($t)) {      // script called by main.php (or the tree)
            if ($t == "c") {
                ?>

                <h3>Information about the scenario</h3>

                <?php
            } else {
                ?>

                <h3>Information about the lexicon</h3>

                <?php
            }
            ?>

            <table>

                <?php
                $database_connection = database_connect() or die("Erro ao conectar ao SGBD");

                if ($t == "c") {        // if it's a scenario
                    $query = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, episodios
              FROM cenario
              WHERE id_cenario = $id";
                    $query_r = mysql_query($query) or die("Error while sending selection query");
                    $result = mysql_fetch_array($query_r);
                    ?>

                    <tr>
                        <td>Title:</td><td><?= $result['titulo'] ?></td>
                    </tr>
                    <tr>
                        <td>Objective:</td><td><?= $result['objetivo'] ?></td>
                    </tr>
                    <tr>
                        <td>Context:</td><td><?= $result['contexto'] ?></td>
                    </tr>
                    <tr>
                        <td>Actors:</td><td><?= $result['atores'] ?></td>
                    </tr>
                    <tr>
                        <td>Resources:</td><td><?= $result['recursos'] ?></td>
                    </tr>
                    <tr>
                        <td>Episodes:</td><td><?= $result['episodios'] ?></td>
                    </tr>
                    <tr>
                        <td height="40" valign="bottom">
                            <a href="#" onClick="changes_scene(<?= $result['id_cenario'] ?>);">Change scenario</a>
                        </td>
                        <td valign="bottom">
                            <a href="#" onClick="removes_scene(<?= $result['id_cenario'] ?>);">Remove scenario</a>
                        </td>
                    </tr>

                    <?php
                } else {
                    $query = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_lexico = $id";
                    $query_r = mysql_query($query) or die("Error while sending seleciton query");
                    $result = mysql_fetch_array($query_r);
                    ?>

                    <tr>
                        <td>Name:</td><td><?= $result['nome'] ?></td>
                    </tr>
                    <tr>
                        <td>Notion:</td><td><?= $result['nocao'] ?></td>
                    </tr>
                    <tr>
                        <td>Impact:</td><td><?= $result['impacto'] ?></td>
                    </tr>
                    <tr>
                        <td height="40" valign="bottom">
                            <a href="#" onClick="changes_lexicon(<?= $result['id_lexico'] ?>);">Change lexicon</a>
                        </td>
                        <td valign="bottom">
                            <a href="#" onClick="removes_lexicon(<?= $result['id_lexico'] ?>);">Remove lexicon</a>
                        </td>
                    </tr>

                    <?php
                }
                ?>

            </table>
            <br>
            <br>
            <br>

            <?php
            if ($t == "c") {
                ?>

                <h3>Scenarios that reference this scenario</h3>

                <?php
            } else {
                ?>

                <h3>Scenarios and terms of the lexicon that reference this term</h3>

                <?php
            }

            frame_inferior($database_connection, $t, $id);
        } elseif (isset($id_project)) {//script called by heading.php

            /*
             * A variable &id_project was passed. This variable must contain the ID of
             * a project that the user is registrered in. However, if the passage is
             * made using JavaScript (in heading.php), we should check if this ID is
             * really corresponding to a project that the user has access to
             */
            check_proj_perm($_SESSION['id_usuario_corrente'], $id_project) or die("Permission denied");

            // Sets a session variable corresponding to the current project
            $_SESSION['id_projeto_corrente'] = $id_project;
            ?>

            <table>
                <tr>
                    <td>Project:</td>
                    <td><?= simple_query("nome", "projeto", "id_projeto = $id_project") ?></td>
                </tr>
                <tr>
                    <td>Date created:</td>
                    <td><?= simple_query("TO_CHAR(data_criacao, 'DD/MM/YY')", "projeto", "id_projeto = $id_project") ?></td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td><?= simple_query("descricao", "projeto", "id_projeto = $id_project") ?></td>
                </tr>
            </table>

            <?php
            // Checks if the user is this projects administrator
            if (is_admin($_SESSION['id_usuario_corrente'], $id_project)) {
                ?>

                <br>
                <p><b>You are an administrator of this project</b></p>
                <p><a href="#" onClick="request_scene();">Check scenarios alteration requests</a></p>
                <p><a href="#" onClick="request_lexicon();">Check lexicon terms alteration requests</a></p>
                <p><a href="#" onClick="add_user();">Add a user (not yet existing) in this project</a></p>
                <p><a href="#" onClick="relUsuario();">Relate users (already existing) to this project</a></p>
                <p><a href="#" onClick="generates_XML();">Generate XML of this project</a></p>

                <?php
            }
        } else {        // script called by index.php
            ?>

            <p>Select a project above, or create a new one.</p>

            <?php
        }
        ?>

    </body>
</html>
