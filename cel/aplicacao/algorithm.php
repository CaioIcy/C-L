<?php
/* File: algorithm.php
 * Purpose: This script is responsible for the algorithms
 * used in the system.
 */

session_start();

include_once 'estruturas.php';
include_once 'algorithm_support';

?>

<html>
    <head>
        <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
        <style>

        </style>
    </head>
    <body>
        <?php

        function checks_consistence() {
            return TRUE;
        }

        function compares_arrrays($array1, $array2) {

            if (count($array1) != count($array2)) {
                return FALSE;
            }

            foreach ($array1 as $key => $element) {
                if ($element->verbo != $array2[$key]->verbo) {
                    return FALSE;
                }
            }
            return TRUE;
        }

        /*
          Cenario:	Montar hierarquia.
          Objetivo:	Montar hierarquia de conceitos.
          Contexto:	Organizacao da ontologia em andamento.
          Atores:
          Recursos:	Sistema, conceito, lista de subconceitos, lista de conceitos.
          Episodios:
          - Para cada subconceito
         * Procurar sua chave na lista de conceitos.
         * Adicionar a chave como um subconceito do conceito.
         */

        function create_hierarchy($concept, $new_list, $current_list) {
            foreach ($new_list as $sub_concept) {
                $key = concept_exists($sub_concept, $current_list);
                $concept->sub_concept[] = $sub_concept;
            }
        }

        /*
          Cenario:	Traduzir os termos do lexico classificados como sujeito e objeto.
          Objetivo:	Traduzir os termos do lexico classificados como sujeito e objeto.
          Contexto:	Algoritmo de tradu��o iniciado.
          Atores:		Usuario.
          Recursos:	Sistema, lista de sujeito e objetos, lista de conceitos, lista de relacoes.
          Episodios:
          - Para cada elemento da lista de sujeito e objetos
         * Criar novo conceito com o mesmo nome e a descricao igual a nocao do elemento.
         * Para cada impacto do elemento
          . Verificar com o usuario a existencia do impacto na lista de relacoes.
          . Caso n�o exista, incluir este impacto na lista de relacoes.
          . Incluir esta relacao na lista de relacoes do conceito.
          . Descobrir
         * Incluir o conceito na lista de conceitos.
         * Verificar consistencia.
         */

        function translate_subject_to_object($list_of_subjects_objects, $concept, $relations, $axioms) {

            for (; $_SESSION["index1"] < count($list_of_subjects_objects); ++$_SESSION["index1"]) {

                $subject = $list_of_subjects_objects[$_SESSION["index1"]];

                if (!isset($_SESSION["conceito"])) {
                    $_SESSION["salvar"] = "TRUE";
                    $_SESSION["conceito"] = new Concept($subject->nome, $subject->nocao);
                    $_SESSION["conceito"]->namespace = "proprio";
                } else {
                    $_SESSION["salvar"] = "FALSE";
                }


                for (; $_SESSION["index2"] < count($subject->impacto); ++$_SESSION["index2"]) {

                    $impact = $subject->impacto[$_SESSION["index2"]];

                    if (trim($impact) == "")
                        continue;

                    if (!isset($_SESSION["verbos_selecionados"]))
                        $_SESSION["verbos_selecionados"] = array();

                    if (!isset($_SESSION["impact"])) {
                        $_SESSION["impact"] = array();
                        $_SESSION["finish_insert"] = FALSE;
                    }
                    while (!$_SESSION["finish_insert"]) {
                        if (!isset($_SESSION["exist"])) {
                            asort($relations);
                            $_SESSION["lista"] = $relations;
                            $_SESSION["nome1"] = $impact;
                            $_SESSION["nome2"] = $subject;
                            $_SESSION["job"] = "exist";
                            ?>
                            <SCRIPT language='javascript'>
                                document.location = "auxiliar_interface.php";
                            </SCRIPT>



                            <?php
                            exit();
                        }



                        if ($_POST["existe"] == "FALSE") {

                            $name = strtolower($_POST["nome"]);
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && (array_search($name, $_SESSION["verbos_selecionados"]) !== null)) {
                                continue;
                            }
                            $_SESSION["verbos_selecionados"][] = $name;
                            $i = array_search($name, $relations);
                            if ($i === false) {
                                $_SESSION["impact"][] = (array_push($relations, $name) - 1);
                            } else {
                                $_SESSION["impact"][] = $i;
                            }
                        } else if ($_POST["indice"] != -1) {
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && array_search($relations[$_POST["indice"]], $_SESSION["verbos_selecionados"]) !== false) {
                                continue;
                            }
                            $_SESSION["verbos_selecionados"][] = $relations[$_POST["indice"]];
                            $_SESSION["impact"][] = $_POST["indice"];
                        } else {
                            $_SESSION["finish_insert"] = TRUE;
                        }
                    }

                    if (!isset($_SESSION["ind"])) {
                        $_SESSION["ind"] = 0;
                    }

                    $_SESSION["verbos_selecionados"] = array();

                    for (; $_SESSION["ind"] < count($_SESSION["impact"]); ++$_SESSION["ind"]) {

                        if (!isset($_SESSION["predicados_selecionados"]))
                            $_SESSION["predicados_selecionados"] = array();

                        $index = $_SESSION["impact"][$_SESSION["ind"]];
                        $_SESSION["finish_relation"] = FALSE;
                        while (!$_SESSION["finish_relation"]) {
                            if (!isset($_SESSION["insert_relation"])) {
                                asort($concept);
                                $_SESSION["lista"] = $concept;
                                $_SESSION["nome1"] = $relations[$index];
                                $_SESSION["nome2"] = $subject->nome;
                                $_SESSION["nome3"] = $impact;
                                $_SESSION["job"] = "insert_relation";
                                ?>
                                <SCRIPT language='javascript'>
                                    document.location = "auxiliar_interface.php";
                                </SCRIPT>
                                <?php
                                exit();
                            } else if (isset($_SESSION["nome2"])) {

                                session_unregister("nome2");
                                session_unregister("nome3");
                                session_unregister("insert_relation");


                                if ($_POST["existe"] == "FALSE") {
                                    $concept = strtolower($_POST["nome"]);

                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($concept, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }
                                    $_SESSION["predicados_selecionados"][] = $concept;

                                    if (concept_exists($concept, $_SESSION['lista_de_conceitos']) == -1) {
                                        if (concept_exists($concept, $list_of_subjects_objects) == -1) {
                                            $new_concept = new Concept($concept, "");
                                            $new_concept->namespace = $_POST['namespace'];
                                            $_SESSION['lista_de_conceitos'][] = $new_concept;
                                        }
                                    }

                                    $$index_relation = relation_exists($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    if ($$index_relation != -1) {
                                        if (array_search($concept, $_SESSION["conceito"]->relacoes[$$index_relation]->predicados) === false)
                                            $_SESSION["conceito"]->relacoes[$$index_relation]->predicados[] = $concept;
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] = new RelationBetweenConcepts($concept, $_SESSION["nome1"]);
                                    }
                                } else if ($_POST["indice"] != "-1") {
                                    $concept = $concept[$_POST["indice"]]->nome;
                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($concept, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }

                                    $_SESSION["predicados_selecionados"][] = $concept;

                                    $$index_relation = relation_exists($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    if ($$index_relation != -1) {
                                        if (array_search($concept, $_SESSION["conceito"]->relacoes[$$index_relation]->predicados) === false)
                                            $_SESSION["conceito"]->relacoes[$$index_relation]->predicados[] = $concept;
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] = new RelationBetweenConcepts($concept, $_SESSION["nome1"]);
                                    }
                                } else {
                                    $_SESSION["finish_relation"] = TRUE;
                                }
                            }
                        }
                        $_SESSION["predicados_selecionados"] = array();
                    }


                    /* Unregister a global variable from the current session */
                    session_unregister("exist");
                    session_unregister("impact");
                    session_unregister("ind");
                    session_unregister("insert_relation");
                    session_unregister("insert");
                    session_unregister("verbos_selecionados");
                    session_unregister("predicados_selecionados");
                }

                $finish_disjoint = FALSE;
                while (!$finish_disjoint) {
                    if (!isset($_SESSION["axiomas_selecionados"]))
                        $_SESSION["axiomas_selecionados"] = array();

                    if (!isset($_SESSION["disjoint"])) {
                        $_SESSION["lista"] = $concept;
                        $_SESSION["nome1"] = $_SESSION["conceito"]->nome;
                        $_SESSION["job"] = "disjoint";
                        ?>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    if ($_POST["existe"] == "TRUE") {
                        $actual_axiom = $_SESSION["conceito"]->nome . " disjoint " . strtolower($_POST["nome"]);
                        if (array_search($actual_axiom, $axioms) === false) {
                            $axioms[] = $actual_axiom;
                            $_SESSION["axiomas_selecionados"][] = $actual_axiom;
                        }
                        session_unregister("disjoint");
                    } else {
                        $finish_disjoint = TRUE;
                    }
                }
                $_SESSION["axiomas_selecionados"] = array();

                $concept[] = $_SESSION["conceito"];
                asort($concept);

                if (!checks_consistence()) {
                    exit();
                }

                session_unregister("insert");
                session_unregister("disjoint");
                session_unregister("exist");
                session_unregister("insert_relation");
                session_unregister("conceito");
                $_SESSION["index2"] = 0;
            }
            $_SESSION["index1"] = 0;
            session_unregister("finish_insert");
            session_unregister("finish_relation");
        }

        /*
          Cenario:	Traduzir os termos do lexico classificados como verbo.
          Objetivo:	Traduzir os termos do lexico classificados como verbo.
          Contexto:	Algoritmo de tradu��o iniciado.
          Atores:		Usuario.
          Recursos:	Sistema, lista de verbo, lista de relacoes.
          Episodios:
          - Para cada elemento da lista de verbo
         * Verificar com o usuario a existencia do verbo na lista de relacoes.
         * Caso n�o exista, incluir este verbo na lista de relacoes.
         * Verificar consistencia.
         */

        function translate_verbs($verbs, $relations) {
            for (; $_SESSION["index3"] < count($verbs); ++$_SESSION["index3"]) {

                $verb = $verbs[$_SESSION["index3"]];


                if (!isset($_SESSION["exist"])) {
                    $_SESSION["salvar"] = "TRUE";
                    asort($relations);
                    $_SESSION["lista"] = $relations;
                    $_SESSION["nome1"] = $verb->nome;
                    $_SESSION["nome2"] = $verb;
                    $_SESSION["job"] = "exist";
                    ?>
                    <SCRIPT language='javascript'>
                        document.location = "auxiliar_interface.php";
                    </SCRIPT>
                    <?php
                    exit();
                }

                if ($_POST["existe"] == "FALSE") {
                    $nome = strtolower($_POST["nome"]);
                    if (array_search($nome, $relations) === false)
                        array_push($relations, $nome);
                }


                //	$lista_de_relacoes = $_SESSION["lista"];

                if (!checks_consistence()) {
                    exit();
                }

                session_unregister("exist");
                session_unregister("insert");
            }
            $_SESSION["index3"] = 0;
        }

        /*
          Cenario:	Traduzir os termos do lexico classificados como estado.
          Objetivo:	Traduzir os termos do lexico classificados como estado.
          Contexto:	Algoritmo de traducao iniciado.
          Atores:		Usuario.
          Recursos:	Sistema, lista de estado, lista de conceitos, lista de relacoes, lista de axiomas.
          Episodios:
          - Para cada elemento da lista de estado
         * Para cada impacto do elemento
          . Descobrir
         * Verificar se o elemento possui importancia central na ontologia.
         * Caso tenha, traduza como se fosse um sujeito/objeto.
         * Caso contrario, traduza como se fosse um verbo.
         * Verificar consistencia.
         */

        function translate_states($states, $concepts, $relations, $axioms) {
            for (; $_SESSION["index4"] < count($states); ++$_SESSION["index4"]) {

                $state = $states[$_SESSION["index4"]];


                $auxiliar = array($state);

                if (!isset($_SESSION["main_subject"])) {

                    $_SESSION["nome1"] = $state->nome;
                    $_SESSION["nome2"] = $state;
                    $_SESSION["job"] = "main_subject";
                    ?>
                    <p>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();

                        //$rel = exist($verbo->nome, $lista_de_relacoes);
                    }


                    if (!isset($_SESSION["translate"])) {
                        if ($_POST["main_subject"] == "TRUE") {
                            $_SESSION["translate"] = 1;
                            translate_subject_to_object($auxiliar, &$concepts, &$relations, &$axioms);
                        } else {
                            $_SESSION["translate"] = 2;
                            translate_verbs($auxiliar, &$relations);
                        }
                    } else if ($_SESSION["translate"] == 1) {
                        translate_subject_to_object($auxiliar, &$concepts, &$relations);
                    } else if ($_SESSION["translate"] == 2) {
                        translate_verbs($auxiliar, &$relations);
                    }



                    if (!checks_consistence()) {
                        exit();
                    }

                    session_unregister("main_subject");
                    session_unregister("translate");
                }
                $_SESSION["index4"] = 0;
            }

            /*
              Cenario:	Organizar ontologia.
              Objetivo:	Organizar ontologia.
              Contexto:	Listas de conceitos, relacoes e axiomas prontas.
              Atores:		Usuario.
              Recursos:	Sistema, lista de conceitos, lista de relacoes, lista de axiomas.
              Episodios:
              - Faz-se uma copia da lista de conceitos.
              - Para cada elemento x da lista de conceitos
             * Cria-se uma nova lista contendo o elemento x.
             * Para cada elemento subsequente y
              . Compara as relacoes dos elementos x e y.
              . Caso possuam as mesmas relacoes, adiciona-se o elemento y a nova lista que ja contem x.
              . Retira-se y da lista de conceitos.
             * Retira-se x da lista de conceitos.
             * Caso a nova lista tenha mais de dois elementos, ou seja, caso x compartilhe as mesmas
              relacoes com outro termo
              . Procura por um elemento na lista de conceitos que faca referencia a todos os elementos
              da nova lista.
              . Caso exista tal elemento, montar hierarquia.
              . Caso nao exista, descobrir.
             * Verificar consistencia.
              - Restaurar lista de conceitos.
             */

            function organize_ontology($concepts, $relations, $axioms) {
                $_SESSION["salvar"] = "TRUE";
                /* for( ; $_SESSION["index5"] < count($conceitos); ++$_SESSION["index5"] )
                  {
                  $_SESSION["salvar"] = "TRUE";

                  $conc = $conceitos[$_SESSION["index5"]];

                  if( count($conc->subconceitos) > 0 )
                  {
                  if( $conc->subconceitos[0] == -1 )
                  {
                  array_splice($conc->subconceitos, 0, 1);
                  continue;
                  }
                  }

                  $conc->subconceitos[0] = -1;
                  $key = $_SESSION["index5"];

                  $nova_lista_de_conceitos = array($conc);

                  for( $i = $key+1; $i < count($conceitos); ++$i )
                  {
                  if (compara_arrays($conc->relacoes, $conceitos[$i]->relacoes))
                  {
                  $conceitos[$i]->subconceitos[0] = -1;
                  $nova_lista_de_conceitos[] = $conceitos[$i];
                  }
                  }
                 */
                //if( count($nova_lista_de_conceitos) >= 2 )

                $finish_relation = FALSE;
                while (!$finish_relation) {
                    $index = 0;

                    if (!isset($_SESSION["reference"])) {

                        $_SESSION["lista"] = $concepts; //array($conc1, $nconc);
                        //$_SESSION['nome1'] = $nova_lista_de_conceitos;//
                        $_SESSION["job"] = "reference";
                        ?>
                        <a href="auxiliar_interface.php">auxiliar_interface</a>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();

                        //$rel = exist($verbo->nome, $lista_de_relacoes);
                    }

                    session_unregister("reference");

                    $element_found = FALSE;

                    if (isset($_POST['pai'])) {
                        $father_name = $_POST['pai'];
                        $key2 = concept_exists($father_name, $concepts);
                        $children = array();
                        foreach ($concepts as $key3 => $son) {
                            $son_name = trim($son->nome);
                            if (isset($_POST[$key3])) {
                                $children[] = $son_name;
                            }
                        }
                        if (count($children) > 0) {
                            create_hierarchy(&$concepts[$key2], $children, $concepts);
                            $element_found = true;
                        }
                    } else {
                        $finish_relation = true;
                    }


                    if (!$element_found) {
                        //tentar montar hierarquia pelo vocabulario minimo.
                    }
                }

                if (!checks_consistence()) {
                    exit();
                }
                //array_splice($conc->subconceitos, 0, 1);
                //}
                //$_SESSION["index5"] = 0;
            }

            /*
              Cenario:  	Traduzir L�xico para Ontologia.
              Objetivo: 	Traduzir L�xico para Ontologia.
              Contexto: 	Existem listas de elementos do l�xico organizadas por tipo, e estes elementos
              s�o consistentes.
              Atores:   	Usu�rio.
              Recursos: 	Sistema, listas de elementos do l�xico organizadas por tipo, listas de elementos
              da ontologia.
              Epis�dios:
              - Criar lista de conceitos vazia.
              - Criar lista de relacoes vazia.
              - Criar lista de axiomas vazia.
              - Traduzir os termos do lexico classificados como sujeito e objeto.
              - Traduzir os termos do lexico classificados como verbo.
              - Traduzir os termos do lexico classificados como estado.
              - Organizar a ontologia.

             */

            function translation() {
                //Verifica se as listas foram iniciadas.
                if (isset($_SESSION["lista_de_sujeito"]) && isset($_SESSION["lista_de_objeto"]) &&
                        isset($_SESSION["lista_de_verbo"]) && isset($_SESSION["lista_de_estado"]) &&
                        isset($_SESSION["lista_de_conceitos"]) && isset($_SESSION["lista_de_relacoes"]) &&
                        isset($_SESSION["lista_de_axiomas"])) {
                    $subjects = $_SESSION["lista_de_sujeito"];
                    $objects = $_SESSION["lista_de_objeto"];
                    $verbs = $_SESSION["lista_de_verbo"];
                    $states = $_SESSION["lista_de_estado"];
                } else {
                    echo "ERRO! <br>";
                    exit();
                }

                $list_of_subjects_objects = array_merge($subjects, $objects);
                sort($list_of_subjects_objects);
                $_SESSION['lista_de_sujeito_e_objeto'] = $list_of_subjects_objects;


                if ($_SESSION["funcao"] == "sujeito_objeto") {
                    translate_subject_to_object($list_of_subjects_objects, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "verbo";
                }

                if ($_SESSION["funcao"] == "verbo") {
                    translate_verbs($verbs, &$_SESSION["lista_de_relacoes"]);
                    $_SESSION["funcao"] = "estado";
                }

                if ($_SESSION["funcao"] == "estado") {
                    translate_states($states, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "organiza";
                }

                if ($_SESSION["funcao"] == "organiza") {
                    organize_ontology(&$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "fim";
                }


                //Imprime Resultados
                /*
                  print("CONCEITOS: <br>");
                  foreach( $_SESSION["lista_de_conceitos"] as $con)
                  {
                  echo "$con->nome --> $con->descricao ";
                  foreach($con->relacoes as $rel)
                  {

                  }
                  echo "<br>";
                  }

                  print("RELACOES: <br>");
                  print_r($_SESSION["lista_de_relacoes"]);
                  echo "<br>";

                  print("AXIOMAS: <br>");
                  print_r($_SESSION["lista_de_axiomas"]);
                  echo "<br>";
                 */
                echo 'O processo de gera��o de Ontologias foi conclu�do com sucesso!<br>
	N�o esque�a de clicar em Salvar.';
                ?>
            <p>
            <form method="POST" action="auxiliar_bd.php">
                <input type="hidden" value="TRUE" name="save" size="20" >
                <input type="submit" value="SALVAR">
            </form>
        </p>
        <?php
    }

    translation();
    ?>


</body>
</html>