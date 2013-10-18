<?php
session_start();

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';

check_user_authentication("index.php");        

$XML = "";
?>
<html>
    <body>
    <head>
        <title>Recuperar XML</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

<?php

//Cen�rio -  Gerar Relat�rios XML 
//Objetivo:     Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//              identificados por data.
//Contexto:     Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//Pr�-Condi��o: Login, projeto cadastrado.
//Atores:       Administrador
//Recursos:     Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.
//Epis�dios:    Restri��o: Recuperar os dados em XML do Banco de dados e os transformar
//                       por uma XSL para a exibi��o

$database_recuperation = database_connect() or die("Erro ao conectar ao SGBD");
if (isset($erase)) {
    if ($erase) {
        $query_erase = "DELETE FROM publicacao WHERE id_projeto = '$id_project' AND versao = '$version' ";
        $query_r_erase = mysql_query($query_erase);
    }
}
$query_database_command = "SELECT * FROM publicacao WHERE id_projeto = '$id_project'";
$query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query");
?>
    <h2>Recupera XML/XSL</h2><br>
    <?php
    while ($result = mysql_fetch_row($query_connecting_database)) {
        $data = $result[1];
        $version = $result[2];
        $XML = $result[3];
        ?>
        <table>
            <tr>
                <th>Vers�o:</th><td><?= $version ?></td>
                <th>Data:</th><td><?= $data ?></td>
                <th><a href="mostraXML.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>">XML</a></th>
                <th><a href="recuperarXML.php?id_projeto=<?= $id_project ?>&versao=<?= $version ?>&apaga=true">Apaga XML</a></th>

            </tr>


        </table>

    <?php
}
?>

    <br><i><a href="showSource.php?file=recuperarXML.php">Veja o c�digo fonte!</a></i>

</body>

</html>
