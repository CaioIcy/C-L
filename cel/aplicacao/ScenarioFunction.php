<?php
include_once 'bd.inc';
include_once 'seguranca.php';

// Para a correta inclusao de um cenario, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo cenario na base de dados;
// 2. Para todos os cenarios daquele projeto, exceto o rec�m inserido:
//      2.1. Procurar em contexto e episodios
//           por ocorrencias do titulo do cenario incluido;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//          2.2.1. Incluir entrada na tabela 'centocen';
//      2.3. Procurar em contexto e episodios do cenario incluido
//           por ocorrencias de titulos de outros cenarios do mesmo projeto;
//      2.4. Se achar alguma ocorrencia:
//          2.4.1. Incluir entrada na tabela 'centocen';
// 3. Para todos os nomes de termos do lexico daquele projeto:
//      3.1. Procurar ocorrencias desses nomes no titulo, objetivo, contexto,
//           recursos, atores, episodios, do cenario incluido;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//          3.2.1. Incluir entrada na tabela 'centolex';
###################################################################
# Insere um cenario no banco de dados.
# Recebe o id_projeto, titulo, objetivo, contexto, atores, recursos, excecao e episodios. (1.1)
# Insere os valores do lexico na tabela CENARIO. (1.2)
# Devolve o id_cenario. (1.4)
#
###################################################################
if (!(function_exists("inclui_cenario"))) {

    function inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
        //global $r;      // Conexao com a base de dados
        $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");

        $q = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios) 
		VALUES ($id_projeto,'$data', '" . prepara_dado(strtolower($titulo)) . "', '" . prepara_dado($objetivo) . "',
		'" . prepara_dado($contexto) . "', '" . prepara_dado($atores) . "', '" . prepara_dado($recursos) . "',
		'" . prepara_dado($excecao) . "', '" . prepara_dado($episodios) . "')";

        mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $q = "SELECT max(id_cenario) FROM cenario";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}
if (!(function_exists("adicionar_cenario"))) {

    function adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
        // Conecta ao SGBD
        $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        // Inclui o cenario na base de dados (sem transformar os campos, sem criar os relacionamentos)
        $id_incluido = inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);

        $q = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_incluido
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        ### PREENCHIMENTO DAS TABELAS LEXTOLEX E CENTOCEN PARA MONTAGEM DO MENU LATERAL
        // Verifica ocorr�ncias do titulo do cenario incluido no contexto 
        // e nos episodios de todos os outros cenarios e adiciona os relacionamentos,
        // caso possua, na tabela centocen

        while ($result = mysql_fetch_array($qrr)) {    // Para todos os cenarios
            $tituloEscapado = escapa_metacaracteres($titulo);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   // (2.2)
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
		                      VALUES (" . $result['id_cenario'] . ", $id_incluido)"; // (2.2.1)
                mysql_query($q) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }

            $tituloEscapado = escapa_metacaracteres($result['titulo']);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $episodios) != 0)) {   // (2.3)        
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_incluido, " . $result['id_cenario'] . ")"; //(2.4.1)

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }   // if
        }   // while
        // Verifica a ocorrencia do nome de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido 

        $q = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($qrr)) {    // (3)
            $nomeEscapado = escapa_metacaracteres($result2['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $titulo) != 0) ||
                    (preg_match($regex, $objetivo) != 0) ||
                    (preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $atores) != 0) ||
                    (preg_match($regex, $recursos) != 0) ||
                    (preg_match($regex, $episodios) != 0) ||
                    (preg_match($regex, $excecao) != 0)) {   // (3.2)
                $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = " . $result2['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);

                if ($resultArrayCen == false) {
                    $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, " . $result2['id_lexico'] . ")";
                    mysql_query($q) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                }
            }   // if
        }   // while
        // Verifica a ocorrencia dos sinonimos de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido
        //Sinonimos

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }

        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_incluido";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            // verifica sinonimos dos outros lexicos no cenario inclu�do
            while ($result = mysql_fetch_array($qrr)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($nomesSinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $objetivo) != 0) ||
                        (preg_match($regex, $contexto) != 0) ||
                        (preg_match($regex, $atores) != 0) ||
                        (preg_match($regex, $recursos) != 0) ||
                        (preg_match($regex, $episodios) != 0) ||
                        (preg_match($regex, $excecao) != 0)) {

                    $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);

                    if ($resultArrayCen == false) {
                        $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
                        mysql_query($q) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                }   // if
            }   // while
        } //for
    }

}

###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################


if (!(function_exists("removeCenario"))) {

    function removeCenario($id_projeto, $id_cenario) {
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        # Remove o relacionamento entre o cenario a ser removido
        # e outros cenarios que o referenciam
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        # Remove o relacionamento entre o cenario a ser removido
        # e o seu lexico
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");
        # Remove o cenario escolhido
        $sql4->execute("DELETE FROM cenario WHERE id_cenario = $id_cenario");
    }

}
?>
