<?php

/*
 * File: puts_links.php
 * Purpose: 
 */

/*
 * To prevent the current lexicon to create a link to itself.
 * Function that loads the vector with all the titles of the lexicons and their
 * synyonyms EXCEPT the ones passed through $id_current_lexicon.
 */

function load_lexicon_vector($id_project, $id_current_lexicon, $no_current) {
    $lexicon_vector = array();
    if ($no_current) {
        $query_lexicon = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_project' AND id_lexico <> '$id_current_lexicon' 
							ORDER BY nome DESC";

        $query_synonym = "SELECT id_lexico, nome 
							FROM sinonimo
							WHERE id_projeto = '$id_project' AND id_lexico <> '$id_current_lexicon' 
							ORDER BY nome DESC";
    } else {

        $query_lexicon = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_project' 
							ORDER BY nome DESC";

        $query_synonym = "SELECT id_lexico, nome    
							FROM sinonimo
							WHERE id_projeto = '$id_project' ORDER BY nome DESC";
    }

    $query_lexicon_result = mysql_query($query_lexicon) or die("Error while sending the selection query in the lexicon table!" . mysql_error());

    $i = 0;
    while ($lexicon_line = mysql_fetch_object($query_lexicon_result)) {
        $lexicon_vector[$i] = $lexicon_line;
        $i++;
    }

    $query_synonym_result = mysql_query($query_synonym) or die("Error while sending the selection query in the synonym table!" . mysql_error());
    while ($synonym_line = mysql_fetch_object($query_synonym_result)) {
        $lexicon_vector[$i] = $synonym_line;
        $i++;
    }
    return $lexicon_vector;
}

/*
 * To prevent the current scenario to create a link to itself.
 * Function that loads the vector with all the titles of the scenarios
 * EXCEPT the ones passed through $id_current_scenario.
 */

function load_scenario_vector($id_project, $id_current_scenario, $no_current) {
    if (!isset($scenario_vector)) {
        $scenario_vector = array();
    }
    if ($no_current) {
        $query_scenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_project' AND id_cenario <> '$id_current_scenario' 
							ORDER BY titulo DESC";
    } else {
        $query_scenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_project' 
							ORDER BY titulo DESC";
    }

    $query_scenarios_result = mysql_query($query_scenarios) or die("Error while sending the selection query" . mysql_error());

    $i = 0;
    while ($scenario_line = mysql_fetch_object($query_scenarios_result)) {
        $scenario_vector[$i] = $scenario_line;
        $i++;
    }

    return $scenario_vector;
}

function split_array_in_two(&$vector, $start, $finish, $type) {
    $i = $start;
    $j = $finish;
    $dir = 1;

    while ($i < $j) {
        if (strcasecmp($type, 'cenario') == 0) {
            if (strlen($vector[$i]->titulo) < strlen($vector[$j]->titulo)) {
                $temporary_string = $vector[$i];
                $vector[$i] = $vector[$j];
                $vector[$j] = $temporary_string;
                $dir--;
            }
        } else {
            if (strlen($vector[$i]->nome) < strlen($vector[$j]->nome)) {
                $temporary_string = $vector[$i];
                $vector[$i] = $vector[$j];
                $vector[$j] = $temporary_string;
                $dir--;
            }
        }
        if ($dir == 1) {
            $j--;
        } else {
            $i++;
        }
    }

    return $i;
}

function quicksort(&$vector, $start, $finish, $type) {
    if ($start < $finish) {
        $k = split_array_in_two($vector, $start, $finish, $type);
        quicksort($vector, $start, $k - 1, $type);
        quicksort($vector, $k + 1, $finish, $type);
    }
}

/*
 * Function that builds the links according to the text, passed through the
 * parameter $text. Leixcons, passed through $lexicon_vector. And scenarios,
 * passed through $scenarios_vector
 */

