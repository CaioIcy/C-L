<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

// Access control scenario
check_use_authentication("index.php");

/*
 * This script is called when there is an inclusion solicitation of a new
 * project, or when a new user registers in the system
 */

//Cen�rio  -  Cadastrar Novo Projeto 
//Objetivo:	   Permitir ao usu�rio cadastrar um novo projeto
//Contexto:	   Usu�rio deseja incluir um novo projeto na base de dados
//              Pr�-Condi��o: Login  
//Atores:	   Usu�rio
//Recursos:	   Sistema, dados do projeto, base de dados
//Epis�dios:   O Usu�rio clica na op��o �adicionar projeto� encontrada no menu superior.
//             O sistema disponibiliza uma tela para o usu�rio especificar os dados do novo projeto,
//              como o nome do projeto e sua descri��o.
//             O usu�rio clica no bot�o inserir.
//             O sistema grava o novo projeto na base de dados e automaticamente constr�i a Navega��o
//              para este novo projeto.
//Exce��o:	   Se for especificado um nome de projeto j� existente e que perten�a ou tenha a participa��o
//                 deste usu�rio, o sistema exibe uma mensagem de erro.
// Called through the submit button
if (isset($submit)) {

    $id_includedProject = inclui_projeto($project_name, $project_description);

    // Insert in the main table

    if ($id_includedProject != -1) {
        $database_conection = database_connect() or die("Error while connecting to SGBD");
        $manager = 1;
        $id_currentUser = $_SESSION['id_currentUser'];
        $query = "INSERT INTO participa (id_user, id_project, manager) VALUES ($id_currentUser, $id_includedProject, $manager)";
        mysql_query($query) or die("Error while connection to the main table");
    } else {
        ?>
        <html>
            <title>Error</title>
            <body>
                <p style="color: red; font-weight: bold; text-align: center">Project name already exists!</p>
            <center><a href="JavaScript:window.history.go(-1)">Return</a></center>
        </body>
        </html>   
        <?php
        return;
    }
    ?>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
// Called normally
} else {
    ?>

    <html>
        <head>
            <title>Add project</title>
            <script language="javascript1.3">

                function checks_textArea() {
                    if (document.forms[0].nome.value == "") {
                        alert('Fill the field "Name"');
                        document.forms[0].nome.focus();
                        return false;
                    } else {
                        padrao = /[\\\/\?"<>:|]/;
                        nOK = padrao.exec(document.forms[0].nome.value);
                        if (nOK)
                        {
                            window.alert("The name of the project must not contain any of the follwoing characters:   / \\ : ? \" < > |");
                            document.forms[0].nome.focus();
                            return false;
                        }
                    }
                    return true;
                }

            </script>
        </head>
        <body>
            <h4>Add project:</h4>
            <br>
            <form action="" method="post" onSubmit="return checks_textArea();">
                <table>
                    <tr>
                        <td>Name:</td>
                        <td><input maxlength="128" name="nome" size="48" type="text"></td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td><textarea cols="48" name="descricao" rows="4"></textarea></td>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Adicionar Projeto"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=add_projeto.php">See the source code!</a></i>
        </body>
    </html>

    <?php
}
?>
