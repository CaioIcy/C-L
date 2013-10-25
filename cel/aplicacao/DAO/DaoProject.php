<?php

require_once '/../bd.inc';

define("QUERY_SEL_PARTICIPANT");
define("QUERY_SEL_PROJECTNAME");
define("QUERY_DEL_REQUESTSCENARIO");
define("QUERY_DEL_REQUESTLEXICON");
define("QUERY_SEL_LEXICON");
define("QUERY_DEL_LEXICON");
define("QUERY_SEL_SCENARIO");
define();

function getProjectNameDatabase($projectName){
    
    database_connect();
    
    $query = "SELECT * FROM projeto WHERE nome = '$projectName'";
    $executeQuery = mysql_query($query);
    $resultArray = mysql_fetch_array($executeQuery);
    
    return $resultArray;
}

function getProjectIdDatabase($projectId,$userId){
    
    database_connect();
    
    $query = "SELECT * FROM participa WHERE id_projeto = '$projectId' AND id_usuario = '$userId'";
    $executeQuery = mysql_query($query);
    $resultArray = mysql_fetch_array($executeQuery);
    
    return $resultArray;
    
}

function rmvProjectDatabase($projectId){
    
    database_connect();
    
    $result = 0;
    
    $queryDelRequestScenario = "Delete FROM pedidocen  WHERE id_pojeto = '$projectId'";
    $deleteRequestSceneario = mysql_query($queryDelRequestScenario);
    if($deleteRequestSceneario){
        $result = 1;
    }  else {
        $result = 2;
    }
    
    $queryDelRequestLexicon = "Delete FROM pedidolex WHERE id_projeto = '$projectId'";
    $deleteRequestLexicon = mysql_query($queryDelRequestLexicon);
    if($deleteRequestLexicon){
        $result = 1;
    } else {
        $result = 2;
    }
    
    $queryLexicon = "SELECT * FROM lexico WHERE id_projeto = '$projectId'";
    $selectLexicon = mysql_query($queryLexicon);
    $arrayLexicon = mysql_fetch_array($selectLexicon);
    
    while ($arrayLexicon){
        
        $projectId = $arrayLexicon['id_lexico'];
        
        $queryDelLexToLex = "Delete FROM lextolex WHERE id_lexico_from = '$projectId'";
        $deleteLexToLex = mysql_query($queryDelLexToLex);
        if($deleteLexToLex){
            $result = 1;
        } else {
            $result = 2;
        }
        
        $queryDelScenToLex = "Delete FROM centolex WHERE id_lexico = '$projectId'";
        $deleteScenToLex = mysql_query($queryDelScenToLex);
        if($deleteScenToLex){
            $result = 1;
        } else {
            $result = 2;
        }
        
        $queryDelSynonym = "Delete FROM sinonimo WHERE id_projeto = '$projectId'";
        $deleteSynonym = mysql_query($queryDelSynonym);
        if($deleteSynonym){
            $result = 1;
        } else {
            $result = 2;
        }
        
    }
    
    $queryDelLexicon = "Delete FROM lexico WHERE id_projeto = '$projectId'";
    $deleteLexicon = mysql_query($queryDelLexicon);
    if($deleteLexicon){
        $result = 1;
    } else {
        $result = 2;
    }
    
    $queryScenario = "SELECT * FROM cenario WHERE id_projeto = '$projectId'";
    $selectScenario = mysql_query($queryScenario);
    $arrayScenario = mysql_fetch_array($selectScenario);
    
    while ($arrayScenario){
        
        $lexiconId = $arrayScenario['id_cenario'];
        
        $queryDelScenToScen = "Delete FROM centocen WHERE id_cenario_from = '$projectId'";
        $deleteScenToScen = mysql_query($queryDelScenToScen);
        if($deleteScenToScen){
            $result = 1;
        } else {
            $result = 2;
        }
        
        $queryDelScenToLex = "Delete FROM centolex WHERE id_cenario_from = '$projectId'";
        $deleteScenToLex = mysql_query($queryDelScenToLex);
        if($deleteScenToLex){
            $result = 1;
        }  else {
            $result = 2;
        }
    }
    
    $queryDelScenario = "Delete FROM cenario WHERE id_projeto = '$projectId'";
    $deleteScenario = mysql_query($queryDelScenario);
    if($deleteScenario){
        $result = 1;
    }  else {
        $result = 2;
    }
    
    $queryDelParticipant = "Delete FROM participa WHERE id_projeto = '$projectId";
    $deleteParticipant = mysql_query($queryDelParticipant);
    if($deleteParticipant){
        $result = 1;
    } else {
        $result = 2;
    }
    
    $queryDelPublication = "Delete FROM publicacao WHERE id_projeto";
    $deletePublication = mysql_query($queryDelPublication);
    if($deletePublication){
        $result = 1;
    }  else {
        $result = 2;
    }
    
    $queryDelProject = "Delete FROM projeto WHERE id_projeto = '$projectId'";
    $deleteProject = mysql_query($queryDelProject);
    if($deleteProject){
        $result = 1;
    }  else {
        $result = 2;
    }
    
    return $result;
}
?>