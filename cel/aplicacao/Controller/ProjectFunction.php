<?php

/**
 * File name: ProjectFunction.php
 * Propuse: Function related to project
 */

require_once '/../Dao/DaoProject.php';
require_once '/../seguranca.php';

assert_options(ASSERT_ACTIVE, 1);

/**
 * Insert a project in database
 * @param string $projectName
 * @param string $projectDescription
 * @return projectId
 */
function inclui_projeto($nome, $descricao) {
    $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    //verifica se usuario ja existe
    $qv = "SELECT * FROM projeto WHERE nome = '$nome'";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //$result = mysql_fetch_row($qvr);
    $resultArray = mysql_fetch_array($qvr);


    if ($resultArray != false) {
        //verifica se o nome existente corresponde a um projeto que este usuario participa
        $id_projeto_repetido = $resultArray['id_projeto'];

        $id_usuario_corrente = $_SESSION['id_usuario_corrente'];

        $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' AND id_usuario = '$id_usuario_corrente' ";

        $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $resultArray = mysql_fetch_row($qvuv);

        if ($resultArray[0] != null) {
            return -1;
        }
    }

    $q = "SELECT MAX(id_projeto) FROM projeto";
    $qrr = mysql_query($q) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $result = mysql_fetch_row($qrr);

    if ($result[0] == false) {
        $result[0] = 1;
    } else {
        $result[0]++;
    }
    $data = date("Y-m-d");

    $qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'" . safely_prepare_data($nome) . "','$data' , '" . safely_prepare_data($descricao) . "')";

    mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    return $result[0];
}

/*
###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################
*/

function removeProjeto($projectId) {
    
    assert($projectId > 0);

    database_connect();

    $resultDelReqScen = delRequestScenarioDatabase($projectId);

    assert(($resultDelReqScen == TRUE) || ($resultDelReqScen == FALSE));

    $resultDelReqLex = delRequestLexiconDatabase($projectId);

    assert(($resultDelReqLex == TRUE) || ($resultDelReqLex == FALSE));

    $resultSelLex = selLexiconDatabase($projectId);
    while ($resultSelLex) {
        $id_lexicon = $resultSelLex['id_lexico'];

        $resultDelLexToLex = delLexToLexDatabase($id_lexicon);
        $resultDelScenToLex = delScenToLexDatabase($id_lexicon);
    }
}
?>
