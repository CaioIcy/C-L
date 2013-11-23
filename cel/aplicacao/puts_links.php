<?php

/*
 * To prevent the current lexicon to create a link to itself.
 * Function that loads the vector with all the titles of the lexicons and their
 * synyonyms EXCEPT the ones passed through $id_current_lexicon.
 */

function load_vetor_lexico($id_projeto, $id_current_lexicon, $semAtual) {
    $vetorDeLexicos = array();
    if ($semAtual) {
        $queryLexicos = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_projeto' AND id_lexico <> '$id_current_lexicon' 
							ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome 
							FROM sinonimo
							WHERE id_projeto = '$id_projeto' AND id_lexico <> '$id_current_lexicon' 
							ORDER BY nome DESC";
    } else {

        $queryLexicos = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_projeto' 
							ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome    
							FROM sinonimo
							WHERE id_projeto = '$id_projeto' ORDER BY nome DESC";
    }

    $resultadoQueryLexicos = mysql_query($queryLexicos) or die("Error while sending the selection query in the lexicon table!" . mysql_error());

    $i = 0;
    while ($linhaLexico = mysql_fetch_object($resultadoQueryLexicos)) {
        $vetorDeLexicos[$i] = $linhaLexico;
        $i++;
    }

    $resultadoQuerySinonimos = mysql_query($querySinonimos) or die("Error while sending the selection query in the synonym table!" . mysql_error());
    while ($linhaSinonimo = mysql_fetch_object($resultadoQuerySinonimos)) {
        $vetorDeLexicos[$i] = $linhaSinonimo;
        $i++;
    }
    return $vetorDeLexicos;
}

/*
 * To prevent the current scenario to create a link to itself.
 * Function that loads the vector with all the titles of the scenarios
 * EXCEPT the ones passed through $id_current_scenario.
 */

function load_vetor_cenario($id_projeto, $id_current_scenario, $semAtual) {
    if (!isset($vetorDeCenarios)) {
        $vetorDeCenarios = array();
    }
    if ($semAtual) {
        $queryCenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_projeto' AND id_cenario <> '$id_current_scenario' 
							ORDER BY titulo DESC";
    } else {
        $queryCenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_projeto' 
							ORDER BY titulo DESC";
    }

    $resultadoQueryCenarios = mysql_query($queryCenarios) or die("Error while sending the selection query" . mysql_error());

    $i = 0;
    while ($linhaCenario = mysql_fetch_object($resultadoQueryCenarios)) {
        $vetorDeCenarios[$i] = $linhaCenario;
        $i++;
    }

    return $vetorDeCenarios;
}

