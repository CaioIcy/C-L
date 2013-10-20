<?php

include_once 'bd.inc';
include_once 'seguranca.php';


###################################################################
# Insere um lexico no banco de dados.
# Recebe o id_projeto, nome, no��o, impacto e os sinonimos. (1.1)
# Insere os valores do lexico na tabela LEXICO. (1.2)
# Insere todos os sinonimos na tabela SINONIMO. (1.3)
# Devolve o id_lexico. (1.4)
###################################################################
if (!(function_exists("inclui_lexico"))) {

    function inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");


        $q = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($id_projeto, '$data', '" . prepara_dado(strtolower($nome)) . "',
			  '" . prepara_dado($nocao) . "', '" . prepara_dado($impacto) . "', '$classificacao')";

        mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //sinonimo
        $newLexId = mysql_insert_id($r);


        if (!is_array($sinonimos))
            $sinonimos = array();

        foreach ($sinonimos as $novoSin) {
            $q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($newLexId, '" . prepara_dado(strtolower($novoSin)) . "', $id_projeto)";
            mysql_query($q, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        $q = "SELECT max(id_lexico) FROM lexico";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}



###################################################################
# Para a correta inclusao de um termo no lexico, uma serie de procedimentos
# precisam ser tomados (relativos ao requisito 'navegacao circular'):
#
# 1. Incluir o novo termo na base de dados;
# 2. Para todos os cenarios daquele projeto:
#      2.1. Procurar em titulo, objetivo, contexto, recursos, atores, episodios
#           por ocorrencias do termo incluido ou de seus sinonimos;
#      2.2. Para os campos em que forem encontradas ocorrencias:
#              2.2.1. Incluir entrada na tabela 'centolex';
# 3. Para todos termos do lexico daquele projeto (menos o recem-inserido):
#      3.1. Procurar em nocao, impacto por ocorrencias do termo inserido ou de seus sinonimos;
#      3.2. Para os campos em que forem encontradas ocorrencias:
#              3.2.1. Incluir entrada na tabela 'lextolex';
#      3.3. Procurar em nocao, impacto do termo inserido por
#           ocorrencias de termos do lexico do mesmo projeto;
#      3.4. Se achar alguma ocorrencia:
#              3.4.1. Incluir entrada na table 'lextolex';
###################################################################
if (!(function_exists("adicionar_lexico"))) {

    function adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao); // (1)

        $qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto";

        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // 2  - Para todos os cenarios
            $nomeEscapado = escapa_metacaracteres($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { //2.2
                $q = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_incluido)"; //2.2.1

                mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }


        //sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($qrr)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {

                    $qLex = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);

                    if ($resultArraylex == false) {

                        $q = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $id_incluido)";

                        mysql_query($q) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    } //if
                }//if                
            }//while            
        } //for


        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico != $id_incluido";

        //pega todos os outros lexicos
        $qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // (3)
            $nomeEscapado = escapa_metacaracteres($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {

                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($qrLex);

                if ($resultArraylex == false) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $id_incluido)";

                    mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }

            $nomeEscapado = escapa_metacaracteres($result['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0)) {   // (3.3)        
                $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($id_incluido, " . $result['id_lexico'] . ")";

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }   // while
        //lexico para lexico

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $id_projeto
              AND id_lexico != $id_incluido";

        //sinonimos dos outros lexicos no texto do inserido

        $qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            while ($resultl = mysql_fetch_array($qrr)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);

                    if ($resultArraylex == false) {

                        $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                         VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";

                        mysql_query($q) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }//if
                }    //if
            }//while
        }//for
        //sinonimos ja existentes

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
    }

}


###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico"))) {

    function removeLexico($id_projeto, $id_lexico) {
        $DB = new PGDB ();
        $delete = new QUERY($DB);

        # Remove o relacionamento entre o lexico a ser removido
        # e outros lexicos que o referenciam
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        # Remove o lexico escolhido
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM lexico WHERE id_lexico = $id_lexico");
    }

}

?>
