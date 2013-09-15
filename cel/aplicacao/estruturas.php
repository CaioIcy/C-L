<?php

$_SESSION["estruturas"] = 1;

class Concept {

    var $nome;
    var $descricao;
    var $relacoes;
    var $sub_concept;
    var $namespace;

    function Concept($n, $d) {
        $this->nome = $n;
        $this->descricao = $d;
        $this->relacoes = array();
        $this->sub_concept = array(); //not initialized
        $this->namespace = "";
    }

}

class RelationBetweenConcepts {

    var $predicados;
    var $verbo;

    function RelationBetweenConcepts($p, $v) {
        $this->predicados[] = $p;
        $this->verbo = $v;
    }

}

class LexiconTerm {

    var $nome;
    var $nocao;
    var $impacto;

    function LexiconTerm($name, $notion, $i) {
        $this->nome = $name;
        $this->nocao = $notion;
        $this->impacto = $i;
    }

}

?>