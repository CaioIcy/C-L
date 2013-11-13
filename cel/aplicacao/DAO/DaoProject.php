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

    $result = 0;

    $queryDelRequestScenario = "Delete FROM pedidocen  WHERE id_pojeto = '$projectId'";
    $deleteRequestSceneario = mysql_query($queryDelRequestScenario);
    if ($deleteRequestSceneario) {
        $result = 1;
    } else {
        $result = 2;
    }

    $queryDelRequestLexicon = "Delete FROM pedidolex WHERE id_projeto = '$projectId'";
    $deleteRequestLexicon = mysql_query($queryDelRequestLexicon);
    if ($deleteRequestLexicon) {
        $result = 1;
    } else {
        $result = 2;
    }

    $queryLexicon = "SELECT * FROM lexico WHERE id_projeto = '$projectId'";
    $selectLexicon = mysql_query($queryLexicon);
    $arrayLexicon = mysql_fetch_array($selectLexicon);

    while ($arrayLexicon) {

        $projectId = $arrayLexicon['id_lexico'];

        $queryDelLexToLex = "Delete FROM lextolex WHERE id_lexico_from = '$projectId'";
        $deleteLexToLex = mysql_query($queryDelLexToLex);
        if ($deleteLexToLex) {
            $result = 1;
        } else {
            $result = 2;
        }

        $queryDelScenToLex = "Delete FROM centolex WHERE id_lexico = '$projectId'";
        $deleteScenToLex = mysql_query($queryDelScenToLex);
        if ($deleteScenToLex) {
            $result = 1;
        } else {
            $result = 2;
        }

        $queryDelSynonym = "Delete FROM sinonimo WHERE id_projeto = '$projectId'";
        $deleteSynonym = mysql_query($queryDelSynonym);
        if ($deleteSynonym) {
            $result = 1;
        } else {
            $result = 2;
        }
    }

    $queryDelLexicon = "Delete FROM lexico WHERE id_projeto = '$projectId'";
    $deleteLexicon = mysql_query($queryDelLexicon);
    if ($deleteLexicon) {
        $result = 1;
    } else {
        $result = 2;
    }

    $queryScenario = "SELECT * FROM cenario WHERE id_projeto = '$projectId'";
    $selectScenario = mysql_query($queryScenario);
    $arrayScenario = mysql_fetch_array($selectScenario);

    while ($arrayScenario) {

        $lexiconId = $arrayScenario['id_cenario'];

        $queryDelScenToScen = "Delete FROM centocen WHERE id_cenario_from = '$lexiconId'";
        $deleteScenToScen = mysql_query($queryDelScenToScen);
        if ($deleteScenToScen) {
            $result = 1;
        } else {
            $result = 2;
        }

        $queryDelScenToLex = "Delete FROM centolex WHERE id_cenario_from = '$projectId'";
        $deleteScenToLex = mysql_query($queryDelScenToLex);
        if ($deleteScenToLex) {
            $result = 1;
        } else {
            $result = 2;
        }
    }

    $queryDelScenario = "Delete FROM cenario WHERE id_projeto = '$projectId'";
    $deleteScenario = mysql_query($queryDelScenario);
    if ($deleteScenario) {
        $result = 1;
    } else {
        $result = 2;
    }

    $queryDelParticipant = "Delete FROM participa WHERE id_projeto = '$projectId";
    $deleteParticipant = mysql_query($queryDelParticipant);
    if ($deleteParticipant) {
        $result = 1;
    } else {
        $result = 2;
    }

    $queryDelPublication = "Delete FROM publicacao WHERE id_projeto";
    $deletePublication = mysql_query($queryDelPublication);
    if ($deletePublication) {
        $result = 1;
    } else {
        $result = 2;
    }

    $queryDelProject = "Delete FROM projeto WHERE id_projeto = '$projectId'";
    $deleteProject = mysql_query($queryDelProject);
    if ($deleteProject) {
        $result = 1;
    } else {
        $result = 2;
    }

    return $result;
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
function getLexiconDatabase($projectId) {

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

    $queryDelRequestScen = "DELETE FROM pedidocen WHERE id_pojeto=".$projectId."";
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

    $queryDelRequestLex = "DELETE FROM pedidolex WHERE id_projeto=".$projectId."";
    $deleteRequestLexicon = mysql_query($queryDelRequestLex);

    return $deleteRequestLexicon;
}

/**
 * Function that delete a lextolex from database using a specific id
 * The deletion occurs in table "lextolex"
 * 
 * @param string $projectId
 * @return bool
 */
function delLexToLexDatabase($projectId) {

    $arrayLexicon = selLexicon($projectId);
    $projectId = $arrayLexicon['id_lexico'];

    $queryDelLexToLex = "Delete FROM lextolex WHERE id_lexico_from=".$projectId."";
    $deleteLexToLex = mysql_query($queryDelLexToLex);

    return $deleteLexToLex;
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