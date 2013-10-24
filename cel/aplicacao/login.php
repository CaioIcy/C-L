<?php
/*
 * File: login.php
 * Purpose: View of the login screen with their scenarios
 */

session_start();

define("QUERY_USER", "SELECT * FROM usuario WHERE login='%s' AND senha='%s'");

include_once 'bd.inc';
include_once 'httprequest.inc';

$submit = false;
$authenticated = false;

shows_loginForm();

function shows_loginForm() {
    $url = 'index.php';
    ?>

    <html>
        <head>
            <title>Entre com seu Login e Senha</title>
        </head>
        <body>
            <p style="color: green; font-weight: bold; text-align: center">
                <img src="Images/Logo_CEL.jpg" width="100" height="100"><br/><br/>
                &nbsp;&nbsp;&nbsp;&nbsp;Entre com seu Login e Senha:</p>

            <form name="loginScreen" action="?url=<?= $url ?>" method="POST">
                <div align="center">
                    <table cellpadding="5">
                        <tr><td>Login:</td><td><input maxlength="32" name="login" size="24" type="text"></td></tr>
                        <tr><td>Senha:</td><td><input maxlength="32" name="senha" size="24" type="password"></td></tr>
                        <tr><td height="10"></td></tr>
                        <tr><td align="center" colspan="2"><input name="submit" type="submit" value="Entrar"></td></tr>
                    </table>

                    <p><a href="adds_user.php">Cadastrar-se</a>&nbsp;&nbsp;

                        <a href="forgotten_password.php">Esqueci senha</a></p>
                </div>
            </form>
        </body>

        <i><a href="showSource.php?file=login.php">Veja o c�digo fonte!</a></i>
    </html>
    <?php
}

function authenticate_user($userName, $userPassword) {

    $database = database_connect();
    $result = false;

    $query_user = sprintf(QUERY_USER, mysql_real_escape_string($userName), mysql_real_escape_string($userPassword));
    $authenticated = mysql_query($query_user, $database);

    if ($authenticated) {
        $result = true;
    } else {
        $result = false;
    }

    return ($result);
}

/** @Episodio 2: Conectar o SGBD * */
/** @Restri��o: a fun��o bd_connect definida em bd.inc � utilizada * */
/** @Exce��o: Erro ao conectar banco de dados * */
/** @Episodio 9: Se o formul�rio tiver sido submetido ent�o verificar se o login e senha est�o corretos. * */
if ($submit) {
    $senha_cript = md5($user_password);
    $query_database_command = "SELECT id_usuario FROM usuario WHERE login='$user_login' AND senha='$senha_cript'";
    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao executar a query");

    /** @Episodio 10: Se o login e/ou senha estiverem incorretos ent�o retornar a p�gina de login com wrong=true na URL. * */
    if (!mysql_num_rows($query_connecting_database)) {
        ?>
        <script language="javascript1.3">
            document.location.replace('login.php?wrong=true&url=<?= $url ?>');
        </script>

        <?php
        $wrong = $_GET["wrong"];
    } else {/** @Episodio 11: Se o login e senha estiverem corretos ent�o registrar sess�o para o usu�rio, fechar login.php e abrir aplica��o . * */
        $row = mysql_fetch_row($query_connecting_database);
        $id_currentUser = $row[0];

        session_register("id_currentUser");
        ?>
        <script language="javascript1.3">
            opener.document.location.replace('<?= $url ?>');
            self.close();
        </script>

        <?php
    }
} else {/** @Episodio 3: Mostrar o formul�rio de login para usu�rio. * */
}
?>