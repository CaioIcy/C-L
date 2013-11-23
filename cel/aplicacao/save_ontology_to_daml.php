<?php

/*
 * This module has functions to save an ontology in a .daml file
 */

session_start();

include_once 'estruturas.php';

// Date pattern
$dia = date("Y-m-d");
$hora = date("H:i:s");
$data = $dia . "T" . $hora . "Z";

/*
  Objective:
  Save ontology in .daml
  Parameters:
    - $url_ontology - URL of the Ontology
    - $local_directory - local directory where the .daml will be stored
    - $filename - DAML file name (with .daml extension)
    - $array_info - Array with the following keys ("title" , "creator" , "description" , "subject" , "versionInfo")
    - $concept_list - List of concepts
    - $relation_list - List of relations
    - $axiom_list - List of axioms
  Returns:
    - FALSE - in case there is an error while creating the file
    - filename - in case the file is created with success
 */

function save_daml($url_ontology, $local_directory, $filename, $array_info, $concept_list, $relation_list, $axiom_list) {
    // Registers the ontology URL
    $url = $url_ontology . $filename;

    // Registers the path to the DAML file 
    $caminho = $local_directory . $filename;

    // Creates a new DAML file 
    if (!$fp = fopen($caminho, "w")) {
        return FALSE;
    }

    // Records header in the DAML standard 
    $cabecalho = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
    $cabecalho = $cabecalho . '<rdf:RDF xmlns:daml="http://www.daml.org/2001/03/daml+oil#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:xsd="http://www.w3.org/2000/10/XMLSchema#" xmlns:';
    $cabecalho = $cabecalho . $array_info['title'] . '="' . $url . '#">';
    if (!fwrite($fp, $cabecalho)) {
        return FALSE;
    }


    // Inserts ontology information 
    $info = '<daml:Ontology rdf:about="">';
    if ($array_info ["title"] == "") {
        $info = $info . '<dc:title />';
    } else {
        $info = $info . '<dc:title>' . $array_info ["title"] . '</dc:title>';
    }

    $info = $info . '<dc:date>' . date("j-m-Y  H:i:s") . '</dc:date>';
    if ($array_info ["creator"] == "") {
        $info = $info . '<dc:creator />';
    } else {
        $info = $info . '<dc:creator>' . $array_info ["creator"] . '</dc:creator>';
    }

    if ($array_info ["description"] == "") {
        $info = $info . '<dc:description />';
    } else {
        $info = $info . '<dc:description>' . $array_info ["description"] . '</dc:description>';
    }

    if ($array_info ["subject"] == "") {
        $info = $info . '<dc:subject />';
    } else {
        $info = $info . '<dc:subject>' . $array_info ["subject"] . '</dc:subject>';
    }

    if ($array_info ["versionInfo"] == "") {
        $info = $info . '<daml:versionInfo />';
    } else {
        $info = $info . '<daml:versionInfo>' . $array_info ["versionInfo"] . '</daml:versionInfo>';
    }

    $info = $info . '</daml:Ontology>';
    if (!fwrite($fp, $info)) {
        return FALSE;
    }


    //Inserts the concepts, relations and axioms
    if (!record_concepts($fp, $url, $concept_list, $array_info ["creator"])) {
        return FALSE;
    }
    if (!records_relations($fp, $url, $relation_list, $array_info ["creator"])) {
        return FALSE;
    }
    if (!records_axioms($fp, $url, $axiom_list, $array_info ["creator"])) {
        return FALSE;
    }


    // Insert the closing tag of the header 
    if (!fwrite($fp, '</rdf:RDF>')) {
        return FALSE;
    }

    // Closes the open file 
    fclose($fp);

    // Returns the filename 
    return $filename;
}

/*
  Objective:
  Recording the concepts in the DAML file
  Parameters:
  - $file_pointer - pointer for the DAML file
  - $url_ontology - URL of the ontology
  - $concept_list - List of concepts
  - $who_created - Creator of the DAML file
 */

