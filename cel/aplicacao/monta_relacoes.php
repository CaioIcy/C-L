<?php

include_once("monta_relacoes.php");
include_once("puts_links.php");
### MONTA AS RELACOES USADAS NO MENU LATERAL###

function make_relationship($id_projeto) {
    // Apaga todas as rela��es existentes das tabelas centocen, centolex e lextolex

    $DB = new PGDB ();
    $sql1 = new QUERY($DB);
    $sql2 = new QUERY($DB);
    $sql3 = new QUERY($DB);

    //$sql1->execute ("DELETE FROM centocen");
    //$sql2->execute ("DELETE FROM centolex") ;
    //$sql3->execute ("DELETE FROM lextolex") ;
    // Refaz as rela��es das tabelas centocen, centolex e lextolex
    //seleciona todos os cenarios

    $query = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $id_projeto
	          ORDER BY CHAR_LENGTH(titulo) DESC";
    $query_r = mysql_query($query) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($query_r)) { // Para todos os cenarios 
        $id_current_scenario = $result['id_cenario'];

        // Monta vetor com titulo dos cenarios

        $vetor_cenarios = load_vetor_cenario($id_projeto, $id_current_scenario);

        // Monta vetor com nome e sinonimos de todos os lexicos

        $vetor_lexicos = carrega_vetor_todos($id_projeto);

        // Ordena o vetor de lexico pela quantidade de palavaras do nome ou sinonimo

        quicksort($vetor_lexicos, 0, count($vetor_lexicos) - 1, 'lexico');

        // Ordena o vetor de cenarios pela quantidade de palavras do titulo

        quicksort($vetor_cenarios, 0, count($vetor_cenarios) - 1, 'cenario');



        $scene_title = $result['titulo'];
        $temporary_title = cenario_para_lexico($id_current_scenario, $scene_title, $vetor_lexicos);
        add_relationship($id_current_scenario, 'cenario', $temporary_title);



        $scene_goal = $result['objetivo'];
        $temporary_scene_goal = cenario_para_lexico($id_current_scenario, $scene_goal, $vetor_lexicos);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_goal);



        $scene_context = $result['contexto'];
        $temporary_scene_context = cenario_para_lexico_cenario_para_cenario($id_current_scenario, $$scene_context, $vetor_lexicos, $vetor_cenarios);
        add_relationship($id_current_scenario, 'cenario', $$temporary_scene_context);



        $scene_performer = $result['atores'];
        $temporary_scene_performer = cenario_para_lexico($id_current_scenario, $scene_performer, $vetor_lexicos);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_performer);



        $scene_resource = $result['recursos'];
        $temporary_scene_resource = cenario_para_lexico($id_current_scenario, $$scene_resource, $vetor_lexicos);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_resource);



        $scene_exception = $result['excecao'];
        $temporary_scene_exception = cenario_para_lexico($id_current_scenario, $$scene_exception, $vetor_lexicos);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_exception);



        $scene_episode = $result['episodios'];
        $temporary_scene_episode = cenario_para_lexico_cenario_para_cenario($id_current_scenario, $$scene_episode, $vetor_lexicos, $vetor_cenarios);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_episode);
    }

    // Seleciona todos os l�xicos

    $query = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $id_projeto
	          ORDER BY CHAR_LENGTH(nome) DESC";
    $query_r = mysql_query($query) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($query_r)) { // Para todos os lexicos
        $id_current_lexicon = $result['id_lexico'];

        // Monta vetor com nomes e sinonimos de todos os lexicos menos o lexico atual

        $vetor_lexicos = carrega_vetor($id_projeto, $id_current_lexicon);

        // Ordena o vetor de lexicos pela quantidade de palavaras do nome ou sinonimo
        quicksort($vetor_lexicos, 0, count($vetor_lexicos) - 1, 'lexico');



        $notion = $result['nocao'];
        $temporary_notion = lexico_to_lexico($id_lexico, $notion, $vetor_lexicos);
        add_relationship($id_current_lexicon, 'lexico', $temporary_notion);



        $impact = $result['impacto'];
        $temporary_impact = lexico_to_lexico($id_lexico, $impact, $vetor_lexicos);
        add_relationship($id_current_lexicon, 'lexico', $temporary_impact);
    }
}

// marca as rela��es de l�xicos para l�xicos

