<?php

/**
 * File Name: ProjectDAO.php
 * Propuse: Function that interact with the database
 * 
 * PHP version 5
 */

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);

require_once '/../bd.inc';

/**
 * Function that select all attributes of a project
 * The selection occurs in table "projeto" database "cel"
 * 
 * @param string $projectName Represents the name of the project in database
 * 
 * @return bool|array
 * 
 */
function getProjectNameDatabase($projectName) {

    $querySelProjecName = "SELECT * FROM projeto 
        WHERE nome = '".$projectName."'";
    $executeQuery = mysql_query($querySelProjecName);
    $resultArray = mysql_fetch_array($executeQuery);

    return $resultArray;
}

/**
 * Function that select a "id_projeto" from database "cel"
 * The selection occurs in table participa
 * 
 * @param string $projectId Represents id of a project in database
 * int $userId Represents id of an user in database
 * 
 * @return bool|array
 * 
 */
function getProjectIdDatabase($projectId, $userId) {
    
    assert($userId>0);
    assert($projectId>0);

    $querySelParticipant = "SELECT * FROM participa 
        WHERE id_projeto = '".$projectId."'
        AND id_usuario = '$userId'";
    $executeQuery = mysql_query($querySelParticipant);
    $resultArray = mysql_fetch_array($executeQuery);

    return $resultArray;
}

/**
 * Function that select all atributtes of lexico from database
 * The selection occurs in table "lexico"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool|array 
 * 
 */
function selLexiconDatabase($projectId) {
    
    assert($projectId>0);

    $queryLexicon = "SELECT * FROM lexico 
        WHERE id_projeto = '".$projectId."'";
    $selectLexicon = mysql_query($queryLexicon);
    $arrayLexicon = mysql_fetch_array($selectLexicon);

    return $arrayLexicon;
}

/**
 * Function that select all atributtes of scenario from database
 * The selection occurs in table "cenario"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool|array 
 * 
 */
function selScenarioDatabase($projectId){
    
    assert($projectId>0);
    
    $querySelScenario = "SELECT * FROM cenario
        WHERE id_projeto = '".$projectId."'";
    $selectScenario = mysql_query($querySelScenario);
    $arrayScenario = mysql_fetch_array($selectScenario);
    
    return $arrayScenario;
}

/**
 * Function that delete a request of scenario from database using a specific id
 * The deletion occurs in table "pedidocen"
 * 
 * @param strign $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delRequestScenarioDatabase($projectId) {
    
    assert($projectId>0);

    $queryDelRequestScen = "DELETE FROM pedidocen
        WHERE id_pojeto='" . $projectId . "'";
    $deleteRequestScen = mysql_query($queryDelRequestScen);

    return $deleteRequestScen;
}

/**
 * Function that delete a request of lexicon from database using a specific id
 * The deletion occurs in table "pedidolex"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delRequestLexiconDatabase($projectId) {
    
    assert($projectId>0);

    $queryDelRequestLex = "DELETE FROM pedidolex 
        WHERE id_projeto='" . $projectId . "'";
    $deleteRequestLexicon = mysql_query($queryDelRequestLex);

    return $deleteRequestLexicon;
}

/**
 * Function that delete a lextolex from database using a specific id
 * The deletion occurs in table "lextolex"
 * 
 * @param string $idLexicon Represents id of a lexicon in database
 * 
 * @return bool
 * 
 */
function delLexToLexDatabase($idLexicon) {
    
    assert($idLexicon>0);
    
    $queryDelLexToLex = "Delete FROM lextolex
        WHERE id_lexico_from='" . $idLexicon . "'";
    $deleteLexToLex = mysql_query($queryDelLexToLex);

    return $deleteLexToLex;
}

/**
 * Function that delete a scentolex from database
 * The deletion occurs in table "centolex"
 * 
 * @param int $deleteId Represents id of a lexicon in database
 * 
 * @return none
 * 
 */
function delScenToLexDatabase($deleteId) {
    
    assert($deleteId>0);

    $queryScenToLex = "Delete FROM centolex 
        WHERE id_lexico = '" . $deleteId . "'";
    $deleteScenToLex = mysql_query($queryScenToLex);

    return $deleteScenToLex;
}

/**
 * Function that delete a project from database using a specific id
 * The deletion occurs in table "projeto"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delProjectDatabase($projectId) {
    
    assert($projectId>0);

    $queryDelProject = "Delete FROM projeto 
        WHERE id_projeto = '" . $projectId . "'";
    $deleteProject = mysql_query($queryDelProject);

    return $deleteProject;
}

/**
 * Function that delete a synonym from database using a specific id
 * The deletion occurs in table "sinonimo"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delSynonymDatabase($projectId){
    
    assert($projectId>0);
    
    $queryDelSynonym = "DELETE FROM sinonimo 
        WHERE id_projeto = '".$projectId."'";
    $deleteSynonym = mysql_query($queryDelSynonym);
    
    return $deleteSynonym;
}

/**
 * Function that delete a scenario from database using a specific id
 * The deletion occurs in table "cenario"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delScenarioDatabase($projectId){
    
    assert($projectId>0);
    
    $queryDelScenario = "DELETE FROM cenario 
        WHERE id_projeto = '".$projectId."'";
    $deleteScenario = mysql_query($queryDelScenario);
    
    return $deleteScenario;
}

/**
 * Function that delete a lexicon from database using a specific id
 * The deletion occurs in table "lexico"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delLexiconDatabase($projectId){
    
    assert($projectId>0);
    
    $queryDelLexicon = "DELETE FROM lexico 
        WHERE id_projeto = '".$projectId."'";
    $deleteLexicon = mysql_query($queryDelLexicon);
    
    return $deleteLexicon;
}

/**
 * Function that delete a scenario to scenario from database using a specific id
 * The deletion occurs in table "centocen"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delScenToScenDatabase($scenarioId){
    
    assert($scenarioId>0);
    
    $queryDelScenToScen = "DELETE FROM centocen
        WHERE id_cenario_from = '".$scenarioId."'";
    $delScenToScen = mysql_query($queryDelScenToScen);
    
    return $delScenToScen;
}

/**
 * Function that delete a participant from database using a specific id
 * The deletion occurs in table "participa"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delParticipantDatabase($projectId){
    
    assert($projectId>0);
    
    $queryDelParticipant = "DELETE FROM participa
        WHERE id_projeto = '".$projectId."'";
    $deleteParticipant = mysql_query($queryDelParticipant);
    
    return $deleteParticipant;
}

/**
 * Function that delete a publication from database using a specific id
 * The deletion occurs in table "publicacao"
 * 
 * @param string $projectId Represents id of a project in database
 * 
 * @return bool
 * 
 */
function delPublicationDatabase($projectId){
    
    assert($projectId>0);
    
    $queryDelPublication = "DELETE FROM publicacao
        WHERE id_projeto = '".$projectId."'";
    $deletePublication = mysql_query($queryDelPublication);
    
    return $deletePublication;
}
?>