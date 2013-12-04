<?php

/*
 * File: monta_relacoes.php
 * Purpose: 
 * 
 * Used in the side menu.
 */

include_once 'monta_relacoes.php';
include_once 'puts_links.php';

/*
 * Erases all the relations on the tables centocen, centolex e lextolex
 */

function erase_relations($id_project) {
    $database = new PGDB ();
    $query_centocen = new QUERY($database);
    $query_centolex = new QUERY($database);
    $query_lextolex = new QUERY($database);

    $query_centocen->execute("DELETE FROM centocen");
    $query_centolex->execute("DELETE FROM centolex");
    $query_lextolex->execute("DELETE FROM lextolex");

    $query_scenarios = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $id_project
	          ORDER BY CHAR_LENGTH(titulo) DESC";
    $query_scenarios_result = mysql_query($query_scenarios) or die("Error while sending query.");

    while ($result = mysql_fetch_array($query_scenarios_result)) {
        $id_current_scenario = $result['id_cenario'];

        $scenario_vector = load_scenario_vector($id_project, $id_current_scenario);
        quicksort($scenario_vector, 0, count($scenario_vector) - 1, 'cenario');

        $lexicon_vector = carrega_vetor_todos($id_project);
        quicksort($lexicon_vector, 0, count($lexicon_vector) - 1, 'lexico');

        $scenario_title = $result['titulo'];
        $temporary_scenario_title = mark_relations_centolex($id_current_scenario, $scenario_title, $lexicon_vector);
        add_relationship($id_current_scenario, 'cenario', $temporary_scenario_title);

        $scenario_goal = $result['objetivo'];
        $temporary_scenario_goal = mark_relations_centolex($id_current_scenario, $scenario_goal, $lexicon_vector);
        add_relationship($id_current_scenario, 'cenario', $temporary_scenario_goal);

        $scenario_context = $result['contexto'];
        $temporary_scenario_context = mark_relations_centolex_centocen($id_current_scenario, $$scenario_context, $lexicon_vector, $scenario_vector);
        add_relationship($id_current_scenario, 'cenario', $$temporary_scenario_context);

        $scenario_actor = $result['atores'];
        $temporary_scenario_actor = mark_relations_centolex($id_current_scenario, $scenario_actor, $lexicon_vector);
        add_relationship($id_current_scenario, 'cenario', $temporary_scenario_actor);

        $scenario_resource = $result['recursos'];
        $temporary_scene_resource = mark_relations_centolex($id_current_scenario, $$scenario_resource, $lexicon_vector);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_resource);

        $scenario_exception = $result['excecao'];
        $temporary_scenario_exception = mark_relations_centolex($id_current_scenario, $$scenario_exception, $lexicon_vector);
        add_relationship($id_current_scenario, 'cenario', $temporary_scenario_exception);

        $scenario_episode = $result['episodios'];
        $temporary_scene_episode = mark_relations_centolex_centocen($id_current_scenario, $$scenario_episode, $lexicon_vector, $scenario_vector);
        add_relationship($id_current_scenario, 'cenario', $temporary_scene_episode);
    }

    $query_lexicon = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $id_project
	          ORDER BY CHAR_LENGTH(nome) DESC";
    $query_lexicon_result = mysql_query($query_lexicon) or die("Error while sending query.");

    while ($result = mysql_fetch_array($query_lexicon_result)) {
        $id_current_lexicon = $result['id_lexico'];

        // Vector with all the lexicon names and synonyms, except the current lexicon
        $lexicon_vector = carrega_vetor($id_project, $id_current_lexicon);
        quicksort($lexicon_vector, 0, count($lexicon_vector) - 1, 'lexico');

        $lexicon_notion = $result['nocao'];
        $temporary_lexicon_notion = mark_relations_lextolex($id_current_lexicon, $lexicon_notion, $lexicon_vector);
        add_relationship($id_current_lexicon, 'lexico', $temporary_lexicon_notion);

        $lexicon_impact = $result['impacto'];
        $temporary_lexicon_impact = mark_relations_lextolex($id_current_lexicon, $lexicon_impact, $lexicon_vector);
        add_relationship($id_current_lexicon, 'lexico', $temporary_lexicon_impact);
    }
}

function mark_relations_lextolex($id_lexicon, $text, $lexicon_vector) {
    $i = 0;
    while ($i < count($lexicon_vector)) {
        $regex = "/(\s|\b)(" . $lexicon_vector[$i]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $lexicon_vector[$i]->id_lexico . "**$2" . "}$3", $text);
        $i++;

        //Insert the relation in the lextolex table
        $query = "INSERT 
       		INTO lextolex (id_lexico_from, id_lexico_to)
        		VALUES ($id_lexicon, " . $lexicon_vector[$i]->id_lexico . ")";
        mysql_query($query) or die("Error while sending query: " . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }
    return $text;
}