function lexico_to_lexico($id_lexico, $texto, $vetor_lexicos) {
    $i = 0;
    while ($i < count($vetor_lexicos)) {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1{l" . $vetor_lexicos[$i]->id_lexico . "**$2" . "}$3", $texto);
        $i++;
        // insere o relacionamento na tabela centolex
        //$query = "INSERT 
        //		INTO lextolex (id_lexico_from, id_lexico_to)
        //		VALUES ($id_lexico, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $texto;
}

// Marca as rela��es de cen�rios para l�xicos

function cenario_para_lexico($id_cenario, $texto, $vetor_lexicos) {
    $i = 0;
    while ($i < count($vetor_lexicos)) {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1{l" . $vetor_lexicos[$j]->id_lexico . "**$2" . "}$3", $texto);
        $i++;
        // insere o relacionamento na tabela centolex
        //$query = "INSERT 
        //		INTO centolex (id_cenario, id_lexico)
        //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $texto;
}

// Marca as rela��es de cen�rios para cen�rios

function cenario_para_cenario($id_cenario, $texto, $vetor_cenarios) {
    $i = 0;
    while ($i < count($vetor_cenarios)) {
        $regex = "/(\s|\b)(" . $vetor_cenarios[$i]->titulo . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1{c" . $vetor_cenarios[$j]->id_cenario . "**$2" . "}$3", $texto);
        $i++;
        // insere o relacionamento na tabela centolex
        //$query = "INSERT 
        //		INTO centolex (id_cenario, id_lexico)
        //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $texto;
}

// Marca as rela�oes de cen�rio para cen�rio e cen�rio para l�xico no mesmo texto

function cenario_para_lexico_cenario_para_cenario($id_cenario, $text, $vetor_lexicos, $vetor_cenarios) {
    $i = 0;
    $j = 0;
    $k = 0;
    $total = count($vetor_lexicos) + count($vetor_cenarios);
    while ($k < $total) {
        if (strlen($vetor_cenarios[$j]->titulo) < strlen($vetor_lexicos[$i]->nome)) {
            $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{l" . $vetor_lexicos[$i]->id_lexico . "**$2" . "}$3", $text);
            $i++;

            // insere o relacionamento na tabela centolex
            //$query = "INSERT 
            //		INTO centolex (id_cenario, id_lexico)
            //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
            //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        } else {
            $regex = "/(\s|\b)(" . $vetor_cenarios[$j]->titulo . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{c" . $vetor_cenarios[$j]->id_cenario . "**$2" . "}$3", $text);
            $j++;
        }
        $k++;
    }
    return $text;
}

// Fun��o que adiciona os relacionamentos nas tabelas centocen, centolex e lextolex
// Atraves da analise das marcas
// id_from id do l�xico ou cen�rio que referencia outro cen�rio ou l�xico
// $tipo_from tipo de quem esta referenciando ( se � l�xico ou cen�rio)

function add_relationship($id_from, $tipo_from, $texto) {
    $i = 0; // indice do texto com marcadores
    $parser = 0; // verifica quando devem ser adicionadas as tags

    $novo_texto = "";
    while ($i < strlen(&$texto)) {
        if ($texto[$i] == "{") {
            $parser++;
            if ($parser == 1) { //adiciona link ao texto - abrindo
                $id_to = "";
                $i++;
                $tipo = $texto[$i];
                $i++;
                while ($texto[$i] != "*") {
                    $id_to .= $texto[$i];
                    $i++;
                }
                if ($tipo == "l") {// Destino � um l�xico (id_lexico_to)
                    if (strcasecmp($tipo_from, 'lexico') == 0) {// Origem � um l�xico (id_lexico_from -> id_lexico_to)
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'l�xico para l�xico")</script>';
                        //adiciona rela��o de lexico para l�xico	
                    } else if (strcasecmp($tipo_from, 'cenario') == 0) {// Origem � um cen�rio (id_cenario -> id_lexico)
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para l�xico")</script>';
                        //adiciona rela��o de cen�rio para l�xico
                    }
                }
                if ($tipo == "c") {// Destino � um cen�rio (id_cenario_to)
                    if (strcasecmp($tipo_from, 'cenario') == 0) {// Origem � um cenario (id_cenario_from -> id_cenario_to)
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para cen�rio")</script>';
                        // Relacionamentos do tipo cen�rio para cen�rio
                        // Adiciona relacao de cenario para cenario na tabela centocen
                        //$q = "INSERT 
                        //		INTO centocen (id_cenario_from, id_cenario_to)
                        //		VALUES ($id_from, " . $vetor_cenarios[$j]->id_cenario . ")";
                        // mysql_query($q) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
                $i + 1;
            }
        } elseif ($texto[$i] == "}") {
            $parser--;
        }
        $i++;
    }
}

?>