<?php

require_once '/../bd.inc';

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
        $result = 0;
    }
    
    $queryDelRequestLexicon = "Delete FROM pedidolex WHERE id_projeto = '$projectId'";
    $deleteRequestLexicon = mysql_query($queryDelRequestLexicon);
    if($deleteRequestLexicon){
        $result = 1;
    } else {
        $result = 0;
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
            $result = 0;
        }
        
        $queryDelScenToLex = "Delete FROM centolex WHERE id_lexico = '$projectId'";
        $deleteScenToLex = mysql_query($queryDelScenToLex);
        if($deleteScenToLex){
            $result = 1;
        } else {
            $result = 0;
        }
        
        $queryDelSynonym = "Delete FROM sinonimo WHERE id_projeto = '$projectId'";
        $deleteSynonym = mysql_query($queryDelSynonym);
        if($deleteSynonym){
            $result = 1;
        } else {
            $result = 0;
        }
        
    }
    
    $queryDelLexicon = "Delete FROM lexico WHERE id_projeto = '$projectId'";
    $deleteLexicon = mysql_query($queryDelLexicon);
    if($deleteLexicon){
        $result = 1;
    } else {
        $result = 0;
    }
    
    $queryScenario = "SELECT * FROM cenario WHERE id_projeto = '$projectId'";
    $selectScenario = mysql_query($queryScenario);
    $arrayScenario = mysql_fetch_array($selectScenario);
    
    while ($arrayScenario){
        
    }
}
?>
