<?php
session_start();

include_once 'bd.inc';
include_once 'funcoes_genericas.php';
include_once 'puts_links.php';
include_once 'httprequest.inc';
include_once 'seguranca.php';

check_user_authentication("index.php");        // Checa se o usuario foi autenticado


if (isset($_POST['flag'])) {
    $flag = "ON";
} else {
    $flag = "OFF";
}
?>

<?php
// gerador_xml.php
// Dada a base e o id do projeto, gera-se o xml
// dos cen�rios e l�xicos.
//Cen�rio - Gerar Relat�rios XML 
//Objetivo:    Permitir ao administrador gerar relat�rios em formato XML de um projeto, identificados por data.     
//Contexto:    Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado.
//Atores:    Administrador     
//Recursos:    Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.     
//Epis�dios:O sistema fornece para o administrador uma tela onde dever� fornecer os dados
//          do relat�rio para sua posterior identifica��o, como data e vers�o. 
//          Para efetivar a gera��o do relat�rio, basta clicar em Gerar. 
//          Restri��o: O sistema executar� duas valida��es: 
//                      - Se a data � v�lida.
//                      - Se existem cen�rios e l�xicos em datas iguais ou anteriores.
//          Gerando com sucesso o relat�rio a partir dos dados cadastrados do projeto,
//          o sistema fornece ao administrador a tela de visualiza��o do relat�rio XML criado. 
//          Restri��o: Recuperar os dados em XML do Banco de dados e os transformar por uma XSL para a exibi��o.      