//Splits the array in two
function divide_array(&$vet, $ini, $fim, $tipo) {
    $i = $ini;
    $j = $fim;
    $dir = 1;

    while ($i < $j) {
        if (strcasecmp($tipo, 'cenario') == 0) {
            if (strlen($vet[$i]->titulo) < strlen($vet[$j]->titulo)) {
                $str_temp = $vet[$i];
                $vet[$i] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        } else {
            if (strlen($vet[$i]->nome) < strlen($vet[$j]->nome)) {
                $str_temp = $vet[$i];
                $vet[$i] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        }
        if ($dir == 1)
            $j--;
        else
            $i++;
    }

    return $i;
}

//Sorts the array
function quicksort(&$vet, $ini, $fim, $tipo) {
    if ($ini < $fim) {
        $k = divide_array($vet, $ini, $fim, $tipo);
        quicksort($vet, $ini, $k - 1, $tipo);
        quicksort($vet, $k + 1, $fim, $tipo);
    }
}

/*
 * Function that builds the links according to the text, passed through the
 * parameter $text. Leixcons, passed through $lexiconVector. And scenarios,
 * passed through $scenariosVector
 */

function monta_links($text, $lexiconVector, $scenariosVector) {
    $copiaTexto = $text;
    if (!isset($vetorAuxLexicos)) {
        $vetorAuxLexicos = array();
    }
    if (!isset($vetorAuxCenarios)) {
        $vetorAuxCenarios = array();
    }
    if (!isset($scenariosVector)) {
        $scenariosVector = array();
    }
    if (!isset($lexiconVector)) {
        $lexiconVector = array();
    }

    // If the scenarios vector if empty, it will only search for references to lexicon

    if (count($scenariosVector) == 0) {

        $i = 0;
        $a = 0;
        while ($i < count($lexiconVector)) {
            $nomeLexico = escape_metacharacters($lexiconVector[$i]->nome);
            $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
            if (preg_match($regex, $copiaTexto) != 0) {
                $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                $vetorAuxLexicos[$a] = $lexiconVector[$i];
                $a++;
            }
            $i++;
        }
    } else {

        // If the scenarios vector is NOT empty, it will search for lexicon and scenarios

        $tamLexicos = count($lexiconVector);
        $tamCenarios = count($scenariosVector);
        $tamanhoTotal = $tamLexicos + $tamCenarios;
        $i = 0;
        $j = 0;
        $a = 0;
        $b = 0;
        $contador = 0;
        while ($contador < $tamanhoTotal) {
            if (($i < $tamLexicos ) && ($j < $tamCenarios)) {
                if (strlen($scenariosVector[$j]->titulo) < strlen($lexiconVector[$i]->nome)) {
                    $nomeLexico = escape_metacharacters($lexiconVector[$i]->nome);
                    $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                    if (preg_match($regex, $copiaTexto) != 0) {
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxLexicos[$a] = $lexiconVector[$i];
                        $a++;
                    }
                    $i++;
                } else {

                    $tituloCenario = escape_metacharacters($scenariosVector[$j]->titulo);
                    $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                    if (preg_match($regex, $copiaTexto) != 0) {
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxCenarios[$b] = $scenariosVector[$j];
                        $b++;
                    }
                    $j++;
                }
            } else if ($tamLexicos == $i) {

                $tituloCenario = escape_metacharacters($scenariosVector[$j]->titulo);
                $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                if (preg_match($regex, $copiaTexto) != 0) {
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxCenarios[$b] = $scenariosVector[$j];
                    $b++;
                }
                $j++;
            } else if ($tamCenarios == $j) {

                $nomeLexico = escape_metacharacters($lexiconVector[$i]->nome);
                $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                if (preg_match($regex, $copiaTexto) != 0) {
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxLexicos[$a] = $lexiconVector[$i];
                    $a++;
                }
                $i++;
            }
            $contador++;
        }
    }

    // Adds the links for lexicon in the text

    $indice = 0;
    $vetorAux = array();
    while ($indice < count($vetorAuxLexicos)) {
        $nomeLexico = escape_metacharacters($vetorAuxLexicos[$indice]->nome);
        $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
        $link = "<a title=\"L�xico\" href=\"main.php?t=l&id=" . $vetorAuxLexicos[$indice]->id_lexico . "\">" . $vetorAuxLexicos[$indice]->nome . "</a>";
        $vetorAux[$indice] = $link;
        $text = preg_replace($regex, "$1wzzxkkxy" . $indice . "$3", $text);
        $indice++;
    }
    $indice2 = 0;

    while ($indice2 < count($vetorAux)) {
        $linkLexico = ( $vetorAux[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxy" . $indice2 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkLexico . "$3", $text);
        $indice2++;
    }


    // Adds the links for scenarios in the text

    $indice = 0;
    $vetorAuxCen = array();
    while ($indice < count($vetorAuxCenarios)) {
        $tituloCenario = escape_metacharacters($vetorAuxCenarios[$indice]->titulo);
        $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
        $link = "$1<a title=\"Cen�rio\" href=\"main.php?t=c&id=" . $vetorAuxCenarios[$indice]->id_cenario . "\"><span style=\"font-variant: small-caps\">" . $vetorAuxCenarios[$indice]->titulo . "</span></a>$3";
        $vetorAuxCen[$indice] = $link;
        $text = preg_replace($regex, "$1wzzxkkxyy" . $indice . "$3", $text);
        $indice++;
    }


    $indice2 = 0;
    while ($indice2 < count($vetorAuxCen)) {
        $linkCenario = ( $vetorAuxCen[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxyy" . $indice2 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkCenario . "$3", $text);
        $indice2++;
    }

    return $text;
}
?>
