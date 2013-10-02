<?php
/* File: adds_user.php
 * Purpose: This script registers a new user in the system
 */
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php

session_start();

include_once("bd.inc");
include("funcoes_genericas.php");
include("httprequest.inc");

define("QUERY_LOGIN", "SELECT id_usuario FROM usuario WHERE login = '%s'");

$first_access = true;

/*
  // Cen�rio - Incluir usu�rio independente
  // Objetivo:  Permitir um usu�rio, que n�o esteja cadastrado como administrador, se cadastrar
  //            com o perfil de administrador
  // Contexto:  Sistema aberto Usu�rio deseja cadastrar-se ao sistema como administrador.
  //            Usu�rio na tela de cadastro de usu�rio
  //            Pr�-Condi��o: Usu�rio ter acessado ao sistema
  // Atores:    Usu�rio, Sistema
  // Recursos:  Interface, Banco de Dados
  // Epis�dios: O sistema retorna para o usu�rio uma interface com campos para entrada de
  //            um Nome, email, login, uma senha e a confirma��o da senha.
  //            O usu�rio preenche os campos e clica em cadastrar
  //            O sistema ent�o checa para ver se todos os campos est�o preenchidos.
  //              Caso algum campo deixar de ser preenchido, o sistema avisa que todos
  //               os campos devem ser preenchidos.
  //              Caso todos os campos estiverem preenchidos, o sistema checa no banco
  //               de dados para ver se esse login j� existe..
  //              Caso aquele login digitado j� exista, o sistema retorna a mesma p�gina
  //               para o usu�rio avisando que o usu�rio deve escolher outro login,.
 */

function encrypting_password($current_password){
    
    $current_password = md5($current_password);
    return $current_password;
    
}

function checks_equalsLogin($current_login){
    
    $database = database_connect();
    $result = false;
    
    $query_login = sprintf(QUERY_USER, mysql_real_escape_string($current_login));
    $exist_login = mysql_num_rows($query_login);
    
    if($exist_login){
        $result = true;
    }else{
        $result = false;
    }
    
    return ($result);
}

function checks_equalsPassword($user_password,$check_password){
    
    $result = false;
    
    if($user_password == $check_password){
        $result = true;
    }else{
        $result = false;
    }
    
    return ($result);
}

function checks_blankField($user_name,$user_email,$user_login,$user_password,$check_password){
    
    $result = false;
    
    if($user_login == "" || $user_email == "" || $user_name == "" || $user_password == "" || $check_password == ""){
        $result = true;
    }else{
        $result = false;
    }
    
    return $result;
}

function include_inDatabase($user_name,$user_login,$user_email, $user_password){
    
    $user_name = str_replace(">", " ", str_replace("<", " ", $user_name));
    $user_login = str_replace(">", " ", str_replace("<", " ", $user_login));
    $user_email = str_replace(">", " ", str_replace("<", " ", $user_email));
    $user_password = encrypting_password($user_password);
    
    $id_currentUser = simple_query("id_usuario", "usuario", "login = '$user_login'");
}

function display_errorBlankField(){
    $text_style = "color: red; font-weight: bold";
    $error_mensage = "Please, fill in all the fields.";
    $redo_url = "?p_style=$text_style&p_text=$error_mensage&nome=''&email=''&login=''&senha=''&senha_conf=''&novo=$novo";
    recarrega($redo_url);
}


