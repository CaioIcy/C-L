<?php
include_once 'bd.inc';
include_once 'httprequest.inc';

/*
 * Scenario - Remember password
 * Objective:   Allow the registered user, who forgot his password, to recieve it by e-mail
 * Context:     System is open, user in the remember password screen
 *              Pre-condition: User has accessed the system
 * Actors:      User, system
 * Resources:   Database
 * Episodes:    1- The system checks if the informed login is registered in the database
 *              2- If the informed login is registered, the system checks in the database
 *                  what the is e-mail and password, corresponding to that login.
 *              3- The system sends the password to the e-mail corresponding to the informed
 *                  login
 *              4- If the informed login isn't registered in the database, the system
 *                  displays an error message on the screen, saying the the login does
 *                  not exist, and displays a RETURN button, that redirects the user
 *                  to the login screen.
 */

$database_conection = database_connect() or die("Error while connecting to SGBD");

$query_database_command = "SELECT * FROM usuario WHERE login='$user_login'";

$query_connecting_database = mysql_query($query_database_command) or die("Error while executing the query");
?>

<html>
    <head>
        <title>Send password</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <body bgcolor="#FFFFFF">
        <?php
        if (!mysql_num_rows($query_connecting_database)) {
            ?>
            <p style="color: red; font-weight: bold; text-align: center">Login does not exist!</p>
        <center><a href="JavaScript:window.history.go(-1)">Return</a></center>
        <?php
    } else {
        $row = mysql_fetch_row($query_connecting_database);
        $user_name = $row[1];
        $mail = $row[2];
        $user_login = $row[3];
        $user_password = $row[4];

        //Generates a random string with string_size characters
        //$string_size: the size of the string that is going to be generated
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

        $email_body = "Dear $user_name,\n As requested, we are sending you your new password to access the C&L system.\n\n Login: $user_login \n Password: $new_password \n\n To avoid future troubles, change your password as soon as you can. \n Thank you! \n C&L support team.";
        $headers = "";
        if (mail("$mail", "New C&L password", "$email_body", $headers)) {
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