function build_links($text, $lexicon_vector, $scenarios_vector) {
    $copy_text = $text;
    if (!isset($aux_vector_lexicon)) {
        $aux_vector_lexicon = array();
    }
    if (!isset($aux_vector_scenarios)) {
        $aux_vector_scenarios = array();
    }
    if (!isset($scenarios_vector)) {
        $scenarios_vector = array();
    }
    if (!isset($lexicon_vector)) {
        $lexicon_vector = array();
    }

    // If the scenarios vector if empty, it will only search for references to lexicon
    if (count($scenarios_vector) == 0) {

        $i = 0;
        $a = 0;
        while ($i < count($lexicon_vector)) {
            $name_lexicon = escape_metacharacters($lexicon_vector[$i]->nome);
            $regex = "/(\s|\b)(" . $name_lexicon . ")(\s|\b)/i";
            if (preg_match($regex, $copy_text) != 0) {
                $copy_text = preg_replace($regex, " ", $copy_text);
                $aux_vector_lexicon[$a] = $lexicon_vector[$i];
                $a++;
            }
            $i++;
        }
    } else {

        // If the scenarios vector is NOT empty, it will search for lexicon and scenarios
        $lexicon_size = count($lexicon_vector);
        $scenarios_size = count($scenarios_vector);
        $total_size = $lexicon_size + $scenarios_size;
        $i = 0;
        $j = 0;
        $a = 0;
        $b = 0;
        $counter = 0;
        while ($counter < $total_size) {
            if (($i < $lexicon_size ) && ($j < $scenarios_size)) {
                if (strlen($scenarios_vector[$j]->titulo) < strlen($lexicon_vector[$i]->nome)) {
                    $name_lexicon = escape_metacharacters($lexicon_vector[$i]->nome);
                    $regex = "/(\s|\b)(" . $name_lexicon . ")(\s|\b)/i";
                    if (preg_match($regex, $copy_text) != 0) {
                        $copy_text = preg_replace($regex, " ", $copy_text);
                        $aux_vector_lexicon[$a] = $lexicon_vector[$i];
                        $a++;
                    }
                    $i++;
                } else {

                    $scenario_title = escape_metacharacters($scenarios_vector[$j]->titulo);
                    $regex = "/(\s|\b)(" . $scenario_title . ")(\s|\b)/i";
                    if (preg_match($regex, $copy_text) != 0) {
                        $copy_text = preg_replace($regex, " ", $copy_text);
                        $aux_vector_scenarios[$b] = $scenarios_vector[$j];
                        $b++;
                    }
                    $j++;
                }
            } else if ($lexicon_size == $i) {

                $scenario_title = escape_metacharacters($scenarios_vector[$j]->titulo);
                $regex = "/(\s|\b)(" . $scenario_title . ")(\s|\b)/i";
                if (preg_match($regex, $copy_text) != 0) {
                    $copy_text = preg_replace($regex, " ", $copy_text);
                    $aux_vector_scenarios[$b] = $scenarios_vector[$j];
                    $b++;
                }
                $j++;
            } else if ($scenarios_size == $j) {

                $name_lexicon = escape_metacharacters($lexicon_vector[$i]->nome);
                $regex = "/(\s|\b)(" . $name_lexicon . ")(\s|\b)/i";
                if (preg_match($regex, $copy_text) != 0) {
                    $copy_text = preg_replace($regex, " ", $copy_text);
                    $aux_vector_lexicon[$a] = $lexicon_vector[$i];
                    $a++;
                }
                $i++;
            }
            $counter++;
        }
    }

    // Adds the links for lexicon in the text
    $index = 0;
    $aux_vector = array();
    while ($index < count($aux_vector_lexicon)) {
        $name_lexicon = escape_metacharacters($aux_vector_lexicon[$index]->nome);
        $regex = "/(\s|\b)(" . $name_lexicon . ")(\s|\b)/i";
        $link = "<a title=\"L�xico\" href=\"main.php?t=l&id=" . $aux_vector_lexicon[$index]->id_lexico . "\">" . $aux_vector_lexicon[$index]->nome . "</a>";
        $aux_vector[$index] = $link;
        $text = preg_replace($regex, "$1wzzxkkxy" . $index . "$3", $text);
        $index++;
    }

    $index_2 = 0;
    while ($index_2 < count($aux_vector)) {
        $lexicon_link = ( $aux_vector[$index_2] );
        $regex = "/(\s|\b)(wzzxkkxy" . $index_2 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $lexicon_link . "$3", $text);
        $index_2++;
    }


    // Adds the links for scenarios in the text
    $index = 0;
    $aux_vector_scenario_2 = array();
    while ($index < count($aux_vector_scenarios)) {
        $scenario_title = escape_metacharacters($aux_vector_scenarios[$index]->titulo);
        $regex = "/(\s|\b)(" . $scenario_title . ")(\s|\b)/i";
        $link = "$1<a title=\"Cen�rio\" href=\"main.php?t=c&id=" . $aux_vector_scenarios[$index]->id_cenario . "\"><span style=\"font-variant: small-caps\">" . $aux_vector_scenarios[$index]->titulo . "</span></a>$3";
        $aux_vector_scenario_2[$index] = $link;
        $text = preg_replace($regex, "$1wzzxkkxyy" . $index . "$3", $text);
        $index++;
    }


    $index_2 = 0;
    while ($index_2 < count($aux_vector_scenario_2)) {
        $linkCenario = ( $aux_vector_scenario_2[$index_2] );
        $regex = "/(\s|\b)(wzzxkkxyy" . $index_2 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkCenario . "$3", $text);
        $index_2++;
    }

    return $text;
}

?>