if (isset($submit)) {   //if called by the submit button
    
    $first_access = "false";

    /*
     * The system checks if all the fields are filled. If some is not, it
     * warns the user.
     */
    if ($user_name == "" || $user_email == "" || $user_login == "" || $user_password == "" || $password_check == "") {
        $p_style = "color: red; font-weight: bold";
        $p_text = "Please, fill in all the fields.";
        recarrega("?p_style=$p_style&p_text=$p_text&nome=$user_name&email=$user_email&login=$user_login&senha=$user_password&senha_conf=$password_check&novo=$novo");
    } else {

        // Testa se as senhas fornecidas pelo usuario sao iguais.
        if ($user_password != $password_check) {
            $p_style = "color: red; font-weight: bold";
            $p_text = "The password and the confirmation are different! Please retry.";
            recarrega("?p_style=$p_style&p_text=$p_text&nome=$user_name&email=$user_email&login=$user_login&novo=$novo");
        } else {

            /*
             * Now all the fields are filled in. The system must check if there
             * isn't already someone registered with the same login
             */
            $database_conection = database_connect() or die("Error while connecting to SGBD");
            $query = "SELECT id_usuario FROM usuario WHERE login = '$user_login'";
            $query_r = mysql_query($query) or die("Error while sending query");

            // If there is someone with the same login
            if (mysql_num_rows($query_r)) {
                /*
                  //                $p_style = "color: red; font-weight: bold";
                  //                $p_text = "Login j� existente no sistema. Favor escolher outro login.";
                  //                recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&senha=$senha&senha_conf=$senha_conf&novo=$novo");
                  // Cen�rio - Adicionar Usu�rio
                  // Objetivo:  Permitir ao Administrador criar novos usu�rios.
                  // Contexto:  O Administrador deseja adicionar novos usu�rios (n�o cadastrados)
                  //            criando novos  usu�rios ao projeto selecionado.
                  //            Pr�-Condi��es: Login
                  // Atores:    Administrador
                  // Recursos:  Dados do usu�rio
                  // Epis�dios: O Administrador clica no link �Adicionar usu�rio (n�o existente) neste projeto�,
                  //            entrando com as informa��es do novo usu�rio: nome, email, login e senha.
                  //            Caso o login j� exista, aparecer� uma mensagem de erro na tela informando que
                  //            este login j� existe.
                 * 
                 */
                ?>
                <script language="JavaScript">
                    alert("Login already exists. Please choose another one.");
                </script>

                <?php
                recarrega("?novo=$novo");
                // Registering passed through all the tests (can be included in the database)
            } else {
                /* Substitutes all the occurences of ">" and "<" for " " */
                $user_name = str_replace(">", " ", str_replace("<", " ", $user_name));
                $user_login = str_replace(">", " ", str_replace("<", " ", $user_login));
                $user_email = str_replace(">", " ", str_replace("<", " ", $user_email));

                // Encrypting the password
                $user_password = md5($user_password);
                $query = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$user_name', '$user_login', '$user_email', '$user_password')";
                mysql_query($query) or die("Error while registering the user");
                recarrega("?cadastrado=&novo=$novo&login=$user_login");
            }
        }
    }
} elseif (isset($cadastrado)) {

    /*
     * Registering complete. Depending on where the user came from, 
     * send him to a different place.
     */


    /*
     * Came from the inicial login screen.
     * Registers that the recently registered user is logged in
     */
    if ($novo == "true") {

        /*
          // Cen�rio - Incluir usu�rio independente
          // Objetivo:  Permitir um usu�rio, que n�o esteja cadastrado como administrador, se cadastrar
          //            com o perfil de administrador
          // Contexto:  Sistema aberto Usu�rio deseja cadastrar-se ao sistema como administrador.
          //            Usu�rio na tela de cadastro de usu�rio
          //            Pr�-Condi��o: Usu�rio ter acessado ao sistema
          // Atores:    Usu�rio, Sistema
          // Recursos:  Interface, Banco de Dados
          // Epis�dios:  Caso aquele login digitado n�o exista, o sistema cadastra esse usu�rio
          //               como administrador no banco de dados,  possibilitando:
          //              - Redirecion�-lo  para a interface de CADASTRAR NOVO PROJETO;
         */
        $id_currentUser = simple_query("id_usuario", "usuario", "login = '$user_login'");
        session_register("id_usuario_corrente");
        ?>

        <script language="javascript1.3">

            // Redirect him to the project inclusion screen
            opener.location.replace('index.php');
            open('add_projeto.php', '', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');
            self.close();


        </script>

        <?php
    } else {

        /*
          // Cen�rio - Adicionar Usu�rio
          // Objetivo:  Permitir ao Administrador criar novos usu�rios.
          // Contexto:  O Administrador deseja adicionar novos usu�rios (n�o cadastrados) criando novos
          //              usu�rios ao projeto selecionado.
          //            Pr�-Condi��es: Login
          // Atores:    Administrador
          // Recursos:  Dados do usu�rio
          // Epis�dios: Clicando no bot�o Cadastrar para confirmar a adi��o do novo
          //             usu�rio ao projeto selecionado.
          //            O novo usu�rio criado receber� uma mensagem via email com seu login e senha.
         */

        /*
         * The administrator of the project just included the user.
         * Must now add the user to the project
         */
        $database_conection = database_connect() or die("Error while connecting to SGBD");
        $id_includedUser = simple_query("id_usuario", "usuario", "login = '$user_login'");
        $query = "INSERT INTO participa (id_usuario, id_projeto)
          VALUES ($id_includedUser, " . $_SESSION['id_projeto_corrente'] . ")";
        mysql_query($query) or die("Error while inserting in the main table");

        $user_name = simple_query("nome", "usuario", "id_usuario = $id_includedUser");
        $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
        ?>

        <script language="javascript1.3">

            document.writeln('<p style="color: blue; font-weight: bold; text-align: center"> User <b><?= $user_name ?></b> registered and included in the project <b><?= $project_name ?></b></p>');
            document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

        </script>

        <?php
    }
} else {
    if (empty($p_style)) {
        $p_style = "color: green; font-weight: bold";
        $p_text = "Please fill the data below:";
    }

    if ($first_access) {
        $user_email = "";
        $user_login = "";
        $user_name = "";
        $user_password = "";
        $password_check = "";
    }
    ?>

    <html>
        <head>
            <title>User registration</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        </head>
        <body>
            <script language="JavaScript">
                <!--
                function verifyEmail(form)
                {
                    email = form.email.value;
                    i = email.indexOf("@");
                    if (i == -1)
                    {
                        alert('Warning: the inserted e-mail is not valid.');
                        return false;
                    }
                }

                function checkEmail(email) {
                    if (email.value.length > 0)
                    {
                        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
                        {
                            return (true)
                        }
                        alert("Warning: the inserted e-mail is not valid.")
                        email.focus();
                        email.select();
                        return (false)
                    }
                }

                //-->



            </SCRIPT>

            <p style="<?= $p_style ?>"><?= $p_text ?></p>
            <form action="?novo=<?= $novo ?>" method="post">
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
                        <td>Password:</td><td><input name="senha" maxlength="32" size="16" type="password" value="<?= $user_password ?>"></td>
                        <td>Password (confirmation):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
                    </tr>
                    <tr>

                        <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return verifyEmail(this.form);" type="submit" value="Cadastrar"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=add_usuario.php">See the source code!</a></i>
        </body>
    </html>

    <?php
}
?>
