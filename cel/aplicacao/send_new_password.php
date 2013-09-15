<?php
include("bd.inc");
include("httprequest.inc");

// Cen�rio - Lembrar senha 
//Objetivo:	 Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:	 Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//           senha. 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema	
//Recursos:	 Banco de Dados	
//Epis�dios: O sistema verifica se o login informado � cadastrado no banco de dados.     
//           Se o login informado for cadastrado, sistema consulta no banco de dados qual 
//           o email e senha do login informado.           

$database_conection = database_connect() or die("Error while connecting to SGBD");

$query = "SELECT * FROM usuario WHERE login='$user_login'";

$query_r = mysql_query($query) or die("Error while executing the query");
?>

<html>
    <head>
        <title>Send password</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <body bgcolor="#FFFFFF">
        <?php
        if (!mysql_num_rows($query_r)) {
            ?>
            <p style="color: red; font-weight: bold; text-align: center">Login does not exist!</p>
        <center><a href="JavaScript:window.history.go(-1)">Return</a></center>
        <?php
    } else {
        $row = mysql_fetch_row($query_r);
        $user_name = $row[1];
        $mail = $row[2];
        $user_login = $row[3];
        $user_password = $row[4];

// Cen�rio - Lembrar senha 
//Objetivo:	 Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:	 Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//           senha. 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema	
//Recursos:	 Banco de Dados	
//Epis�dios: Sistema envia a senha para o email cadastrado correspondente ao login que 
//           foi informado pelo usu�rio.     
//           Caso n�o exista nenhum login cadastrado igual ao informado pelo usu�rio, 
//           sistema exibe mensagem de erro na tela dizendo que login � inexistente, e 
//           exibe um bot�o voltar, que redireciona o usu�rio para a tela de login novamente.
                     
        //$Vemail = ini_set("SMTP","mail.gmail.com");  
        //require("PHPMailer.php");
        // Seta o SMTP sem alterar o config
        //ini_set("SMTP","mail.hotpop.com");

        //Generates a random string with string_size characters
        function generateRandomString($string_size) {
            $str = "ABCDEFGHIJKLMNOPQRSTUVXYWZabcdefghijklmnopqrstuvxywz0123456789";
            $cod = "";
            for ($a = 0; $a < $string_size; $a++) {
                $rand = rand(0, 61);
                $cod .= substr($str, $rand, 1);
            }
            return $cod;
        }

        $new_password = generateRandomString(6);
        $encrypted_new_password = md5($new_password);

        // Substitutes old password for the new one in the database
        $query_up = "update usuario set senha = '$encrypted_new_password' where login = '$user_login'";
        $query_r_up = mysql_query($query_up) or die("Error while executing the update query in the user table");

        $corpo_email = "Dear $user_name,\n As requested, we are sending you your new password to access the C&L system.\n\n Login: $user_login \n Password: $new_password \n\n To avoid future troubles, change your password as soon as you can. \n Thank you! \n C&L support team.";
        $headers = "";
        if (mail("$mail", "New C&L password", "$corpo_email", $headers)) {
            ?>
            <p style="color: red; font-weight: bold; text-align: center">A new password was created and sent to your registered e-mail.</p>
            <center><a href="JavaScript:window.history.go(-2)">Return</a></center>
            <?php
        } else {
            ?>
            <p style="color: red; font-weight: bold; text-align: center">An error occurred while sending the e-mail!</p>
            <center><a href="JavaScript:window.history.go(-2)">Return</a></center>
            <?php
        }
    }
    ?>

</body>
</html>
