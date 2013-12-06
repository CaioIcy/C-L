<?php
/*
 * File: ver_pedido_lexico.php
 * 
 * This script shows the various requests concerning the lexicon.
 * The manager has the option to see the requests that were already validated.
 * The manager has a third option: remove the valiated request from the request list
 * The manager can respond to a request via e-mail, directly from this page.
 * 
 * Called by: heading.php
 */

session_start();

include_once '/../funcoes_genericas.php';
include_once '/../httprequest.inc';

check_user_authentication("index.php");

if (isset($submit)) {

    $DB = new PGDB ();
    $select = new QUERY($DB);
    $update = new QUERY($DB);
    $delete = new QUERY($DB);
    for ($counter = 0; $counter < sizeof($pedidos); $counter++) {
        $update->execute("UPDATE pedidolex SET aprovado= 1 WHERE id_pedido = " . $pedidos[$counter] . "");
        tratarPedidoLexico($pedidos[$counter]);
    }
    for ($counter = 0; $counter < sizeof($remover); $counter++) {
        $delete->execute("DELETE FROM pedidolex WHERE id_pedido  = " . $remover[$counter] . "");
        $delete->execute("DELETE FROM sinonimo WHERE id_pedidolex = " . $remover[$counter] . "");
    }
    ?>

    <script language="javascript1.2">
        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace("main.php");
    </script>
    <h4>Operation was successful!</h4>
    <script language="javascript1.2">
        self.close();
    </script>
    <?php
} else {
    
    $id_project = $_REQUEST['id_projeto'];
    ?>
    <html>
        <head>
            <title>Lexicon requests</title>
        </head>
        <body>
            <h2>Lexicon alteration requests</h2>
            <form action="?id_projeto=<?= $id_project ?>" method="post">

                <?php
                /*
                 * Scenario - Check lexicon terms alteration requests
                 * Objective:   Allow the administrator to manage the lexicon terms alteration requests.
                 * Context:     Manager wishes to visualize the lexicon terms alteration requests
                 *              Pre-condition: Login, registered project
                 * Actors:      Administrator
                 * Resources:   System, database
                 * Episodes:    1- The administrator clicks in the 'Verify lexicon terms
                 *              alteration requests' option.
                 *              Restriction: Only the administrator can have this function visible.
                 *              2- The system provides a screen where the administrator can visualize
                 *              the history of all the pending alterations.
                 *              3- For new lexicon terms inclusion/alteration requests, the system allows
                 *              the administrator to APPROVE or REMOVE.
                 *              4- For already approved inclusion/alteration requests, the system
                 *              allows only the REMOVE option to the administrator.
                 *              5- To commit the approval/removal selections, just click in 'Process'
                 */
                $DB = new PGDB ();
                $select = new QUERY($DB);
                $select2 = new QUERY($DB);
                $select3 = new QUERY($DB);
                $select->execute("SELECT * FROM pedidolex WHERE id_projeto = " . $id_project . "");
                if ($select->getntuples() == 0) {
                    echo "<BR>No requests.<BR>";
                } else {
                    $i = 0;
                    $record = $select->gofirst();
                    while ($record != 'LAST_RECORD_REACHED') {
                        $id_user = $record['id_usuario'];
                        $id_pedido = $record['id_pedido'];
                        $tipo_pedido = $record['tipo_pedido'];
                        $aprovado = $record['aprovado'];

                        //Get the synonyms
                        $select3->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = " . $id_pedido . "");

                        $select2->execute("SELECT * FROM usuario WHERE id_usuario = " . $id_user . "");
                        $usuario = $select2->gofirst();
                        if (strcasecmp($tipo_pedido, 'remover')) {
                            ?>
                            <h3>The user <a  href="mailto:<?= $usuario['email'] ?>" ><?= $usuario['nome'] ?></a> requests for <?= $tipo_pedido ?> the lexicon <font color="#ff0000"><?= $record['nome'] ?></font> <?
                                if (!strcasecmp($tipo_pedido, 'alterar')) {
                                    echo"for the lexicon below:</h3>";
                                } else {
                                    echo"</h3>";
                                }
                                ?>
                                <table>
                                    <td><b>Name:</b></td>
                                    <td><?= $record['nome'] ?></td>

                                    <tr>

                                        <td><b>Notion:</b></td>
                                        <td><?= $record['nocao'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Impact:</b></td>
                                        <td><?= $record['impacto'] ?></td>
                                    </tr>


                                    <tr>
                                        <td><b>Synonyms:</b></td>
                                        <td>
                                            <?php
                                            $sinonimo = $select3->gofirst();
                                            $strSinonimos = "";
                                            while ($sinonimo != 'LAST_RECORD_REACHED') {
                                                $strSinonimos = $strSinonimos . $sinonimo["nome"] . ", ";
                                                $sinonimo = $select3->gonext();
                                            }

                                            echo(substr($strSinonimos, 0, strrpos($strSinonimos, ",")));
                                            ?>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td><b>Justification:</b></td>
                                        <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                                    </tr>
                                </table>
                            <?php } else {
                                ?>
                                <h3>The user <a  href="mailto:<?= $usuario['email'] ?>" ><?= $usuario['nome'] ?></a> requests for <?= $tipo_pedido ?> the lexicon <font color="#ff0000"><?= $record['nome'] ?></font></h3>
                                <?php
                            }
                            if ($aprovado == 1) {
                                echo "[<font color=\"#ff0000\"><STRONG>Approved</STRONG></font>]<BR>";
                            } else {
                                echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"" . $id_pedido . "\"> <STRONG>Approve</STRONG>]<BR>  ";
//                     echo "Rejeitar<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\">" ;
                            }
                            echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"" . $id_pedido . "\"> <STRONG>Remove from list</STRONG>]";
                            print( "<br>\n<hr color=\"#000000\"><br>\n");
                            $record = $select->gonext();
                        }
                    }
                    ?>
                    <input name="submit" type="submit" value="Processar">
                    </form>
                    <br><i><a href="showSource.php?file=ver_pedido_lexico.php">See the source code!</a></i>
                    </body>
                    </html>

                    <?php
                }
                ?>


