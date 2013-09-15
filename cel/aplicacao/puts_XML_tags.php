<?php

/*
 * Module that puts the tags of the links in the XML files
 */

include ("puts_links.php");

function puts_XML_tag($string) {

    $r = "<link ref=\"$string\">$string </link>";
    return $r;
}

function get_XML_id($string) {

    $j = 0;
    $i = 0;
    while ($string[$i] != '*') {
        $buffer[$j] = $string[$i];
        $i++;
        $j++;
    }

    return implode('', $buffer);
}

function changes_XML_brackets($string) {
    $open_count = 0;
    $close_count = 0;
    $beginning;
    $end;
    $x = 0;
    $y = 0;
    $vector_id;
    $link_original;
    $link_new;
    $buffer3 = '';
    $buffer = 0;
    $i = 0;
    $string_size = strlen($string);

    while ($i <= $string_size) {
        if ($string[$i] == '}') {
            $open_count = $open_count + 1;
        }
        $i++;
    } // FIM WHILE 1

    $i = 0;
    while ($i <= $string_size) {
        if ($string[$i] == '}') {
            $close_count = $close_count + 1;
        }
        $i++;
    } // FIM WHILE 2

    $i = 0;
    if ($open_count == 0) {
        return $string;
    }

    $i = 0;
    while ($i <= $string_size) {
        if ($string[$i] == '{') {
            $buffer = $buffer + 1;
            if ($buffer == 1) {
                $beginning[$x] = $i;
                $x++;
            }
        }

        if ($string[$i] == '}') {
            $buffer = $buffer - 1;
            if ($buffer == 0) {
                $end[$y] = $i + 1;
                $y++;
            }
        }
        $i++;
    };

    $i = 0;
    while ($i < $x) { //x = numero de links reais - 1    
        $link = substr($string, $beginning[$i], $end[$i] - $beginning[$i]);
        $link_original[$i] = $link;
        $link = str_replace('{', '', $link);
        $link = str_replace('}', '', $link);
        $buffer2 = 0;
        $conta = 0;
        $n = 0;
        
        $vector_id[$i] = get_XML_id($link);
        $link = '**' . $link;
        $marcador = 0;

        while ($n < $end[$i] - $beginning[$i]) {
            if ($link[$n] == '*' && $link[$n + 1] == '*' && $marcador == 1) {
                $marcador = 0;
                $link[$n] = '{';
                $link[$n + 1] = '{';
                $n++;
                $n++;
                continue;
            }

            if ($link[$n] == '*' && $link[$n + 1] == '*') {
                $marcador = 1;
                $link[$n] = '{';
                $n++;
                continue;
            }

            if ($marcador == 1) {
                $link[$n] = '{';
            }
            $n++;
        }
        $link = str_replace('{', '', $link);
        $link = puts_XML_tag($link, $vector_id[$i]);
        $link_new[$i] = $link;
        $i++;
    }

    $i = 0;
    while ($i < $x) {
        $string = str_replace($link_original[$i], $link_new[$i], $string);
        $i++;
    }
    return $string;
}

function make_links_XML($text, $lexicon_vector, $scenario_vector) {

    marca_texto($text, $scenario_vector, "cenario");
    marca_texto_cenario($text, $lexicon_vector, $scenario_vector);

    $str = changes_XML_brackets($text);
    return $str;
}
?> 
