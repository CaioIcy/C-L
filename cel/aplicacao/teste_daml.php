<?php

//include 'auxiliar_daml.php';
include_once 'bd.inc';
include_once 'database_support.php';
include_once 'CELConfig/CELConfig.inc';
include_once 'save_ontology_to_daml.php';

$database_conection = database_connect();

$site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$dir = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$arquivo = nome_arquivo_daml();

$i = array("title" => "Ontologia de teste",
    "creator" => "Pedro",
    "description" => "teste de tradução de léxico para ontologia",
    "subject" => "",
    "versionInfo" => "1.1");

$lista_conceitos = get_lista_de_conceitos();
$lista_relacoes = get_relationList();
$lista_axiomas = get_lista_de_axiomas();


$daml = save_daml($site, $dir, $arquivo, $i, $lista_conceitos, $lista_relacoes, $lista_axiomas);

if (!$daml) {
    print 'Erro ao exportar ontologia para DAML!';
} else {
    print 'Ontologia exportada para DAML com sucesso! <br>';
    print 'Arquivo criado: ';
    print "<a href=\"$site$daml\">$daml</a>";
}


mysql_close($database_conection);
?>
