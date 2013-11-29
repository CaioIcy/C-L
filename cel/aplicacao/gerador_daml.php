<?php

session_start();

include_once 'save_ontology_to_daml.php';
include_once 'database_support.php';
include_once 'bd.inc';

$database_conection = database_connect();

if ($_POST['user'] == "") {
// Recupera nome do usu�rio 
    $sql_user = "select nome from usuario where id_usuario='" . $_SESSION['id_usuario_corrente'] . "';";
    $query_user = mysql_query($sql_user) or die("Erro ao verificar usu�rio!" . mysql_error());
    $result = mysql_fetch_array($query_user);
    $user = $result[0];
} else {
    $user = $_POST['user'];
}

// Recupera nome do projeto 
$sql_project = "select nome from projeto where id_projeto='" . $_SESSION['id_projeto_corrente'] . "';";
$query_project = mysql_query($sql_project) or die("Erro ao verificar usu�rio!" . mysql_error());
$result = mysql_fetch_array($query_project);
$project = $result[0];

$site = $_SESSION['site'];
$actual_diretory = $_SESSION['diretorio'];
$file = strtr($project, "������", "aaaooo") . "__" . date("j-m-Y_H-i-s") . ".daml";

$i = array("title" => $_POST['title'],
    "creator" => $user,
    "description" => $_POST['description'],
    "subject" => $_POST['subject'],
    "versionInfo" => $_POST['versionInfo']);

$_SESSION['id_projeto'] = $_SESSION['id_projeto_corrente'];
$list_concepts = get_conceptList();
$list_relations = get_relationList();
$list_axioms = get_lista_de_axiomas();

$daml = save_daml($site, $actual_diretory, $file, $i, $list_concepts, $list_relations, $list_axioms);

mysql_close($database_conection);
?>   

<html> 
    <head><title>Gerar DAML</title></head> 
    <body bgcolor="#FFFFFF"> 

        <?php
        if (!$daml) {
            print 'Erro ao exportar ontologia para DAML!';
        } else {

            print 'Ontologia exportada para DAML com sucesso! <br>';
            print 'Arquivo criado: ';
            print "<a href=\"$site$daml\">$daml</a>";
        }
        ?>  

    </body> 
</html> 