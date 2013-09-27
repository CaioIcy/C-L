<?php
/**
  @Titulo: Acessar o sistema

  @Objetivo: Permitir que o usu�rio acesse a Aplica��o de Edi��o de L�xicos e de Edi��o de Cen�rios, cadastre-se no sistema ou requisite sua senha no caso de t�-la esquecido.

  @Contexto: A p�gina da aplica��o � acessada. Na p�gina de abertura ../cel/aplicacao/login.php o usu�rio insere login ou senha incorretos - $wrong=true.

  @Atores: usu�rio, aplica��o

  @Recursos: URL de acesso ao sistema,  login, senha, bd.inc, httprequest.inc, $wrong, $url, showSource.php?file=login.php, forgotten_password.php, add_usuario.php?novo=true
 * */
/** @Episodio 1: Iniciar sess�o * */
session_start();

include("bd.inc");
include("httprequest.inc");

$url = '';
$submit = '';
$user_login = '';
$user_password = '';
$wrong = "false";


/** @Episodio 2: Conectar o SGBD * */
/** @Restri��o: a fun��o bd_connect definida em bd.inc � utilizada * */
/** @Exce��o: Erro ao conectar banco de dados * */
//$database_conection = database_connect() or die("Error while connecting to SGBD");

/** @Episodio 9: Se o formul�rio tiver sido submetido ent�o verificar se o login e senha est�o corretos. * */
if ($submit == 'Entrar') {
    $senha_cript = md5($user_password);
    $query = "SELECT id_usuario FROM usuario WHERE login='$user_login' AND senha='$senha_cript'";
    $query_r = mysql_query($query) or die("Erro ao executar a query");

    /** @Episodio 10: Se o login e/ou senha estiverem incorretos ent�o retornar a p�gina de login com wrong=true na URL. * */
    if (!mysql_num_rows($query_r)) {
        ?>
        <script language="javascript1.3">
            document.location.replace('login.php?wrong=true&url=<?= $url ?>');
        </script>

        <?php
        $wrong = $_get["wrong"];
    }

    /** @Episodio 11: Se o login e senha estiverem corretos ent�o registrar sess�o para o usu�rio, fechar login.php e abrir aplica��o . * */ else {

        $row = mysql_fetch_row($query_r);
        $id_currentUser = $row[0];

        session_register("id_usuario_corrente");
        ?>
        <script language="javascript1.3">
            opener.document.location.replace('<?= $url ?>');
            self.close();
        </script>

        <?php
    }
}

/** @Episodio 3: Mostrar o formul�rio de login para usu�rio. * */ else {
    ?>

    <html>
        <head>
            <title>Entre com seu Login e Senha</title>
        </head>
        <body>

            <?php
            /** @Episodio 4: Se wrong = true ent�o mostrar a mensagem Login ou Senha incorreto . * */
            if ($wrong == "true") {
                ?>

                <p style="color: red; font-weight: bold; text-align: center">
                    <img src="Images/Logo_CEL.jpg" width="180" height="180"><br/><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;Login ou Senha Incorreto</p>

                <?php
            }
            /** @Episodio 5: Se wrong != true ent�o mostrar a mensagem Entre com seu login e senha. * */ else {
                ?>

                <p style="color: green; font-weight: bold; text-align: center">
                    <img src="Images/Logo_CEL.jpg" width="100" height="100"><br/><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;Entre com seu Login e Senha:</p>

                <?php
            }
            ?>

            <form action="?url=<?= $url ?>" method="post">
                <div align="center">
                    <table cellpadding="5">
                        <tr><td>Login:</td><td><input maxlength="32" name="login" size="24" type="text"></td></tr>
                        <tr><td>Senha:</td><td><input maxlength="32" name="senha" size="24" type="password"></td></tr>
                        <tr><td height="10"></td></tr>
                        <tr><td align="center" colspan="2"><input name="submit" type="submit" value="Entrar"></td></tr>
                    </table>

                    <?php /** @Episodio 6: [CADASTRAR NOVO USU�RIO] * */ ?>
                    <p><a href="adds_user.php">Cadastrar-se</a>&nbsp;&nbsp;

                        <?php /** @Episodio 7: [LEMBRAR SENHA] * */ ?>
                        <a href="forgotten_password.php">Esqueci senha</a></p>
                </div>
            </form>
        </body>

        <?php /** @Episodio 8: [MOSTRAR O C�DIGO FONTE] * */ ?>

        <i><a href="showSource.php?file=login.php">Veja o c�digo fonte!</a></i>    
    </html>

    <?php
}
?>