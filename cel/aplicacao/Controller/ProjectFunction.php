<?php

/**
 * File name: ProjectFunction.php
 * Propuse: Function related to project
 */

require_once '/../Dao/ProjectDAO.php';
require_once '/../seguranca.php';

assert_options(ASSERT_ACTIVE, 1);

/**
 * Insert a project in database
 * 
 * @param string $projectName
 * @param string $projectDescription
 * @return projectId
 */
function includeProject($projectName, $projectDescription) {
    $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    //verifica se usuario ja existe
    $qv = "SELECT * FROM projeto WHERE nome = '$projectName'";
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
                  VALUES ($result[0],'" . safely_prepare_data($projectName) . "','$data' , '" . safely_prepare_data($projectDescription) . "')";

    mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    return $result[0];
}

/**
 * Function that delete a whole project and related
 * 
 * @param int $projectId
 */
function removeProject($projectId) {
    
    assert($projectId > 0);

    database_connect();
    
    $resultDelReqScen = delRequestScenarioDatabase($projectId);
    assert(($resultDelReqScen == TRUE) || ($resultDelReqScen == FALSE));

    $resultDelReqLex = delRequestLexiconDatabase($projectId);
    assert(($resultDelReqLex == TRUE) || ($resultDelReqLex == FALSE));

    $arrayLexicon = selLexiconDatabase($projectId);
    while ($arrayLexicon) {
        $lexiconId = $arrayLexicon['id_lexico']; // Select a lexico

        $resultDelLexToLex = delLexToLexDatabase($lexiconId);
        assert(($resultDelLexToLex == TRUE) || ($resultDelLexToLex == FALSE));
        
        $resultDelScenToLex = delScenToLexDatabase($lexiconId);
        assert(($resultDelScenToLex == TRUE) || ($resultDelScenToLex == FALSE));
        
        $resultDelSynonym = delSynonymDatabase($projectId);
        assert(($resultDelSynonym == TRUE) || ($resultDelSynonym == FALSE));
    }
    
    $resultDelLexicon = delLexiconDatabase($projectId);
    assert(($resultDelLexicon == TRUE) || ($resultDelLexicon == FALSE));
    
    $arrayScenario = selScenarioDatabase($projectId);
    while($arrayScenario){
        $scenarioId = $arrayScenario['id_cenario']; // Select a scenario
        
        $resultDelScenToScen = delScenToScenDatabase($scenarioId);
        assert(($resultDelScenToScen == TRUE) || ($resultDelScenToScen == FALSE));
        
        $resultDelScenLex = delScenToLexDatabase($scenarioId);
        assert(($resultDelScenLex == TRUE) || ($resultDelScenLex == FALSE));
    }
    
    $resultDelScenario = delScenarioDatabase($projectId);
    assert(($resultDelScenario == TRUE) || ($resultDelScenario == FALSE));
    
    $resultDelParticipant = delParticipantDatabase($projectId);
    assert(($resultDelParticipant == TRUE) || ($resultDelParticipant == FALSE));
    
    $resultDelPublication = delPublicationDatabase($projectId);
    assert(($resultDelPublication == TRUE) || ($resultDelPublication == FALSE));
    
    $resultDelProject = delProjectDatabase($projectId);
    assert(($resultDelProject == TRUE) || ($resultDelProject == FALSE));
    
}
?>
