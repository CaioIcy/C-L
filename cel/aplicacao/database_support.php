<?php

session_start();

include_once 'estruturas.php';
include_once 'algorithm_support.php';
include_once 'bd.inc';

function get_subjectList() {
    $id_projeto = $_SESSION['id_projeto'];
    $auxiliar = array();

    $query = "select * from lexico where tipo = 'sujeito' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $auxiliar[] = get_termLexicon($line);
    }

    sort($auxiliar);

    return $auxiliar;
}

function get_objectList() {
    $id_projeto = $_SESSION['id_projeto'];
    $auxiliar = array();

    $query = "select * from lexico where tipo = 'objeto' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $auxiliar[] = get_termLexicon($line);
    }

    sort($auxiliar);

    return $auxiliar;
}

function get_verbList() {
    $id_projeto = $_SESSION['id_projeto'];
    $auxiliar = array();

    $query = "select * from lexico where tipo = 'verbo' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $auxiliar[] = get_termLexicon($line);
    }

    sort($auxiliar);

    return $auxiliar;
}

function get_stateList() {
    $id_projeto = $_SESSION['id_projeto'];
    $auxiliar = array();

    $query = "select * from lexico where tipo = 'estado' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $auxiliar[] = get_termLexicon($line);
    }

    sort($auxiliar);

    return $auxiliar;
}

function is_typeDefined() {
    $id_projeto = $_SESSION['id_projeto'];
    //Esta fun��o verifica se todos os membros da tabela de l�xicos tem um tipo definido
    //Caso haja registros na tabela sem tipo defino, a fun��o retorna estes registros
    //Caso contr�rio retorna true

    $query = "select * from lexico where tipo is null AND id_projeto='$id_projeto' order by id_lexico;";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $result2 = mysql_num_rows($result);

    $col_value = $result2;

    if ($col_value > 0) {
        /* Caso haja lexicos sem tipo definido, seus id's ser�o retornados atrav�s de um array */

        $auxiliar = array();

        while ($line2 = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $auxiliar[] = $line2['id_lexico'];
        }
        mysql_free_result($result);
        return($auxiliar);
    } else {
        mysql_free_result($result);
        return(TRUE);
    }
}

function changes_lexiconType($id_lexicon, $lexicon_type) {
    $id_project = $_SESSION['id_projeto'];
    // esta fun��o atualiza o tipo do lexico $id_lexico (inteiro) para $tipo (string)
    // esta fun��o s� aceita os tipos: sujeito, objeto, verbo, estado e NULL

    if (!(($lexicon_type != "sujeito") || ($lexicon_type != "objeto") || ($lexicon_type != "verbo") || ($lexicon_type != "estado") || ($lexicon_type != "null"))) {
        return (FALSE);
    }
    if ($lexicon_type == "null") {
        $query = "update lexico set tipo = $lexicon_type where id_lexico = '$id_lexicon';";
    } else {
        $query = "update lexico set tipo = '$lexicon_type' where id_lexico = '$id_lexicon';";
    }

    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    return(TRUE);
}

function get_lexicon($id_lexicon) {
    $id_project = $_SESSION['id_projeto'];
    //retorna todos os campos do lexico; cada campo � uma posi��o do
    //array que pode ser indexada pelo nome do campo, ou por um indice
    //inteiro.
    $query = "select * from lexico where id_lexico = '$id_lexicon' AND id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_BOTH);
    return($line);
}

function get_termLexicon($lexicon) {
    $id_project = $_SESSION['id_projeto'];
    $impacts = array();
    $id_lexicon = $lexicon['id_lexico'];
    $query = "select impacto from impacto where id_lexico = '$id_lexicon'";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $impacts[] = strtolower($line['impacto']);
    }
    $lexicon_terms = new LexiconTerm(strtolower($lexicon['nome']), strtolower($lexicon['nocao']), $impacts);
    return $lexicon_terms;
}

/*
  function zera_tipos()
  {
  $query = "update lexico set tipo =  NULL;";
  $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
  }
 */

function registers_impacts($id_lexicon, $lexicon_impacts) {
    $id_project = $_SESSION['id_project'];
    $query = "insert into impacto (id_lexico, impacto) values ('$id_lexicon', '$lexicon_impacts');";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    $query = "select * from impacto where impacto = '$lexicon_impacts' and id_lexico = $id_lexicon;";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    $line = mysql_fetch_array($result, MYSQL_ASSOC);
    $id_impact = $line['id_impacto'];

    return $id_impact;
}

