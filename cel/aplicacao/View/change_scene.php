<?php
/* File: change_scene.php
 * Purpose: This  script make a request for modification of a
 * scene in the project
 */

// O usuario recebe um form com o cenario corrente (ou seja com seus campos preenchidos)
// e podera fazer	alteracoes em todos os campos menos no titulo.Ao final a tela principal
// retorna para a tela de inicio e a arvore e fechada.O form de alteracao tb e fechado.
// Arquivo chamador: main.php

session_start();

include_once '/../bd.inc';
include_once '/../funcoes_genericas.php';
include_once '/../httprequest.inc';

check_user_authentication("index.php"); // Checa se o usuario foi autenticado
// Conecta ao SGBD
$database_conection = database_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {       // Script chamado atraves do submit do formulario
    inserirPedidoAlterarCenario($_SESSION['id_projeto_corrente'], $id_cenario, $scene_title, $scene_goal, $scene_context, $scene_performer, $scene_resource, $scene_exception, $scene_episode, $justificativa, $_SESSION['id_usuario_corrente']);
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Opera��o efetuada com sucesso!</h4>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
} else { // Script chamado atraves do link no cenario corrente
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);

    $query_database_command = "SELECT * FROM cenario WHERE id_cenario = $id_cenario";
    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao executar a query");
    $result = mysql_fetch_array($query_connecting_database);

// Cen�rio -    Alterar Cen�rio 
//Objetivo:	    Permitir a altera��o de um cen�rio por um usu�rio
//Contexto:	    Usu�rio deseja alterar cen�rio previamente cadastrado
//              Pr�-Condi��o: Login, Cen�rio cadastrado no sistema
//Atores:	    Usu�rio
//Recursos:	    Sistema, dados cadastrados
//Excess�es:    O nome do cen�rio sendo alterado � modificado para o nome de um cen�rio j� existente.
//Epis�dios:	O sistema fornecer� para o usu�rio a mesma tela de INCLUIR CEN�RIO,
//              por�m com os seguintes dados do cen�rio a ser alterado preenchidos
//              e edit�veis nos seus respectivos campos: Objetivo, Contexto, Atores, Recursos e Epis�dios.
//              Os campos Projeto e T�tulo estar�o preenchidos, mas n�o edit�veis.
//              Ser� exibido um campo Justificativa para o usu�rio colocar uma
//              justificativa para a altera��o feita.
    ?>

    <html>
        <head>
            <title>Alterar Cen�rio</title>
        </head>
        <body>
            <h4>Alterar Cen�rio</h4>
            <br>
            <form action="?id_projeto=<?= $id_project ?>" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="48" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <input type="hidden" name="id_cenario" value="<?= $result['id_cenario'] ?>">
                    <td>T�tulo:</td>
                    <? $result['titulo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['titulo']); ?>
                    <input type="hidden" name="titulo" value="<?= $result['titulo'] ?>">
                    <td><input disabled maxlength="128" name="titulo2" size="48" type="text" value="<?= $result['titulo'] ?>"></td>
                    <tr>
                        <td>Objetivo:</td>
                        <? $result['objetivo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['objetivo']); ?>

                        <td><textarea name="objetivo" cols="48" rows="3"><?= $result['objetivo'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td>
                        <? $result['contexto'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['contexto']); ?>
                        <td><textarea name="contexto" cols="48" rows="3"><?= $result['contexto'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Atores:</td>
                        <? $result['atores'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['atores']); ?>

                        <td><textarea name="atores" cols="48" rows="3"><?= $result['atores'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td>
                        <? $result['recursos'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['recursos']); ?>

                        <td><textarea name="recursos" cols="48" rows="3"><?= $result['recursos'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Exce��o:</td>
                        <? $result['excecao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['excecao']); ?>

                        <td><textarea name="excecao" cols="48" rows="3"><?= $result['excecao'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Epis�dios:</td>
                        <? $result['episodios'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['episodios']); ?>
                        <td><textarea  cols="48" name="episodios" rows="5"><?= $result['episodios'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                        <td><textarea name="justificativa" cols="48" rows="2"></textarea></td>
                    </tr>

                    <tr>
                        <td colspan="2"><b><small>Essa justificativa � necess�ria apenas para aqueles usu�rios que n�o s�o administradores.</small></b></td>
                    </tr>

                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Alterar Cen�rio" onClick="updateOpener()"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=change_scene.php">Veja o c�digo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>
