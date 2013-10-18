<?php

require_once 'bd.inc';
require_once 'seguranca.php';

database_connect();

###################################################################
# Insere um projeto no banco de dados.
# Recebe o nome e descricao. (1.1)
# Verifica se este usuario ja possui um projeto com esse nome. (1.2)
# Caso nao possua, insere os valores na tabela PROJETO. (1.3)
# Devolve o id_cprojeto. (1.4)
#
###################################################################
if (!(function_exists("inclui_projeto"))) {

    function inclui_projeto($nome, $descricao) {
        $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //verifica se usuario ja existe
        $qv = "SELECT * FROM projeto WHERE nome = '$nome'";
        $qvr = mysql_query($qv) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //$result = mysql_fetch_row($qvr);
        $resultArray = mysql_fetch_array($qvr);


        if ($resultArray != false) {
            //verifica se o nome existente corresponde a um projeto que este usuario participa
            $id_projeto_repetido = $resultArray['id_projeto'];

            $id_usuario_corrente = $_SESSION['id_usuario_corrente'];

            $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' AND id_usuario = '$id_usuario_corrente' ";

            $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

            $resultArray = mysql_fetch_row($qvuv);

            if ($resultArray[0] != null) {
                return -1;
            }
        }

        $q = "SELECT MAX(id_projeto) FROM projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);

        if ($result[0] == false) {
            $result[0] = 1;
        } else {
            $result[0]++;
        }
        $data = date("Y-m-d");

        $qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'" . prepara_dado($nome) . "','$data' , '" . prepara_dado($descricao) . "')";

        mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        return $result[0];
    }

}
###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################

function removeProjeto($id_projeto) {
    $r = database_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de cenario
    $qv = "Delete FROM pedidocen WHERE id_projeto = '$id_projeto' ";
    $deletaPedidoCenario = mysql_query($qv) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de lexico
    $qv = "Delete FROM pedidolex WHERE id_projeto = '$id_projeto' ";
    $deletaPedidoLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os lexicos //verificar lextolex!!!
    $qv = "SELECT * FROM lexico WHERE id_projeto = '$id_projeto' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_lexico']; //seleciona um lexico

        $qv = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' OR id_lexico_to = '$id_lexico' ";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //$qv = "Delete FROM sinonimo WHERE id_lexico = '$id_lexico'";
        //$deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM sinonimo WHERE id_projeto = '$id_projeto'";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM lexico WHERE id_projeto = '$id_projeto' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove os cenarios
    $qv = "SELECT * FROM cenario WHERE id_projeto = '$id_projeto' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArrayCenario = mysql_fetch_array($qvr);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_cenario']; //seleciona um lexico

        $qv = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario' OR id_cenario_to = '$id_cenario' ";
        $deletaCentoCen = mysql_query($qv) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM cenario WHERE id_projeto = '$id_projeto' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover participantes
    $qv = "Delete FROM participa WHERE id_projeto = '$id_projeto' ";
    $deletaParticipantes = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover publicacao
    $qv = "Delete FROM publicacao WHERE id_projeto = '$id_projeto' ";
    $deletaPublicacao = mysql_query($qv) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover projeto
    $qv = "Delete FROM projeto WHERE id_projeto = '$id_projeto' ";
    $deletaProjeto = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
}

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
?>
