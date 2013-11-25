<?php
/* File: Call_UpdUser.php
 * Purpose: This  script updates an existing user
 */
session_start();

include_once("bd.inc");

$database_conection = database_connect() or die("Error while connecting to SGBD");

// Cen�rio - Alterar cadastro
//
//Objetivo:	 Permitir ao usu�rio realizar altera��o nos seus dados cadastrais	
//Contexto:	 Sistema aberto, Usu�rio ter acessado ao sistema e logado 
//           Usu�rio deseja alterar seus dados cadastrais 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema.	
//Recursos:	 Interface	
//Epis�dios: O sistema fornecer� para o usu�rio uma tela com os seguintes campos de texto,
//           preenchidos com os dados do usu�rio,  para serem alterados:
//           nome, email, login, senha e confirma��o da senha; e um bot�o de atualizar
//           as informa��es fornecidas

$id_user = $_SESSION['id_usuario_corrente'];


$query_database_command = "SELECT * FROM usuario WHERE id_usuario='$id_user'";

$query_connecting_database = mysql_query($query_database_command) or die("Error while executing query");

$row = mysql_fetch_row($query_connecting_database);
$user_name = $row[1];
$user_email = $row[2];
$user_login = $row[3];
$user_password = $row[4];
?>
<html>
    <head>
        <title>Change user data</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <script language="JavaScript">
        <!--
        function TestarBranco(form)
        {
            login = form.login.value;
            senha = form.senha.value;
            senha_conf = form.senha_conf.value;
            nome = form.nome.value;
            email = form.email.value;

            if (login == "")
            {
                alert("Please, insert your Login.");
                form.login.focus();
                return false;
            }
            if (email == "")
            {
                alert("Please, insert your e-mail.");
                form.email.focus();
                return false;
            }
            if (senha == "")
            {
                alert("Please, insert your password.");
                form.senha.focus();
                return false;
            }
            if (nome == "")
            {
                alert("Please, insert your name.");
                form.nome.focus();
                return false;
            }
            if (senha != senha_conf)
            {
                alert("The password and the confirmation aren't the same!");
                form.senha.focus();
                return false;
            }

        }


        function checkEmail(email) {
            if (email.value.length > 0)
            {
                if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
                {
                    return (true);
                }
                alert("Warning: the inserted e-mail is not valid.");
                email.focus();
                email.select();
                return (false);
            }
        }


        //-->
    </SCRIPT>
    <body>
        <h3 style="text-align: center">Please, fill the data below:</h3>
        <form action="updUser.php" method="post">
            <table>
                <tr>
                    <td>Name:</td><td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?= $user_name ?>"></td>
                </tr>
                <tr>
                    <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?= $user_email ?>" OnBlur="checkEmail(this)"></td>
                </tr>
                <tr>
                    <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?= $user_login ?>"></td>
                </tr>
                <tr>
                    <td>Password:</td><td><input name="senha" maxlength="32" size="16" type="password" value=""></td>
                </tr>
                <tr>
                    <td>Password (confirmation):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
                </tr>
                <tr>
                    <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return TestarBranco(this.form);" type="submit" value="Atualizar"></td>
                </tr>
            </table>
        </form>
        <br><i><a href="View/showSource.php?file=Call_UpdUser.php">See the source code!</a></i>
    </body>
</html>