//criar tabela para conceitos (class conceito)
function get_conceptList() {
    $id_project = $_SESSION['id_project'];
    $concept_array = array();

    $query = "select * from conceito where id_projeto='$id_project';";
    $first_result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($first_line = mysql_fetch_array($first_result, MYSQL_BOTH)) {
        $concept = new Concept($first_line['nome'], $first_line['descricao']);
        $concept->namespace = $first_line['namespace'];

        $id = $first_line['id_conceito'];
        $query = "select * from relacao_conceito where id_conceito = '$id' AND id_projeto='$id_project';";
        $second_result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        while ($second_line = mysql_fetch_array($second_result, MYSQL_BOTH)) {
            $id_relation = $second_line['id_relacao'];
            $query = "select * from relacao where id_relacao = '$id_relation' AND id_projeto='$id_project';";
            $third_result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
            $third_line = mysql_fetch_array($third_result, MYSQL_BOTH);
            $relation = $third_line['nome'];
            $predicate = $second_line['predicado'];
            $relation_index = relation_exists($relation, $concept->relacoes);
            if ($relation_index != -1) {
                $concept->relacoes[$relation_index]->predicados[] = $predicate;
            } else {
                $concept->relacoes[] = new RelationBetweenConcepts($predicate, $relation);
            }
        }
        $concept_array[] = $concept;
    }
    sort($concept_array);

    $query = "select * from hierarquia where id_projeto='$id_project';";
    $first_result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($first_line = mysql_fetch_array($first_result, MYSQL_BOTH)) {

        $id_concept = $first_line['id_conceito'];
        $query = "select * from conceito where id_conceito = '$id_concept' AND id_projeto='$id_project';";
        $second_result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        $second_line = mysql_fetch_array($second_result, MYSQL_BOTH);
        $concept_name = $second_line['nome'];

        $id_subConcept = $first_line['id_subconceito'];
        $query = "select * from conceito where id_conceito = '$id_subConcept' AND id_projeto='$id_project';";
        $second_result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        $second_line = mysql_fetch_array($second_result, MYSQL_BOTH);
        $subConcept_name = $second_line['nome'];

        foreach ($concept_array as $concept_key => $concept) {
            if ($concept->nome == $concept_name) {
                $concept_array[$concept_key]->sub_concept[] = $subConcept_name;
            }
        }
    }



    return $concept_array;
}

//criar tabela para conceitos (class relacao_entre_conceitos)
function get_relationList() {
    $id_projeto = $_SESSION['id_projeto'];
    $auxiliar = array();

    $query = "select nome from relacao where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $auxiliar[] = $line['nome'];
    }

    sort($auxiliar);

    return $auxiliar;
}

//criar tabela para axiomas (string)
function get_lista_de_axiomas() {
    $id_projeto = $_SESSION['id_projeto'];
    $auxiliar = array();

    $query = "select axioma from axioma where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $auxiliar[] = $line['axioma'];
    }

    sort($auxiliar);

    return $auxiliar;
}

//variavel funcao (string)
function get_funcao() {
    $id_projeto = $_SESSION['id_projeto'];

    $query = "select valor from algoritmo where nome = 'funcao' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_BOTH);
    return $line['valor'];
}

//variaveis de indice (int)
function get_indices() {
    $id_project = $_SESSION['id_projeto'];

    $query = "select * from algoritmo where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $index = array();

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $index[$line['nome']] = $line['valor'];
    }
    return $index;
}

