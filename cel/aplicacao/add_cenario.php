<?php
session_start();

/* vim: set expandtab tabstop=4 shiftwidth=4: */

// add_cenario.php: Este script cadastra um novo cenario do projeto. Eh
//                  passada, atraves da URL, uma variavel $id_projeto, que
//                  indica em que projeto deve ser inserido o novo cenario.

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

check_use_authentication("index.php");        // Checa se o usuario foi autenticado

if (!isset($sucesso)) {
    $sucesso = "n";
}

// Conecta ao SGBD
$database_conection = database_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    $is_existingScenario = checarCenarioExistente($_SESSION['id_projeto_corrente'], $scene_title);
    ?>  <!-- ADICIONEI ISTO PARA TESTES -->
    <!--
       RET = <?= $is_existingScenario ?> => RET = <?PHP $is_existingScenario ? print("TRUE")  : print("FALSE") ; ?><BR>
    $sucesso        = <?= $sucesso ?><BR>
    _GET["sucesso"] = <?= $_GET["sucesso"] ?><BR>   
    -->
    <?PHP
    if ($is_existingScenario == true) {
        print("<!-- Tentando Inserir Cenario --><BR>");

        /* Substitui todas as ocorrencias de ">" e "<" por " " */
        $scene_title = str_replace(">", " ", str_replace("<", " ", $scene_title));
        $scene_goal = str_replace(">", " ", str_replace("<", " ", $scene_goal));
        $scene_context = str_replace(">", " ", str_replace("<", " ", $scene_context));
        $scene_performer = str_replace(">", " ", str_replace("<", " ", $scene_performer));
        $scene_resource = str_replace(">", " ", str_replace("<", " ", $scene_resource));
        $scene_exception = str_replace(">", " ", str_replace("<", " ", $scene_exception));
        $scene_episode = str_replace(">", " ", str_replace("<", " ", $scene_episode));
        inserirPedidoAdicionarCenario($_SESSION['id_projeto_corrente'], $scene_title, $scene_goal, $scene_context, $scene_performer, $scene_resource, $scene_exception, $scene_episode, $_SESSION['id_usuario_corrente']);
        print("<!-- Cenario Inserido Com Sucesso! --><BR>");
    } else {
        ?>
        <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
                <p style="color: red; font-weight: bold; text-align: center">Este cen�rio j� existe!</p>
                <br>
                <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </body></html>
        <?php
        return;
    }
    ?>

    <script language="javascript1.2">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');
    //self.close();
    //location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>add_cenario.php?id_projeto=<?= $id_project ?>&sucesso=s" ;


        location.href = "add_cenario.php?id_projeto=<?= $id_project ?>&sucesso=s";

    </script>

    <?php
} else {    // Script chamado atraves do menu superior
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    ?>

    <html>
        <head>
            <title>Adicionar Cen�rio</title>
        </head>
        <body>
            <script language="JavaScript">
            <!--
                function checks_textArea(form)
                {
                    title_area = form.titulo.value;
                    goal_area = form.objetivo.value;
                    context_area = form.contexto.value;

                    if ((title_area == ""))
                    {
                        alert("Por favor, digite o titulo do cen�rio.")
                        form.titulo.focus()
                        return false;
                    } else {
                        padrao = /[\\\/\?"<>:|]/;
                        OK = padrao.exec(title_area);
                        if (OK)
                        {
                            window.alert("O t�tulo do cen�rio n�o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
                            form.titulo.focus();
                            return false;
                        }
                    }

                    if ((goal_area == ""))
                    {
                        alert("Por favor, digite o objetivo do cen�rio.")
                        form.objetivo.focus()
                        return false;
                    }

                    if ((context_area == ""))
                    {
                        alert("Por favor, digite o contexto do cen�rio.")
                        form.contexto.focus()
                        return false;
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

            <h4>Adicionar Cen�rio</h4>
            <br>
    <?php
    if ($success == "s") {
        ?>
                <p style="color: blue; font-weight: bold; text-align: center">Cen�rio inserido com sucesso!</p>
        <?php
    }
    ?>    
            <form action="" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="51" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <td>T�tulo:</td>
                    <td><input size="51" name="titulo" type="text" value=""></td>                
                    <tr>
                        <td>Objetivo:</td>
                        <td><textarea cols="51" name="objetivo" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td>
                        <td><textarea cols="51" name="contexto" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Atores:</td>
                        <td><textarea cols="51" name="atores" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td>
                        <td><textarea cols="51" name="recursos" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Exce��o:</td>
                        <td><textarea cols="51" name="excecao" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Epis�dios:</td>
                        <td><textarea cols="51" name="episodios" rows="5" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return checks_textArea(this.form);" value="Adicionar Cen�rio"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=add_cenario.php">Veja o c�digo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>
