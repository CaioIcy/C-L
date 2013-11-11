<?php
/*
 * File: ver_pedido_relacao.php
 * 
 * This script shows the various requests concerning the relation.
 * The manager has the option to see the requests that were already validated.
 * The manager has a third option: remove the valiated request from the request list
 * The manager can respond to a request via e-mail, directly from this page.
 * 
 * Called by: heading.php
 */
session_start();

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';

check_user_authentication("index.php");
if (isset($submit)) {
    $DB = new PGDB ();
    $select = new QUERY($DB);
    $update = new QUERY($DB);
    $delete = new QUERY($DB);
    for ($counter = 0; $counter < sizeof($pedidos); $counter++) {
        $update->execute("update pedidorel set aprovado= 1 where id_pedido = $pedidos[$counter]");
        tratarPedidoRelacao($pedidos[$counter]);
    }
    for ($counter = 0; $counter < sizeof($remover); $counter++) {
        $delete->execute("delete from pedidorel where id_pedido = $remover[$counter]");
    }
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Operation was successful!</h4>
    <script language="javascript1.3">

        self.close();

    </script>

<?php } else {
    ?>
    <html>
        <head>
            <title>Relation alteration requests</title>
        </head>
        <body>
            <h2>Alteration requests in the group of relation</h2>
            <form action="?id_projeto=<?= $id_project ?>" method="post">

                <?php
                /*
                 * Scenario - Check concepts alteration requests
                 * Objective:   Allow the administrator to manage the concepts alteration requests.
                 * Context:     Manager wishes to visualize the concepts alteration requests
                 *              Pre-condition: Login, registered project
                 * Actors:      Administrator
                 * Resources:   System, database
                 * Episodes:    The administrator clicks in the 'Verify scenario alteration requests'
                 *              option.
                 *              Restriction: Only the administrator can have this function visible.
                 *              The system provides a screen where the administrator can visualize
                 *              the history of all the pending alterations.
                 *              For new scenario inclusion/alteration requests, the system allows
                 *              the administrator to APPROVE or REMOVE.
                 *              For already approved inclusion/alteration requests, the system
                 *              allows only the REMOVE option to the administrator.
                 *              To commit the approval/removal selections, just click in 'Process'
                 */

                $DB = new PGDB ();
                $select = new QUERY($DB);
                $select2 = new QUERY($DB);
                $select->execute("SELECT * FROM pedidorel WHERE id_projeto = $id_project");
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
                        $select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_user");
                        $usuario = $select2->gofirst();
                        if (strcasecmp($tipo_pedido, 'remover')) {
                            ?>

                            <br>
                            <h3>The user <a  href="mailto:<?= $usuario['email'] ?>" ><?= $usuario['nome'] ?></a> requests for <?= $tipo_pedido ?> the relation <font color="#ff0000"><?= $record['nome'] ?></font> <?
                                if (!strcasecmp($tipo_pedido, 'alterar')) {
                                    echo"to concept below:</h3>";
                                } else {
                                    echo"</h3>";
                                }
                                ?>
                                <table>
                                    <td><b>Name:</b></td>
                                    <td><?= $record['nome'] ?></td>
                                    <tr>
                                        <td><b>Justification:</b></td>
                                        <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                                    </tr>
                                </table>
                            <?php } else { ?>
                                <h3>The user <a  href="mailto:<?= $usuario['email'] ?>" ><?= $usuario['nome'] ?></a> requests for <?= $tipo_pedido ?> the relation <font color="#ff0000"><?= $record['nome'] ?></font></h3>
                                <?php
                            }
                            if ($aprovado == 1) {
                                echo "[<font color=\"#ff0000\"><STRONG>Approved</STRONG></font>]<BR>";
                            } else {
                                echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Approve</STRONG>]<BR>  ";
//                 echo "Rejeitar<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\">" ;
                            }
                            echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\"> <STRONG>Remove from list</STRONG>]";
                            print( "<br>\n<hr color=\"#000000\"><br>\n");
                            $record = $select->gonext();
                        }
                    }
                    ?>
                    <input name="submit" type="submit" value="Processar">
                    </form>
                    <br><i><a href="showSource.php?file=ver_pedido_cenario.php">See the source code!</a></i>
                    </body>
                    </html>
                    <?php
                }
                ?>