function mark_relations_centolex($id_scenario, $text, $lexicon_vector) {
    $i = 0;
    while ($i < count($lexicon_vector)) {
        $regex = "/(\s|\b)(" . $lexicon_vector[$i]->nome . ")(\s|\b)/i";
        $$text = preg_replace($regex, "$1{l" . $lexicon_vector[$j]->id_lexico . "**$2" . "}$3", $$text);
        $i++;
        //Insert the relation in the centolex table
        $query = "INSERT 
        		INTO centolex (id_cenario, id_lexico)
        		VALUES ($id_scenario, " . $lexicon_vector[$i]->id_lexico . ")";
        mysql_query($query) or die("Error while sending query: " . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }
    return $text;
}

function mark_relations_centocen($id_scenario, $text, $scenario_vector) {
    $i = 0;
    while ($i < count($scenario_vector)) {
        $regex = "/(\s|\b)(" . $scenario_vector[$i]->titulo . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{c" . $scenario_vector[$j]->id_cenario . "**$2" . "}$3", $text);
        $i++;
        // Insert the relation in the centocen table
        $query = "INSERT 
        		INTO centolex (id_cenario, id_lexico)
        		VALUES ($id_scenario, " . $scenario_vector[$i]->id_cenario . ")";
        mysql_query($query) or die("Error while sending query: " . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }
    return $text;
}

function mark_relations_centolex_centocen($id_scenario, $text, $lexicon_vector, $scenario_vector) {
    $i = 0;
    $j = 0;
    $k = 0;
    $total = count($lexicon_vector) + count($scenario_vector);
    while ($k < $total) {
        if (strlen($scenario_vector[$j]->titulo) < strlen($lexicon_vector[$i]->nome)) {
            $regex = "/(\s|\b)(" . $lexicon_vector[$i]->nome . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{l" . $lexicon_vector[$i]->id_lexico . "**$2" . "}$3", $text);
            $i++;

            // Insert the relation in the centolex table
            $query = "INSERT 
            		INTO centolex (id_cenario, id_lexico)
            		VALUES ($id_scenario, " . $lexicon_vector[$i]->id_lexico . ")";
            mysql_query($query) or die("Error while sending query: " . mysql_error() . "<br>" . __FILE__ . __LINE__);
        } else {
            $regex = "/(\s|\b)(" . $scenario_vector[$j]->titulo . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{c" . $scenario_vector[$j]->id_cenario . "**$2" . "}$3", $text);
            $j++;
        }
        $k++;
    }
    return $text;
}

/*
 * Adds the relations on the tables centocen, centolex and lextolex
 *  through mark analysis
 * 
 * Parameteres:
 *      id_from: lexicon/scenario id that references another scenario or lexicon
 *      type_from: type of whos referencing (lexicon or scenario)
 */

function add_relationship($id_from, $type_from, $text) {
    $i = 0;
    $parser = 0;

    $novo_texto = "";
    while ($i < strlen(&$text)) {
        if ($text[$i] == "{") {
            $parser++;
            if ($parser == 1) { //adding link to text (opening)
                $id_to = "";
                $i++;
                $tipo = $text[$i];
                $i++;
                while ($text[$i] != "*") {
                    $id_to .= $text[$i];
                    $i++;
                }
                if ($tipo == "l") { // Destination is lexicon (id_lexico_to)
                    if (strcasecmp($type_from, 'lexico') == 0) { //Originates from a lexicon
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'l�xico para l�xico")</script>';
                        // add relation lexicon to lexicon	
                    } else if (strcasecmp($type_from, 'cenario') == 0) {// Originates from a scenario
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para l�xico")</script>';
                        // add relation scenario to lexicon
                    }
                }
                if ($tipo == "c") {// Destination is scenario (id_cenario_to)
                    if (strcasecmp($type_from, 'cenario') == 0) {// Originates from a scenario
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para cen�rio")</script>';

                        //Insert the relation in the centocen table
                        $query = "INSERT 
                        		INTO centocen (id_cenario_from, id_cenario_to)
                        		VALUES ($id_from, " . $scenario_vector[$j]->id_cenario . ")";
                        mysql_query($query) or die("Error while sending query: " . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
                $i + 1;
            }
        } elseif ($text[$i] == "}") {
            $parser--;
        }
        $i++;
    }
}

?>