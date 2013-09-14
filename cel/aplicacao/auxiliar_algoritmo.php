<?php

function existe_relacao($relation, $relation_list) {
    foreach ($relation_list as $relation_key => $relacao) {
        if (@$relacao->verbo == $relation) {
            return $relation_key;
        }
    }
    return -1;
}

function existe_conceito($new_concept, $concept_list) {
    foreach ($concept_list as $concept_key => $concept) {
        if ($concept->nome == $new_concept) {
            return $concept_key;
        }
    }
    return -1;
}

?>