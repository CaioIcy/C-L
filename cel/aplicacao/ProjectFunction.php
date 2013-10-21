<?php
/**
 * File name: ProjectFunction.php
 * Propuse: Function related to project
 */

require_once 'Dao/DaoProject.php';
require_once 'seguranca.php';

/*
 *Insere um projeto no banco de dados.
 *Recebe o nome e descricao. (1.1)
 *Verifica se este usuario ja possui um projeto com esse nome. (1.2)
 *Caso nao possua, insere os valores na tabela PROJETO. (1.3)
 *Devolve o id_cprojeto. (1.4)
 */

function includeProject($projectName, $projectDescription) {
    
    $projectArray = getProjectNameDatabase($projectName);

    if ($projectArray != false) {
        //verifica se o nome existente corresponde a um projeto que este usuario participa
        $id_projeto_repetido = $projectArray['id_projeto'];
        $idCurrentUser = $_SESSION['id_usuario_corrente'];
        
        $resultArray = getProjectIdDatabase($id_projeto_repetido, $idCurrentUser);

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
                  VALUES ($result[0],'" . prepara_dado($nome) . "','$data' , '" . prepara_dado($projectDescription) . "')";

    mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    return $result[0];
}

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

function removeProject($projectId) {
    
    $result = FALSE;
    $rmvRresult = rmvProjectDatabase($projectId);
    
    if($rmvRresult==1){
        $result = TRUE;
    } else {
        $result = FALSE;
    }
    
    return $result;
}
?>
