<?php

/**
 * File Name: DaoProject.php
 * Propuse: Function that interact with the database
 * 
 * PHP version 5
 */
require_once '/../bd.inc';

function removeProjectInDatabase($projectId) {

    database_connect();
    
    $resultDelReqScen = delRequestScenarioDatabase($projectId);
    $resultDelReqLex = delRequestLexiconDatabase($projectId);
    
    $resultSelLex = selLexiconDatabase($projectId);
    while ($resultSelLex){
        $id_lexicon = $resultSelLex['id_lexico'];
        
        $resultDelLexToLex = delLexToLexDatabase($id_lexicon);
        $resultDelScenToLex = delScenToLexDatabase($id_lexicon);
        
    }
    
    
}

/**
 * Function that select all attributes of a project
 * The selection occurs in table "projeto" database "cel"
 * 
 * @param string $projectName
 * @return bool|array
 */
function getProjectNameDatabase($projectName) {

    $querySelProjecName = "SELECT * FROM projeto WHERE nome = '$projectName'";
    $executeQuery = mysql_query($querySelProjecName);
    $resultArray = mysql_fetch_array($executeQuery);

    return $resultArray;
}

/**
 * Function that select a "id_projeto" from database "cel"
 * The selection occurs in table participa
 * 
 * @param string $projectId
 * @param int $userId
 * @return bool|array
 */
function getProjectIdDatabase($projectId, $userId) {

    $querySelParticipant = "SELECT * FROM participa WHERE id_projeto = '$projectId'
        AND id_usuario = '$userId'";
    $executeQuery = mysql_query($querySelParticipant);
    $resultArray = mysql_fetch_array($executeQuery);

    return $resultArray;
}

/**
 * Function that select all atributtes of lexico from database
 * The selection occurs in table "lexico"
 * 
 * @param string $projectId
 * @return bool|array 
 */
function selLexiconDatabase($projectId) {

    $queryLexicon = "SELECT * FROM lexico WHERE id_projeto = '$projectId'";
    $selectLexicon = mysql_query($queryLexicon);
    $arrayLexicon = mysql_fetch_array($selectLexicon);

    return $arrayLexicon;
}

/**
 * Function that delete a request of scenario from database using a specific id
 * The deletion occurs in table "pedidocen"
 * 
 * @param strign $projectId
 * @return bool
 */
function delRequestScenarioDatabase($projectId) {

    $queryDelRequestScen = "DELETE FROM pedidocen WHERE id_pojeto=" . $projectId . "";
    $deleteRequestScen = mysql_query($queryDelRequestScen);

    return $deleteRequestScen;
}

/**
 * Function that delete a request of lexicon from database using a specific id
 * The deletion occurs in table "pedidolex"
 * 
 * @param string $projectId
 * @return bool
 */
function delRequestLexiconDatabase($projectId) {

    $queryDelRequestLex = "DELETE FROM pedidolex WHERE id_projeto=" . $projectId . "";
    $deleteRequestLexicon = mysql_query($queryDelRequestLex);

    return $deleteRequestLexicon;
}

/**
 * Function that delete a lextolex from database using a specific id
 * The deletion occurs in table "lextolex"
 * 
 * @param string $idLexicon
 * @return bool
 */
function delLexToLexDatabase($idLexicon) {

    $queryDelLexToLex = "Delete FROM lextolex WHERE id_lexico_from=" . $idLexicon . "";
    $deleteLexToLex = mysql_query($queryDelLexToLex);

    return $deleteLexToLex;
}

/**
 * Function that delete a scentolex from database
 * The deletion occurs in table "centolex"
 * 
 * @param int $idLexicon
 */
function delScenToLexDatabase($idLexicon) {
    
    $queryScenToLex = "Delete FROM centolex WHERE id_lexico = ".$idLexicon."";
    $deleteScenToLex = mysql_query($queryScenToLex);
    
    return $deleteScenToLex;
}

/**
 * Function that delete a project from database using a specific id
 * The deletion occurs in table "projeto"
 * 
 * @param string $projectId
 * @return bool
 */
function delProjectDatabase($projectId) {

    $queryDelProject = "Delete FROM projeto WHERE id_projeto = " . $projectId . "";
    $deleteProject = mysql_query($queryDelProject);

    return $deleteProject;
}

?>