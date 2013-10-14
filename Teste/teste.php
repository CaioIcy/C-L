<?php

require_once dirname(__FILE__) . '/../cel/aplicacao/funcoes_genericas.php';

class teste extends PHPUnit_Framework_TestCase{
    
    public function test(){
        $result = return1(1);
        $this->assertEquals(1,$result);
    }
    
}
?>
