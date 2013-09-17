<?php

/* File: algorithm_support.php
 * Purpose: This script is responsible for supporting the algorithm.php file
 */

function relation_exists($new_relation, $relation_list) {
    foreach ($relation_list as $relation_key => $relation) {
        if (@$relation->verbo == $new_relation) {
            return $relation_key;
        }
    }
    return -1;
}

function concept_exists($new_concept, $concept_list) {
    foreach ($concept_list as $concept_key => $concept) {
        if ($concept->nome == $new_concept) {
            return $concept_key;
        }
    }
    return -1;
}

?>