function save_algorithm() {
    $id_project = $_SESSION['id_projeto'];
    $link = database_connect();

    foreach ($_SESSION["lista_de_conceitos"] as $conceit) {
        print($conceit->nome);
        foreach ($conceit->relacoes as $rel) {
            print("<br>----$rel->verbo");
            foreach ($rel->predicados as $pred) {
                print("<br>--------$pred");
            }
        }
    }


    $query = "delete from relacao where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from conceito where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from relacao_conceito where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from axioma where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from algoritmo where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from hierarquia where id_projeto='$id_project';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    if (isset($_SESSION["lista_de_relacoes"])) {
        foreach ($_SESSION["lista_de_relacoes"] as $relacao) {
            $query = "insert into relacao (nome, id_projeto) values ('$relacao', '$id_project');";
            $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        }
    }
    if (isset($_SESSION["lista_de_conceitos"])) {
        foreach ($_SESSION["lista_de_conceitos"] as $concept) {
            $query = "select id_conceito from conceito where nome = '$concept->nome' and id_projeto='$id_project';";
            $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

            $id_concept = 0;
            if (mysql_num_rows($result) > 0) {
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_concept = $line['id_conceito'];
            } else {
                $query = "insert into conceito (nome,descricao,namespace, id_projeto) values ('$concept->nome', '$concept->descricao','$concept->namespace' ,'$id_project');";
                $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

                $query = "select id_conceito from conceito where nome = '$concept->nome' and id_projeto='$id_project';";
                $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_concept = $line['id_conceito'];
            }


            foreach ($concept->relacoes as $relacao) {
                $verb = $relacao->verbo;
                $query = "select id_relacao from relacao where nome = '$verb' and id_projeto='$id_project';";
                $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_relation = $line['id_relacao'];
                $predicates = $relacao->predicados;
                foreach ($predicates as $pred) {
                    $query = "insert into relacao_conceito (id_conceito,id_relacao,predicado,id_projeto) values ('$id_concept', '$id_relation', '$pred', '$id_project');";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                }
            }
        }
        foreach ($_SESSION["lista_de_conceitos"] as $concept) {
            foreach ($concept->sub_concept as $sub_concept) {
                if ($sub_concept != -1) {
                    $query = "select id_conceito from conceito where nome = '$sub_concept' and id_projeto='$id_project';";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                    $line = mysql_fetch_array($result, MYSQL_BOTH);
                    $id_sub_concept = $line['id_conceito'];

                    $name = $concept->nome;
                    $query = "select id_conceito from conceito where nome = '$name' and id_projeto='$id_project';";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                    $line = mysql_fetch_array($result, MYSQL_BOTH);
                    $id_concept = $line['id_conceito'];

                    $query = "insert into hierarquia (id_conceito,id_subconceito,id_projeto) values ('$id_concept', '$id_sub_concept','$id_project');";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                }
            }
        }
    }
    if (isset($_SESSION["lista_de_axiomas"])) {
        foreach ($_SESSION["lista_de_axiomas"] as $axioma) {
            $query = "insert into axioma (axioma,id_projeto) values ( '$axioma','$id_project' );";
            $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        }
    }
    if (isset($_SESSION["funcao"])) {
        $function = $_SESSION['funcao'];
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('funcao',";
        $query = $query . "'" . $func . "', '$id_project' );";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    if (isset($_SESSION["index1"])) {
        $query = "insert into algoritmo (nome, valor,id_projeto) values ('index1',";
        $query = $query . "'" . $_SESSION['index1'] . "', '$id_project');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    if (isset($_SESSION["index3"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index3',";
        $query = $query . "'" . $_SESSION['index3'] . "', '$id_project');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    if (isset($_SESSION["index4"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index4',";
        $query = $query . "'" . $_SESSION['index4'] . "', '$id_project');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    if (isset($_SESSION["index5"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index5',";
        $query = $query . "'" . $_SESSION['index5'] . "', '$id_project');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    mysql_close($link);

    if ($_SESSION["funcao"] != 'fim') {
        ?>
        <script>
            document.location = "auxiliar_interface.php";
        </script>
        <?php
    } else {
        ?>
        <script>
            document.location = "algorithm.php";
        </script>
        <?php
    }
}

if (isset($_SESSION["tipos"])) {
    session_unregister("tipos");

    $database_conection = database_connect();

    $lexicon_array = is_typeDefined();

    foreach ($lexicon_array as $key => $termo) {
        $lexicon_newArray = $_POST["type" . $key];
        echo ("$termo, $lexicon_newArray <br>");
        if (!changes_lexiconType($termo, $lexicon_newArray)) {
            echo "ERRO <br>";
        }
    }

    mysql_close($database_conection);
    ?>
    <script>
        document.location = "algoritmo_inicio.php";
    </script>
    <?php
}

if (array_key_exists("save", $_POST)) {
    save_algorithm();
}
?>

<html>
    <head>
        <title>Auxiliar BD</title>
        <style>

        </style>
    </head>
    <body>
    </body>
</html>