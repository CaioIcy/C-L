<?php

/* File: adds_lexicon.php
 * Purpose: This script registers a new term in the lexicon of the project.
 * A variable &id_project is recieved through a URL, and indicates in which project the new scenario will be inserted in.
 */

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

session_start();

if (!isset($success)) {
    $success = 'n';
}

check_use_authentication("index.php");

$database_conection = database_connect() or die("Erro ao conectar ao SGBD");

// Script called from submit button
if (isset($submit)) {

    $exists_scenario = checarLexicoExistente($_SESSION['id_currentProject'], $lexicon_name);
    
    if (!isset($list_ofSynonyms)){
        $list_ofSynonyms = array();
    }
    
    $exists_synonyms = checarSinonimo($_SESSION['id_currentProject'], $list_ofSynonyms);

    if (($exists_scenario == true) AND ($exists_synonyms == true )) {
        $id_currentUser = $_SESSION['id_currentUser'];
        inserirPedidoAdicionarLexico($id_project, $lexicon_name, $lexicon_notion, $lexicon_impacts, $id_currentUser, $list_ofSynonyms, $lexicon_classification);
    } else {
        ?>
        <html><head><title>Project</title></head><body bgcolor="#FFFFFF">
                <p style="color: red; font-weight: bold; text-align: center">This symbol or synonym already exists!</p>
                <br>
                <br>
            <center><a href="JavaScript:window.history.go(-1)">Return</a></center>
        </body></html>
        <?php
        return;
    }
    $ip_value = CELConfig_ReadVar("HTTPD_ip");
    ?>

    <script language="javascript1.2">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');
        location.href = "add_lexico.php?id_projeto=<?= $id_project ?>&sucesso=s";

    </script>   
    <?php

} else {    // script called through the superior menu
    $query = "SELECT nome FROM projeto WHERE id_projeto = $id_project";
    $query_r = mysql_query($query) or die("Erro ao executar a query");
    $result = mysql_fetch_array($query_r);
    $project_name = $result['nome'];
    ?>

    <html>
        <head>
            <title>Add lexicon</title>
        </head>
        <body>
            <script language="JavaScript">
            <!--
                function checks_textArea(form)
                {
                    nome = form.nome.value;
                    nocao = form.nocao.value;

                    if (nome == "")
                    {
                        alert(" Please insert the NAME of the lexicon.\n The field NAME is mandatory to be filled.");
                        form.nome.focus();
                        return false;
                    } else {
                        padrao = /[\\\/\?"<>:|]/;
                        nOK = padrao.exec(nome);
                        if (nOK)
                        {
                            window.alert("The name of the lexicon must not contain any of the following characteres:   / \\ : ? \" < > |");
                            form.nome.focus();
                            return false;
                        }
                    }

                    if (nocao == "")
                    {
                        alert(" Please, insert the NOTION of the lexicon.\n The field NOTION if mandatory to be filled.");
                        form.nocao.focus();
                        return false;
                    }

                }
                function adds_synonymou()
                {
                    listSinonimo = document.forms[0].elements['listSinonimo[]'];

                    if (document.forms[0].sinonimo.value == "")
                        return;

                    sinonimo = document.forms[0].sinonimo.value;
                    padrao = /[\\\/\?"<>:|]/;
                    nOK = padrao.exec(sinonimo);
                    if (nOK)
                    {
                        window.alert("The synonyms of the lexicon must not contain any of the following characters:   / \\ : ? \" < > |");
                        document.forms[0].sinonimo.focus();
                        return;
                    }

                    listSinonimo.options[listSinonimo.length] = new Option(document.forms[0].sinonimo.value, document.forms[0].sinonimo.value);

                    document.forms[0].sinonimo.value = "";

                    document.forms[0].sinonimo.focus();

                }

                function deletes_synonymou()
                {
                    listSinonimo = document.forms[0].elements['listSinonimo[]'];

                    if (listSinonimo.selectedIndex == -1)
                        return;
                    else
                        listSinonimo.options[listSinonimo.selectedIndex] = null;

                    deletes_synonymou();
                }

                function doSubmit()
                {
                    listSinonimo = document.forms[0].elements['listSinonimo[]'];

                    for (var i = 0; i < listSinonimo.length; i++)
                        listSinonimo.options[i].selected = true;

                    return true;
                }

            //-->

    <?php
    //Cen�rios -  Incluir L�xico 
    //Objetivo:    Permitir ao usu�rio a inclus�o de uma nova palavra do l�xico
    //Contexto:    Usu�rio deseja incluir uma nova palavra no l�xico.
    //                     Pr�-Condi��o: Login, palavra do l�xico ainda n�o cadastrada
    //Atores:         Usu�rio, Sistema
    //Recursos:    Dados a serem cadastrados
    //Epis�dios:    O sistema fornecer� para o usu�rio uma tela com os seguintes campos de texto:
    //               - Entrada L�xico.
    //               - No��o.   Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
    //               - Impacto. Restri��o: Caixa de texto com pelo menos 5 linhas de escrita vis�veis
    //              Bot�o para confirmar a inclus�o da nova entrada do l�xico
    //              Restri��es: Depois de clicar no bot�o de confirma��o, o sistema verifica se todos
    //              os campos foram preenchidos. 
    //Exce��o:    Se todos os campos n�o foram preenchidos, retorna para o usu�rio uma mensagem
    //              avisando que todos os campos devem ser preenchidos e um bot�o de voltar para a pagina anterior.
    ?>

            </SCRIPT>

            <h4>Add symbol</h4>
            <br>
    <?php
    if ($success == "s") {
        ?>
                <p style="color: blue; font-weight: bold; text-align: center">Symbol inserted with success!</p>
        <?php
    }
    ?>       
            <form action="?id_project=<?= $id_project ?>" method="post" onSubmit="return(doSubmit());">
                <table>
                    <tr>
                        <td>Project:</td>
                        <td><input disabled size="48" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <tr>
                        <td>Name:</td>
                        <td><input size="48" name="nome" type="text" value=""></td>
                    </tr>    
                    <tr valign="top">
                        <td>Synonyms:</td>
                        <td width="0%">
                            <input name="sinonimo" size="15" type="text" maxlength="50">             
                            &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Adicionar" onclick="adds_synonymou()">
                            &nbsp;&nbsp;<input type="button" value="Remover" onclick="deletes_synonymou()">&nbsp;
                        </td>
                    </tr>
                    <tr> 
                        <td>
                        </td>   
                        <td width="100%">
                    <left><select multiple name="listSinonimo[]"  style="width: 400px;"  size="5"></select></left>                      <br> 
                    </td>
                    <tr>
                    </tr>
                    </tr>
                    <tr>
                        <td>Notion:</td>
                        <td><textarea cols="51" name="nocao" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Impact:</td>
                        <td><textarea  cols="51" name="impacto" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Classification:</td>
                        <td>
                            <SELECT id='classificacao' name='classificacao' size=1 width="300">
                                <OPTION value='sujeito' selected>Subject</OPTION>
                                <OPTION value='objeto'>Object</OPTION>
                                <OPTION value='verbo'>Verb</OPTION>
                                <OPTION value='estado'>State</OPTION>
                            </SELECT>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60">
                            <input name="submit" type="submit" onClick="return checks_textArea(this.form);" value="Adicionar S�mbolo"><BR><BR>
                            </script>
                            <A HREF="#" OnClick="javascript:open('RegrasLAL.html', '_blank', 'dependent,height=380,width=520,titlebar');"> See <i>LAL</i> rules</A>
                        </td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Close</a></center>            
        <br><i><a href="showSource.php?file=add_lexico.php">See the source code!</a></i>
    </body>

    </html>

    <?php
}
?>
