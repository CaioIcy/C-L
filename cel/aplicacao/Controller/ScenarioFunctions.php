<?php

include_once '/../bd.inc';
include_once '/../seguranca.php';
include_once '/../FunctionAsserts.php';

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);

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

        assert($id_projeto > 0);
        null_assert($titulo);
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

        assert($id_projeto > 0);
        null_assert($titulo);
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

        assert($count > 0);

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
        null_assert($id_projeto);
        null_assert($id_cenario);
        assert($id_projeto > 0);
        assert($id_cenario > 0);


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

###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("alteraCenario"))) {

    function alteraCenario($id_projeto, $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {

        assert($id_projeto > 0);
        assert($id_cenario > 0);

        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        # Remove o relacionamento entre o cenario a ser alterado
        # e outros cenarios que o referenciam
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        # Remove o relacionamento entre o cenario a ser alterado
        # e o seu lexico
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");

        # atualiza o cenario

        $sql4->execute("update cenario set 
		objetivo = '" . prepara_dado($objetivo) . "', 
		contexto = '" . prepara_dado($contexto) . "', 
		atores = '" . prepara_dado($atores) . "', 
		recursos = '" . prepara_dado($recursos) . "', 
		episodios = '" . prepara_dado($episodios) . "', 
		excecao = '" . prepara_dado($excecao) . "' 
		where id_cenario = $id_cenario ");

        // monta_relacoes($id_projeto);
        // Conecta ao SGBD
        $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $q = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_cenario
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // Para todos os cenarios
            $tituloEscapado = escapa_metacaracteres($titulo);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   // (2.2)
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
	                      VALUES (" . $result['id_cenario'] . ", $id_cenario)"; // (2.2.1)
                mysql_query($q) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            $tituloEscapado = escapa_metacaracteres($result['titulo']);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $episodios) != 0)) {   // (2.3)        
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_cenario, " . $result['id_cenario'] . ")"; //(2.4.1)

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }   // if
        }   // while


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
                $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = " . $result2['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);

                if ($resultArrayCen == false) {
                    $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, " . $result2['id_lexico'] . ")";
                    mysql_query($q) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                }
            }   // if
        }   // while
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
              AND id_cenario = $id_cenario";
        $count = count($nomesSinonimos);

        assert($count > 0);


        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result = mysql_fetch_array($qrr)) {    // verifica sinonimos dos lexicos no cenario inclu�do
                $nomeSinonimoEscapado = escapa_metacaracteres($nomesSinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $objetivo) != 0) ||
                        (preg_match($regex, $contexto) != 0) ||
                        (preg_match($regex, $atores) != 0) ||
                        (preg_match($regex, $recursos) != 0) ||
                        (preg_match($regex, $episodios) != 0) ||
                        (preg_match($regex, $excecao) != 0)) {

                    $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);

                    if ($resultArrayCen == false) {
                        $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, $id_lexicoSinonimo[$i])";
                        mysql_query($q) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                }   // if
            }   // while
        } //for
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um cenario ela deve receber os campos do cenario
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarCenario($id_projeto, $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $justificativa, $id_usuario) {

        assert($id_projeto > 0);
        assert($id_cenario > 0);

        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente
            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_cenario, '$titulo', '$objetivo', '$contexto', '$atores', '$recursos', '$excecao', '$episodios', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Cen�rio", "O usuario do sistema $nome\nPede para alterar o cenario $titulo \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else { //Eh gerente
            alteraCenario($id_projeto, $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um cenario ela deve receber
# o id do cenario e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# remove_scenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario"))) {

    function inserirPedidoRemoverCenario($id_projeto, $id_cenario, $id_usuario) {

        assert($id_projeto > 0);
        assert($id_cenario > 0);

        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $q = ("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto");
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //Nao e gerente
            $select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
            $cenario = $select->gofirst();
            $titulo = $cenario['titulo'];
            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, $id_cenario, '$titulo', $id_usuario, 'remover', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Cen�rio", "O usuario do sistema $nome\nPede para remover o cenario $id_cenario \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else {
            removeCenario($id_projeto, $id_cenario);
        }
    }

}
###################################################################
# Funcao faz um select na tabela cenario.
# Para inserir um novo cenario, deve ser verificado se ele ja existe.
# Recebe o id do projeto e o titulo do cenario (1.0)
# Faz um SELECT na tabela cenario procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################

function checarCenarioExistente($projeto, $titulo) {
    $naoexiste = false;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $q = "SELECT * FROM cenario WHERE id_projeto = $projeto AND titulo = '$titulo' ";
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }

    return $naoexiste;
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoCenario"))) {

    function tratarPedidoCenario($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        //print("<BR>SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        $select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_cenario = $record['id_cenario'];
                $id_projeto = $record['id_projeto'];
                removeCenario($id_projeto, $id_cenario);
                //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
            } else {

                $id_projeto = $record['id_projeto'];
                $titulo = $record['titulo'];
                $objetivo = $record['objetivo'];
                $contexto = $record['contexto'];
                $atores = $record['atores'];
                $recursos = $record['recursos'];
                $excecao = $record['excecao'];
                $episodios = $record['episodios'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_cenario'];
                    removeCenario($id_projeto, $id_cenario);
                    //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
                }
                adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
            }
            //$delete->execute ("DELETE FROM pedidocen WHERE id_pedido = $id_pedido") ;
        }
    }

}
?>
