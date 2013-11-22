<?php
/* File: adds_scene.php
 * Purpose: This script registers a new scene in the project
 */
session_start();


/*
 * This script registers a new scenario of the project.
 * A variable &id_project is recieved through a URL, and indicates in which
 * project the new scenario will be inserted in.
 */

include_once '/../bd.inc';
include_once '/../funcoes_genericas.php';
include_once '/../httprequest.inc';

check_user_authentication("index.php"); // Checks if the user was authenticated

if (!isset($success)) {
    $success = 'n';
} else {
    //do nothing
}

function checkIfScenarioExists($project, $title) {
    
    $result = false;

    $database = database_connect() or die("ERROR connection to SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $query = "SELECT * FROM cenario WHERE id_projeto = $project AND titulo = '$title' ";
    $query_project = mysql_query($query) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($query_project);
    if ($resultArray == false) {
        $result = true;
    }else{
        $result = false;
    }

    return $result;
    
}

$database_conection = database_connect() or die("Error while connecting to the SGBD");

if (isset($submit)) {
    $exists_scenario = checkIfScenarioExists($_SESSION['id_projeto_corrente'], $scene_title);

    if ($exists_scenario == true) {
        print("<!-- Trying to insert scenario --><BR>");

        //Replaces all the occurrences of ">" and "<" by " "
        $scene_title = str_replace(">", " ", str_replace("<", " ", $scene_title));
        $scene_goal = str_replace(">", " ", str_replace("<", " ", $scene_goal));
        $scene_context = str_replace(">", " ", str_replace("<", " ", $scene_context));
        $scene_performer = str_replace(">", " ", str_replace("<", " ", $scene_performer));
        $scene_resource = str_replace(">", " ", str_replace("<", " ", $scene_resource));
        $scene_exception = str_replace(">", " ", str_replace("<", " ", $scene_exception));
        $scene_episode = str_replace(">", " ", str_replace("<", " ", $scene_episode));
        insertRequestToAddScenario($_SESSION['id_projeto_corrente'], $scene_title, $scene_goal, $scene_context, $scene_performer, $scene_resource, $scene_exception, $scene_episode, $_SESSION['id_usuario_corrente']);
        print("<!-- Scenario inserted with success! --><BR>");
    } else {
        ?>
        <html><head><title>Project</title></head><body bgcolor="#FFFFFF">
                <p style="color: red; font-weight: bold; text-align: center">This scenario already exists!</p>
                <br>
                <br>
            <center><a href="JavaScript:window.history.go(-1)">Return</a></center>
        </body></html>
        <?php
        return;
    }
    ?>

    <script language="javascript1.2">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');
        location.href = "adds_scene.php?id_projeto=<?= $id_project ?>&sucesso=s";

    </script>

    <?php
} else {    // script called through the superior menu
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    ?>

    <html>
        <head>
            <title>Add scenario</title>
        </head>
        <body>
            <script language="JavaScript">
                <!--
                function checks_form_values(form) {
                    title_area = form.titulo.value;
                    goal_area = form.objetivo.value;
                    context_area = form.contexto.value;

                    if ((title_area === ""))
                    {
                        alert("Please, insert the title of the scenario.");
                        form.titulo.focus();
                        return false;
                    } else {
                        padrao = /[\\\/\?"<>:|]/;
                        OK = padrao.exec(title_area);
                        if (OK) {
                            window.alert("The title of the scneario must not contain any of the follwing characters:   / \\ : ? \" < > |");
                            form.titulo.focus();
                            return false;
                        } else {
                            //do nothing
                        }
                    }

                    if ((goal_area === "")) {
                        alert("Please, insert the objective of the scenario.");
                        form.objetivo.focus();
                        return false;
                    } else {
                        //do nothing
                    }

                    if ((context_area === "")) {
                        alert("Please, insert the context of the scenario.");
                        form.contexto.focus();
                        return false;
                    } else {
                        //do nothing
                    }
                }
                //-->

    <?php
// Cen�rio -  Incluir Cen�rio 
//Objetivo:        Permitir ao usu�rio a inclus�o de um novo cen�rio
//Contexto:        Usu�rio deseja incluir um novo cen�rio.
//              Pr�-Condi��o: Login, cen�rio ainda n�o cadastrado
//Atores:        Usu�rio, Sistema
//Recursos:        Dados a serem cadastrados
//Epis�dios:    O sistema fornecer� para o usu�rio uma tela com os seguintes campos de texto:
//                - Nome Cen�rio
//                - Objetivo.  Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//                - Contexto.  Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//                - Atores.    Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//                - Recursos.  Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//                - Exce��o.   Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
//                - Epis�dios. Restri��o: Caixa de texto com pelo menos 16 linhas de escrita vis�veis
//                - Bot�o para confirmar a inclus�o do novo cen�rio
//              Restri��es: Depois de clicar no bot�o de confirma��o,
//                          o sistema verifica se todos os campos foram preenchidos. 
// Exce��o:        Se todos os campos n�o foram preenchidos, retorna para o usu�rio uma mensagem avisando
//              que todos os campos devem ser preenchidos e um bot�o de voltar para a pagina anterior.
    ?>

            </SCRIPT>

            <h4>Add scenario</h4>
            <br>
            <?php
            if ($success == "s") {
                ?>
                <p style="color: blue; font-weight: bold; text-align: center">Scenario inserted with success!</p>
                <?php
            }
            ?>    
            <form action="" method="post">
                <table>
                    <tr>
                        <td>Project:</td>
                        <td><input disabled size="51" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <td>Title:</td>
                    <td><input size="51" name="titulo" type="text" value=""></td>                
                    <tr>
                        <td>Objective:</td>
                        <td><textarea cols="51" name="objetivo" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Context:</td>
                        <td><textarea cols="51" name="contexto" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Actors:</td>
                        <td><textarea cols="51" name="atores" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Resources:</td>
                        <td><textarea cols="51" name="recursos" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Exception:</td>
                        <td><textarea cols="51" name="excecao" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Episodes:</td>
                        <td><textarea cols="51" name="episodios" rows="5" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return checks_form_values(this.form);" value="Add Scenario"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Close</a></center>
        <br><i><a href="showSource.php?file=adds_scenario.php">See the source code!</a></i>
    </body>
    </html>

    <?php
}
?>
