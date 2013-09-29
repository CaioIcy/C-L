<?php
/*
 * File: relogin.php
 * Purpose: Screen used to login an user in case fail
 */
?>
<html>
    <head>
        <title>Entre com seu Login e Senha</title>
    </head>
    <body>
        <p style="color: red; font-weight: bold; text-align: center">
            <img src="Images/Logo_CEL.jpg" width="180" height="180"><br/><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;Login ou Senha Incorreto</p>
        <form name="loginScreen" action="login.php" method="POST">
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
