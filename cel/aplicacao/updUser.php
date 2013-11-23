<?php
session_start();

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';
include_once 'bd.inc';

$id_user = $_SESSION['id_usuario_corrente'];

$database_conection = database_connect() or die("Erro ao conectar ao SGBD");

/*
 * Scenario - Update registered information
 * Objective:   Allow the user to alter his registered data
 * Context:     Open system, user must have logged into the system, user wishes
 *              to update his registered data
 *              Pre-condition: User has accessed the system
 * Actors:      User, system
 * Resources:   Interface
 * Episodes:    1- The user alters the desired data
 *              2- User clicks on the UPDATE button
 */
?>

<html>
    <head>
        <title>Change user data</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>


    <body>

        <?php
        $password_cript = md5($user_password);
        $user = "UPDATE usuario SET  nome ='$name' , login = '$user_login' , email = '$user_email' , senha = '$password_cript' WHERE  id_usuario='$id_user'";

        mysql_query($user) or die("<p style='color: red; font-weight: bold; text-align: center'>Error!Login already exists!</p><br><br><center><a href='JavaScript:window.history.go(-1)'>Return</a></center>");
        ?>

    <center><b>Information successfully updated!</b></center>
    <center><button onClick="javascript:window.close();">Close</button></center>


</body>
</html>