function record_concepts($file_pointer, $url_ontology, $concept_list, $who_created) {

    //Can't use the variable $conceito because of the algorithm of Jeronimo (?????)
    foreach ($concept_list as $oConceito) {

        // Concept header
        if ($oConceito->namespace == "proprio") {
            $namespace = "";
        } else {
            $namespace = $oConceito->namespace;
        }
        $s_conc = '<daml:Class rdf:about="' . $namespace . '#' . $oConceito->nome . '">';
        $s_conc = $s_conc . '<rdfs:label>' . strip_tags($oConceito->nome) . '</rdfs:label>';
        $s_conc = $s_conc . '<rdfs:comment><![CDATA[' . strip_tags($oConceito->descricao) . ']]> ' . '</rdfs:comment>';
        $s_conc = $s_conc . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_conc = $s_conc . '<creator><![CDATA[' . $who_created . ']]> ' . '</creator>';
        if (!fwrite($file_pointer, $s_conc))
            return FALSE;


        // Search for the father-concept (SubConceptOf)
        $lista_subconceitos = $oConceito->sub_concept;
        foreach ($lista_subconceitos as $subconceito) {
            $s_subconc = '<rdfs:subClassOf>';
            $s_subconc = $s_subconc . '<daml:Class rdf:about="' . $url_ontology . '#' . strip_tags($subconceito) . '" />';
            $s_subconc = $s_subconc . '</rdfs:subClassOf>';
            if (!fwrite($file_pointer, $s_subconc))
                return FALSE;
        }

        // Lists the relations between concepts 
        $lista_relacoes = $oConceito->relacoes;
        foreach ($lista_relacoes as $relacao) {
            $s_relac = '<rdfs:subClassOf>';
            $s_relac = $s_relac . '<daml:Restriction>';
            $lista_predicados = $relacao->predicados;
            foreach ($lista_predicados as $predicado) {
                $s_relac = $s_relac . '<daml:onProperty rdf:resource="' . '#' . strip_tags($relacao->verbo) . '" />';
                $s_relac = $s_relac . '<daml:hasClass>';
                $s_relac = $s_relac . '<daml:Class rdf:about="' . '#' . strip_tags($predicado) . '" />';
                $s_relac = $s_relac . '</daml:hasClass>';
            }
            $s_relac = $s_relac . '</daml:Restriction>';
            $s_relac = $s_relac . '</rdfs:subClassOf>';
            if (!fwrite($file_pointer, $s_relac))
                return FALSE;
        }

        // Header end
        $s_conc = '</daml:Class>';
        if (!fwrite($file_pointer, $s_conc))
            return FALSE;
    }

    return TRUE;
}

/*
  Objective:
  Recording the relations in the DAML file
  Parameters:
  - $file_pointer - pointer for the DAML file
  - $url_ontology - URL of the ontology
  - $relation_list - List of relations
  - $who_created - Creator of the DAML file
 */

function records_relations($file_pointer, $url_ontology, $relation_list, $who_created) {
    foreach ($relation_list as $relacao) {
        $s_rel = '<daml:ObjectProperty rdf:about="' . "#" . strip_tags($relacao) . '">';
        $s_rel = $s_rel . '<rdfs:label>' . $relacao . '</rdfs:label>';
        // $s_rel = $s_rel . '<rdfs:comment><![CDATA[' . "" . ']]> ' . '</rdfs:comment>' ;   n�o h� vari�vel coment�rio na estrutura utilizada 
        $s_rel = $s_rel . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_rel = $s_rel . '<creator><![CDATA[' . $who_created . ']]> ' . '</creator>';
        $s_rel = $s_rel . '</daml:ObjectProperty>';
        if (!fwrite($file_pointer, $s_rel))
            return FALSE;
    }
    return TRUE;
}

/*
  Objective:
  Recording the axioms in the DAML file
  Parameters:
  - $file_pointer - pointer for the DAML file
  - $url_ontology - URL of the ontology
  - $axiom_list - List of axioms
 */

function records_axioms($file_pointer, $url_ontology, $axiom_list) {
    foreach ($axiom_list as $axiom) {
        // Concept header
        $axi = explode(" disjoint ", $axiom);
        $s_axi = '<daml:Class rdf:about="' . $url_ontology . '#' . strip_tags($axi[0]) . '">';
        $s_axi = $s_axi . '<daml:disjointWith>';
        $s_axi = $s_axi . '<daml:Class rdf:about="' . $url_ontology . '#' . strip_tags($axi[1]) . '" />';
        $s_axi = $s_axi . '</daml:disjointWith>';
        $s_axi = $s_axi . '</daml:Class>';
        if (!fwrite($file_pointer, $s_axi))
            return FALSE;
    }

    return TRUE;
}

?>