if (!(function_exists("gerar_xml"))) {

    function generates_xml($database, $id_project, $date_search, $formatted_flag) {
        $xml_result = "";
        $empty_vector = array();

        if ($formatted_flag == "ON") {
            $xml_result = "";
            $xml_result = $xml_result . "<?xml-stylesheet type='text/xsl' href='projeto.xsl'?>\n";
        }
        $xml_result = $xml_result . "<projeto>\n";

        // Seleciona o nome do projeto

        $query_select_name_project = "SELECT nome
                     FROM projeto
                     WHERE id_projeto = " . $id_project;
        $table_project_name = mysql_query($query_select_name_project) or die("Erro ao enviar a query de selecao.");

        // Adiciona o nome do projeto no xml		
        $xml_result = $xml_result . "<nome>" . mysql_result($table_project_name, 0) . "</nome>\n";

        ## CEN�RIOS ##
        // Seleciona os cen�rios de um projeto.

        $query_select_scenery_project = "SELECT id_cenario ,
                               titulo ,
                               objetivo ,
                               contexto ,
                               atores ,
                               recursos ,
                               episodios ,
                               excecao
                        FROM   cenario
                        WHERE  (id_projeto = " . $id_project
                . ") AND (data <=" . " '" . $date_search . "'" . ")
                        ORDER BY id_cenario,data DESC";

        $table_scenary = mysql_query($query_select_scenery_project) or die("Erro ao enviar a query de selecao.");

        $while_is_true = true;

        $id_temp = "";

        $lexicon_vector = load_lexicon_vector($id_project, 0, false);

        // Para cada cen�rio

        while ($row = mysql_fetch_row($table_scenary)) {
            $id_cenario = "<ID>" . $row[0] . "</ID>";
            $id_current_scenario = $row[0];
            $scenery_vector = load_scenario_vector($id_project, $id_current_scenario, true);

            // Porque usa $id_temp != $id_cenario ? e a variavel primeiro

            if (($id_temp != $id_cenario) or (while_is_true)) {
                $scene_title = '<titulo id="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . ucwords(strip_tags($row[1])) . '</titulo>';

                $scene_goal = "<objetivo>" . "<sentenca>" . generate_xml_links(build_links($row[2], $lexicon_vector, $empty_vector)) . "</sentenca>" . "<PT/>" . "</objetivo>";

                $scene_context = "<contexto>" . "<sentenca>" . generate_xml_links(build_links($row[3], $lexicon_vector, $scenery_vector)) . "</sentenca>" . "<PT/>" . "</contexto>";

                $scene_performer = "<atores>" . "<sentenca>" . generate_xml_links(build_links($row[4], $lexicon_vector, $empty_vector)) . "</sentenca>" . "<PT/>" . "</atores>";

                $scene_resource = "<recursos>" . "<sentenca>" . generate_xml_links(build_links($row[5], $lexicon_vector, $empty_vector)) . "</sentenca>" . "<PT/>" . "</recursos>";

                $scene_exception = "<excecao>" . "<sentenca>" . generate_xml_links(build_links($row[7], $lexicon_vector, $empty_vector)) . "</sentenca>" . "<PT/>" . "</excecao>";

                $scene_episode = "<episodios>" . "<sentenca>" . generate_xml_links(build_links($row[6], $lexicon_vector, $scenery_vector)) . "</sentenca>" . "<PT/>" . "</episodios>";

                $xml_result = $xml_result . "<cenario>\n";

                $xml_result = $xml_result . "$scene_title\n";

                $xml_result = $xml_result . "$scene_goal\n";

                $xml_result = $xml_result . "$scene_context\n";

                $xml_result = $xml_result . "$scene_performer\n";

                $xml_result = $xml_result . "$scene_resource\n";

                $xml_result = $xml_result . "$scene_episode\n";

                $xml_result = $xml_result . "$scene_exception\n";

                $xml_result = $xml_result . "</cenario>\n";

                $while_is_true = false;

                //??$id_temp = id_cenario;
            }
        } // while dos cen�rios
        // Seleciona os lexicos de um projeto.

        $query_lexico = "SELECT id_lexico ,
                               nome ,
                               nocao ,
                               impacto
                        FROM   lexico
                        WHERE  (id_projeto = " . $id_project .
                ") AND (data <=" . " '" . $date_search . "'" . ")

                ORDER BY id_lexico,data DESC";

        $table_lexico = mysql_query($query_lexico) or die("Erro ao enviar a query de selecao.");

        $while_is_true = true;

        $id_temp = "";

        // Para cada simbolo do lexico

        while ($row = mysql_fetch_row($table_lexico)) {
            $lexicon_vector = load_lexicon_vector($id_project, $row[0], true);
            quicksort($lexicon_vector, 0, count($lexicon_vector) - 1, 'lexico');
            $id_lexicon = "<ID>" . $row[0] . "</ID>";
            if (($id_temp != $id_lexicon) or (primeiro)) {

                $name = '<nome_simbolo id="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags($row[1])) . '</texto>' . '</nome_simbolo>';


                // Consulta os sinonimos do simbolo
                $query_consult_sinonimous = "SELECT nome 
									FROM sinonimo
									WHERE (id_projeto = " . $id_project . ") 
									AND (id_lexico = " . $row[0] . " )";

                $resultSinonimos = mysql_query($query_consult_sinonimous) or die("Erro ao enviar a query de selecao de sinonimos.");

                //Para cada sinonimo do simbolo
                $sinonimous = "<sinonimos>";

                while ($rowSin = mysql_fetch_row($resultSinonimos)) {
                    $sinonimous .= "<sinonimo>" . $rowSin[0] . "</sinonimo>";
                }
                $sinonimous .= "</sinonimos>";

                $notion = "<nocao>" . "<sentenca>" . generate_xml_links(build_links($row[2], $lexicon_vector, $empty_vector)) . "<PT/>" . "</sentenca>" . "</nocao>";

                $impact = "<impacto>" . "<sentenca>" . generate_xml_links(build_links($row[3], $lexicon_vector, $empty_vector)) . "<PT/>" . "</sentenca>" . "</impacto>";

                $xml_result = $xml_result . "<lexico>\n";

                $xml_result = $xml_result . "$name\n";

                $xml_result = $xml_result . "$sinonimous\n";

                $xml_result = $xml_result . "$notion\n";

                $xml_result = $xml_result . "$impact\n";

                $xml_result = $xml_result . "</lexico>\n";

                $while_is_true = false;

                //$id_temp = id_lexico;
            }
        } // while

        $xml_result = $xml_result . "</projeto>\n";

        return $xml_result;
    }

// gerar_xml
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//
//Cen�rio - Gerar links nos Relat�rios XML criados
//
//Objetivo:    Permitir que os relat�rios gerados em formato XML possuam termos com links 
//          para os seus respectivos l�xicos
//
//Contexto:    Gerente deseja gerar um relat�rio em XML para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado, acesso ao banco de dados.
//
//Atores:    Sistema    
//
//Recursos:    Sistema, senten�as a serem linkadas, dados cadastrados do projeto, banco de dados. 
//    
//Epis�dios:O sistema recebe a senten�a com os tags pr�prios do C&L e retorna o c�digo do link HTML
//            equivalente para os l�xicos cadatrados no sistema. 
//     
///////////////////////////////////////////////////////////////////////////////////////////////////
//
//L�xicos:
//
//     Fun��o:            gera_xml_links
//     Descri��o:         Analisa uma senten�a recebida afim de identificar as tags utilizadas no C&L
//                        para linkar os l�xicos e transformar em links XML.
//     Sin�nimos:         -
//     Exemplo: 
//        ENTRADA: <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre</a>
//                 <!--/CL-->
//        SA�DA:  <a title="Lexico" href="main.php?t=l&id=228"><texto referencia_lexico=software 
//                livre>software livre</texto></a>
//
//     Vari�vel:            $sentenca
//     Descri��o:         Armazena a express�o passada por argumento a ser tranformada em link.
//     Sin�nimos:         -
//     Exemplo:             <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre
//                        </a><!--/CL-->
//
//     Vari�vel:            $regex
//     Descri��o:            Armazena o pattern a ser utilizado ao se separar a senten�a.
//     Sin�nimos:            -
//     Exemplo:            "/(<!--CL:tam:\d+-->(<a[^>]*?\>)([^<]*?)<\/a><!--\/CL-->)/mi"
//
//     Vari�vel:            $vetor_texto
//     Descri��o:         Array que armazena palavra por palavra a sente�a a ser linkada, sem o tag.
//     Sin�nimos:         -
//     Exemplo:             $vetor_texto[0] => software
//                        $vetor_texto[1] => livre
//
//     Vari�vel:            $inside_tag
//     Descri��o:         Determina se a an�lise est� sendo feita dentro ou fora do tag
//     Sin�nimos:         -
//     Exemplo:             false
//
//     Vari�vel:            $tamanho_vetor_texto
//     Descri��o:         Armazena a n�mero de palavras que se encontram no array $vetor_texto. 
//     Sin�nimos:         -
//     Exemplo:             2
//
//     Vari�vel:            $i
//     Descri��o:         Vari�vel utilizada como um contador para uso gen�rico.
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $match
//     Descri��o:         Armazena o valor 1 caso a string "/href="main.php\?t=(.)&id=(\d+?)"/mi"
//                        seja encontrada na no array $vetor_texto. Caso contr�rio, armazena 0.
//     Sin�nimos:         -
//     Exemplo:             0
//
//     Vari�vel:            $id_projeto
//     Descri��o:         Armazena o n�mero identificador do projeto corrente.
//     Sin�nimos:         -
//     Exemplo:             1
//
//     Vari�vel:            $atributo
//     Descri��o:         Armazena um tag que indica a refer�ncia para um l�xico
//     Sin�nimos:         -
//     Exemplo:             referencia_lexico
//
//     Vari�vel:            $query
//     Descri��o:         Armazena a consulta a ser feita no banco de dados
//     Sin�nimos:         -
//     Exemplo:             SELECT nome FROM lexico WHERE id_projeto = $id_projeto
//
//     Vari�vel:            $result
//     Descri��o:         Armazena o resultado da consulta feita ao banco de dados
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $row
//     Descri��o:         Array que armazena tupla a tupla o resultado da consulta realizada
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $valor
//     Descri��o:         Armazena uma tupla, substituindo os caracteres acentuados pelos seus 
//                        equivalentes sem acentua��o.
//     Sin�nimos:         -
//     Exemplo:             acentuacao
//
///////////////////////////////////////////////////////////////////////////////////////////////////


if (!(function_exists("gera_xml_links"))) {

    function generate_xml_links($sentence) {

        if (trim($sentence) != "") {

            $regex = "/(<a[^>]*?>)(.*?)<\/a>/";

            $text_vector = preg_split($regex, $sentence, -1, PREG_SPLIT_DELIM_CAPTURE);
            $text_vector_size = count($text_vector);
            $i = 0;


            while ($i < $text_vector_size) {
                preg_match('/href="main.php\?t=(.)&id=(\d+?)"/mi', $text_vector[$i], $match);
                if ($match) {
                    $id_project = $_SESSION['id_projeto_corrente'];

                    // Verifica se � l�xico 
                    if ($match[1] == 'l') {
                        // Retira o link do texto
                        $text_vector[$i] = "";

                        //link para l�xico
                        $lexicon_link = "referencia_lexico";

                        $query = "SELECT nome FROM lexico WHERE id_projeto = $id_project AND id_lexico = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query lexico");
                        $row = mysql_fetch_row($result);
                        // Pega o nome do l�xico
                        $get_name = strtr($row[0], "����������", "aaaaoooeec");

                        $text_vector[$i + 1] = '<texto ' . $lexicon_link . '="' . $get_name . '">' . $text_vector[$i + 1] . '</texto>';
                    } else if ($match[1] == 'c') {
                        // Retira o link do texto
                        $text_vector[$i] = "";

                        //link para cen�rio
                        $lexicon_link = "referencia_cenario";

                        $query = "SELECT titulo FROM cenario WHERE id_projeto = $id_project AND id_cenario = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query cenario");
                        $row = mysql_fetch_row($result);
                        // Pega o titulo do cenario
                        $get_name = strtr($row[0], "����������", "aaaaoooeec");

                        $text_vector[$i + 1] = '<texto ' . $lexicon_link . '="' . $get_name . '">' . strip_tags($text_vector[$i + 1]) . '</texto>';
                    }

                    $i = $i + 2;
                } else {
                    if (trim($text_vector[$i]) != "") {
                        $text_vector[$i] = "<texto>" . $text_vector[$i] . "</texto>";
                    }

                    $i = $i + 1;
                }
            }
            // Junta os elementos do array vetor_texto em uma string
            return implode("", $text_vector);
        }
        return $sentence;
    }

}
?>

<?php
$id_project = $_SESSION['id_projeto_corrente'];
$date_search = $data_ano . "-" . $data_mes . "-" . $data_dia;
$formatted_flag_ = $flag;

// Abre base de dados.
$testing_database_opening = database_connect() or die("Erro ao conectar ao SGBD");

$qVerifica = "SELECT * FROM publicacao WHERE id_projeto = '$id_project' AND versao = '$version' ";
$qrrVerifica = mysql_query($qVerifica);

// Se n�o existir nenhum XML com o id passado ele cria
if (!mysql_num_rows($qrrVerifica)) {

    $str_xml = generates_xml($testing_database_opening, $id_project, $date_search, $flag_formatado);

    $xml_resultante = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n" . $str_xml;

    $query_database_command = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$id_project', '$date_search', '$version', '" . mysql_real_escape_string($xml_resultante) . "')";

    mysql_query($query_database_command) or die("Erro ao enviar a query INSERT do XML no banco de dados! ");
    recarrega("http://pes.inf.puc-rio.br/cel/aplicacao/mostraXML.php?id_projeto=" . $id_project . "&versao=" . $version);
} else {
    ?>
    <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
            <p style="color: red; font-weight: bold; text-align: center">Essa vers�o j� existe!</p>
            <br>
            <br>
        <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
    </body></html>

    <?php
}
?> 
