<?php
session_start();

include_once 'CELConfig/CELConfig.inc';
include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';
include_once 'puts_links.php';
include_once 'frame_inferior.php';


/* URL of the directory containing the DAML files */
$_SESSION['site'] = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

/* Path from directory containing DAML files relative to CEL */
$_SESSION['diretorio'] = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

check_user_authentication("index.php");

//Gets parameter from heading.php. Will crash without it, since variable was uninitialized
if (isset($_GET['id_projeto'])) {
    $id_project = $_GET['id_projeto'];
} else {
    // $id_projeto = ""; 
}

if (!isset($_SESSION['id_projeto_corrente'])) {

    $_SESSION['id_projeto_corrente'] = "";
}
?>    

<html> 
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">

    <head> 
        <LINK rel="stylesheet" type="text/css" href="View/Shared/Css/style.css"> 
        <script language="javascript1.3">
            // Functions that will be used when the script is called through himself or the tree
            function recharge(URL) {
                document.location.replace(URL);
            }

<?php
/*
 * Scenario - Update Scenario
 * Objective:   Allow inclusion, alteration and exclusion of a scenario, by a user.
 * Context:     User wishes to include a new scenario, or alter/exclude an old one.
 *              Pre-condition: logged in
 * Actors:      User, project manager
 * Resources:   System, upper menu, object to be modified
 * Episodes:    1- User clicks on Alter, then ALTER SCENARIO
 *              2- User clicks on Exclude, then EXCLUDE SCENARIO
 * 
 */
?>

            function changes_scenario(scenario) {
                var url = 'change_scene.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + scenario;
                var where = '_blank';
                var window_spec = 'dependent,height=660,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function removes_scenario(scenario) {
                var url = 'remove_scenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + scenario;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
/*
 * Scenario - Update Lexicon
 * Objective:   Allow inclusion, alteration and exclusion of a lexicon, by a user.
 * Context:     User wishes to include a new lexicon, or alter/exclude an old one.
 *              Pre-condition: logged in
 * Actors:      User, project manager
 * Resources:   System, upper menu, object to be modified
 * Episodes:    1- User clicks on Alter, then ALTER LEXICON
 *              2- User clicks on Exclude, then EXCLUDE LEXICON
 * 
 */
?>

            function changes_lexicon(lexico) {
                var url = 'change_lexicon.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function removes_lexicon(lexico) {
                var url = 'remove_lexicon.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }


<?php
/*
 * Scenario - Update Concept
 * Objective:   Allow inclusion, alteration and exclusion of a concept, by a user.
 * Context:     User wishes to include a new concept, or alter/exclude an old one.
 *              Pre-condition: logged in
 * Actors:      User, project manager
 * Resources:   System, upper menu, object to be modified
 * Episodes:    1- User clicks on Alter, then ALTER CONCEPT
 *              2- User clicks on Exclude, then EXCLUDE CONCEPT
 * 
 */
?>

            function change_concept(concept) {
                var url = 'alt_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + concept;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function remove_conceito(concept) {
                var url = 'remove_concept.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + concept;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function remove_relationship(relationship) {

                var url = 'remove_relation.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_relacao=' + relationship;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
/*
 * Scenario - Administrator chooses project
 * Objective:   Allow the administrator to choose a project
 * Context:     The administrator wishes to choose a project
 *              Pre-conditions: Logged in, be administrator of the selected project
 * Actors:      Administator
 * Resources:   Projects of the administrator
 * Episodes:    1- The administrator selects a project which he administrates, from
 *                  a list of projects
 *              Options, in that case:
 *              2- Verify scenario alteration requests
 *              3- Verify lexicon term alteration requests
 *              4- Verify concept alteration requests
 *              5- Add user to project
 *              6- Relate users to project
 *              7- Generate XML from this project
 */
?>

            function request_scene() {
<?php
if (isset($id_project)) {
    ?>
                    var url = 'View/ver_pedido_cenario.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'View/ver_pedido_cenario.php';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function request_lexicon() {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'View/ver_pedido_lexico.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'View/ver_pedido_lexico.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function request_concept() {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'View/ver_pedido_conceito.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'View/ver_pedido_conceito.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function request_relationship() {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'View/ver_pedido_relacao.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'View/ver_pedido_relacao.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function add_user() {
                var url = 'View/adds_user.php';
                var where = '_blank';
                var window_spec = 'dependent,height=320,width=490,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function relationship_user() {
                var url = 'View/rel_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=380,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function generates_XML()
            {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'form_xml.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'form_xml.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function recuperates_XML()
            {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'recuperarXML.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'recuperarXML.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function generates_grafo()
            {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'gerarGrafo.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'gerarGrafo.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }


<?php
// Objetivo:  Gerar ontologia do projeto 
?>
            function generates_Ontology()
            {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'inicio.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'inicio.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = "";
                open(url, where, window_spec);
            }

<?php
// Ontologia - DAML 
// Objetivo:  Gerar daml deste da ontologia do projeto 
?>
            function generates_DAML()
            {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'form_daml.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'form_daml.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=375,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Objetivo : Recuperar hist�rico da ontologia em DAML 
?>
            function recuperates_DAML()
            {

<?php
if (isset($id_project)) {
    ?>
                    var url = 'recuperaDAML.php?id_projeto=' + '<?= $id_project ?>';
    <?php
} else {
    ?>
                    var url = 'recuperaDAML.php?';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }


        </script> 
        <script type="text/javascript" src="/Shared/mtmtrack.js">
        </script> 
    </head> 
    <body> 

        <!--                     PRIMEIRA PARTE                                     --> 

        <?php
        if (isset($id) && isset($t)) {      // SCRIPT CHAMADO PELO PROPRIO MAIN.PHP (OU PELA ARVORE) 
            $vetorVazio = array();
            if ($t == "c") {
                print "<h3>Informa��es sobre o cenário</h3>";
            } elseif ($t == "l") {
                print "<h3>Informações sobre o símbolo</h3>";
            } elseif ($t == "oc") {
                print "<h3>Informações sobre o conceito</h3>";
            } elseif ($t == "or") {
                print "<h3>Informa��es sobre a relação</h3>";
            } elseif ($t == "oa") {
                print "<h3>Informações sobre o axioma</h3>";
            }
            ?>    
            <table> 





                <?php
                $conexaoComBanco = database_connect() or die("Erro ao conectar ao SGBD");
                ?>   



                <!-- CEN�RIO --> 

                <?php
                if ($t == "c") {
                    $query_database_command = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_projeto    
              FROM cenario    
              WHERE id_cenario = $id";

                    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    $result = mysql_fetch_array($query_connecting_database);

                    $c_id_projeto = $result['id_projeto'];

                    $vetor_of_cenarios = load_scenario_vector($c_id_projeto, $id, true);
                    quicksort($vetor_of_cenarios, 0, count($vetor_of_cenarios) - 1, 'cenario');

                    $vetor_of_lexicos = load_lexicon_vector($c_id_projeto, 0, false);
                    quicksort($vetor_of_lexicos, 0, count($vetor_of_lexicos) - 1, 'lexico');
                    ?>    

                    <tr> 
                        <th>Titulo:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['titulo'], $vetor_of_lexicos, $vetorVazio)); ?>
                        </td> 

                    </tr> 
                    <tr> 
                        <th>Objetivo:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['objetivo'], $vetor_of_lexicos, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Contexto:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['contexto'], $vetor_of_lexicos, $vetor_of_cenarios)); ?>		 
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Atores:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['atores'], $vetor_of_lexicos, $vetorVazio)); ?>
                        </td>  
                    </tr> 
                    <tr> 
                        <th>Recursos:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['recursos'], $vetor_of_lexicos, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Exce��o:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['excecao'], $vetor_of_lexicos, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Epis�dios:</th><td CLASS="Estilo">
                            <?php echo nl2br(build_links($result['episodios'], $vetor_of_lexicos, $vetor_of_cenarios)); ?>

                        </td> 
                    </tr> 
                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign=MIDDLE> 
                            <a href="#" onClick="changes_scenario(<?= $result['id_cenario'] ?>);">Alterar Cenário</a> 
                            </th> 
                        <td CLASS="Estilo"  valign=MIDDLE> 
                            <a href="#" onClick="removes_scenario(<?= $result['id_cenario'] ?>);">Remover Cenário</a> 
                            </th> 
                    </tr> 


                    <!-- L�XICO --> 

                    <?php
                } elseif ($t == "l") {

                    $query_database_command = "SELECT id_lexico, nome, nocao, impacto, tipo, id_projeto    
              FROM lexico    
              WHERE id_lexico = $id";

                    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    $result = mysql_fetch_array($query_connecting_database);

                    $l_id_projeto = $result['id_projeto'];

                    $vetor_of_lexicos = load_lexicon_vector($l_id_projeto, $id, true);

                    quicksort($vetor_of_lexicos, 0, count($vetor_of_lexicos) - 1, 'lexico');
                    ?>    
                    <tr> 
                        <th>Nome:</th><td CLASS="Estilo"><?php echo $result['nome']; ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>No��o:</th><td CLASS="Estilo"><?php echo nl2br(build_links($result['nocao'], $vetor_of_lexicos, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Classifica��o:</th><td CLASS="Estilo"><?= nl2br($result['tipo']) ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Impacto(s):</th><td CLASS="Estilo"><?php echo nl2br(build_links($result['impacto'], $vetor_of_lexicos, $vetorVazio)); ?> 
                        </td>
                    </tr> 
                    <tr> 
                        <th>Sin�nimo(s):</th> 

                        <?php
                        //sinonimos 
                        $id_project = $_SESSION['id_projeto_corrente'];
                        $$query_sinonimous = "SELECT * FROM sinonimo WHERE id_lexico = $id";
                        $query_connecting_database = mysql_query($$query_sinonimous) or die("Erro ao enviar a query de Sinonimos" . mysql_error());

                        $$temporary_variable_sinonimous = array();

                        while ($resultSinonimo = mysql_fetch_array($query_connecting_database)) {
                            $temporary_variable_sinonimous[] = $resultSinonimo['nome'];
                        }
                        ?>    

                        <td CLASS="Estilo">

                            <?php
                            $counter = count($$temporary_variable_sinonimous);

                            for ($i = 0; $i < $counter; $i++) {
                                if ($i == $counter - 1) {
                                    echo $$temporary_variable_sinonimous[$i] . ".";
                                } else {
                                    echo $$temporary_variable_sinonimous[$i] . ", ";
                                }
                            }
                            ?>    

                        </td> 

                    </tr> 
                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign="middle"> 
                            <a href="#" onClick="changes_lexicon(<?= $result['id_lexico'] ?>);">Alterar Símbolo</a> 
                            </th> 
                        <td CLASS="Estilo" valign="middle"> 
                            <a href="#" onClick="removes_lexicon(<?= $result['id_lexico'] ?>);">Remover Símbolo</a> 
                            </th> 
                    </tr> 


                    <!-- ONTOLOGIA - CONCEITO --> 

                    <?php
                } elseif ($t == "oc") {        // se for cenario 
                    $query_database_command = "SELECT id_conceito, nome, descricao   
              FROM   conceito   
              WHERE  id_conceito = $id";

                    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    $result = mysql_fetch_array($query_connecting_database);
                    ?>    

                    <tr> 
                        <th>Nome:</th><td CLASS="Estilo"><?= $result['nome'] ?></td> 
                    </tr> 
                    <tr> 
                        <th>Descri��o:</th><td CLASS="Estilo"><?= nl2br($result['descricao']) ?></td> 
                    </tr> 
                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign=MIDDLE>                     
                            </th> 
                        <td CLASS="Estilo"  valign=MIDDLE> 
                            <a href="#" onClick="rmvConceito(<?= $result['id_conceito'] ?>);">Remover Conceito</a> 
                            </th> 
                    </tr> 




                    <!-- ONTOLOGIA - RELA��ES --> 

                    <?php
                } elseif ($t == "or") {        // se for cenario 
                    $query_database_command = "SELECT id_relacao, nome   
              FROM relacao   
              WHERE id_relacao = $id";
                    $query_connecting_database = mysql_query($query_database_command) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    $result = mysql_fetch_array($query_connecting_database);
                    ?>    

                    <tr> 
                        <th>Nome:</th><td CLASS="Estilo"><?= $result['nome'] ?></td> 
                    </tr> 

                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign=MIDDLE>                   
                            </th>
                        <td CLASS="Estilo"  valign=MIDDLE> 
                            <a href="#" onClick="remove_relationship(<?= $result['id_relacao'] ?>);">Remover Relação</a> 
                            </th> 
                    </tr> 




                    <?php
                }
                ?>   

            </table> 
            <br> 





            <?php
            if ($t == "c") {
                print "<h3>Cenários que referenciam este cenário</h3>";
            } elseif ($t == "l") {
                print "<h3>Cen�rios e termos do léxico que referenciam este termo</h3>";
            } elseif ($t == "oc") {
                print "<h3>Relaçoes do conceito</h3>";
            } elseif ($t == "or") {
                print "<h3>Conceitos referentes a relação</h3>";
            } elseif ($t == "oa") {
                print "<h3>Axioma</h3>";
            }
            ?>   








            <?php
            frame_inferior($conexaoComBanco, $t, $id);
        } elseif (isset($id_project)) {         // SCRIPT CHAMADO PELO HEADING.PHP 
            // Foi passada uma variavel $id_projeto. Esta variavel deve conter o id de um 
            // projeto que o usuario esteja cadastrado. Entretanto, como a passagem eh 
            // feita usando JavaScript (no heading.php), devemos checar se este id realmente 
            // corresponde a um projeto que o usuario tenha acesso (seguranca). 
            check_proj_perm($_SESSION['id_usuario_corrente'], $id_project) or die("Permissao negada");

            // Seta uma variavel de sessao correspondente ao projeto atual 
            $_SESSION['id_projeto_corrente'] = $id_project;
            ?>    

            <table ALIGN=CENTER> 
                <tr> 
                    <th>Projeto:</th> 
                    <td CLASS="Estilo"><?= simple_query("nome", "projeto", "id_projeto = $id_project") ?></td> 
                </tr> 
                <tr> 
                    <th>Data de criação:</th> 
                    <?php
                    $data = simple_query("data_criacao", "projeto", "id_projeto = $id_project");
                    ?>    

                    <td CLASS="Estilo"><?= formataData($data) ?></td> 

                </tr> 
                <tr> 
                    <th>Descrição:</th> 
                    <td CLASS="Estilo"><?= nl2br(simple_query("descricao", "projeto", "id_projeto = $id_project")) ?></td> 
                </tr> 
            </table> 

            <?php
// Cen�rio - Escolher Projeto 
// Objetivo:      Permitir ao Administrador/Usu�rio escolher um projeto. 
// Contexto:      O Administrador/Usu�rio deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser Administrador 
// Atores:        Administrador, Usu�rio 
// Recursos:      Usu�rios cadastrados 
// Epis�dios:     Caso o Usuario selecione da lista de projetos um projeto da qual ele seja 
//                administrador, ver Administrador escolhe Projeto. 
//                Caso contr�rio, ver Usu�rio escolhe Projeto. 
            // Verifica se o usuario eh administrador deste projeto 
            if (is_admin($_SESSION['id_usuario_corrente'], $id_project)) {
                ?>    

                <br> 
                <table ALIGN=CENTER> 
                    <tr> 
                        <th>Você é um administrador deste projeto:</th> 

                        <?php
// Cen�rio - Administrador escolhe Projeto 
// Objetivo:  Permitir ao Administrador escolher um projeto. 
// Contexto:  O Administrador deseja escolher um projeto. 
// Pr�-Condi��es: Login, Ser administrador do projeto selecionado. 
// Atores:    Administrador 
// Recursos:  Projetos doAdministrador 
// Epis�dios: O Administrador seleciona da lista de projetos um projeto da qual ele seja 
//            administrador. 
//            Aparecendo na tela as op��es de: 
//            -Verificar pedidos de altera��o de cen�rio (ver Verificar pedidos de altera��o 
//            de cen�rio); 
//            - Verificar pedidos de altera��o de termos do l�xico 
//            ( ver Verificar pedidos de altera��o de termos do l�xico); 
//            -Adicionar usu�rio (n�o existente) neste projeto (ver Adicionar Usu�rio); 
//            -Relacionar usu�rios j� existentes com este projeto 
//            (ver Relacionar usu�rios com projetos); 
//            -Gerar xml deste projeto (ver Gerar relat�rios XML); 
                        ?>    
                    </TR>

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="add_user();">Adicionar usuário (não cadastrado) neste projeto</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="relationship_user();">Adicionar usuários já existentes neste projeto</a></td> 
                    </TR>   

                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="request_scene();">Verificar pedidos de alteração de Cenários</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="request_lexicon();">Verificar pedidos de alteração de termos do Léxico</a></td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="request_concept();">Verificar pedidos de alteração de Conceitos</a></td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="request_relationship();">Verificar pedidos de alteração de Relações</a></td> 
                    </TR>


                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="generates_grafo();" >Gerar grafo deste projeto</a></td>
                    </TR>       
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="generates_XML();">Gerar XML deste projeto</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="recuperates_XML();">Recuperar XML deste projeto</a></td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="generates_Ontology();">Gerar ontologia deste projeto</a></td> 
                    </TR>            
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="generates_DAML();">Gerar DAML da ontologia do projeto</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="recuperates_DAML();">Histórico em DAML da ontologia do projeto</a></td> 
                    </TR>           
                    <TR> 
                        <td CLASS="Estilo"><a href="http://www.daml.org/validator/" target="new">*Validador de Ontologias na Web</a></td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo"><a href="http://www.daml.org/2001/03/dumpont/" target="new">*Visualizador de Ontologias na Web</a></td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo"><font size="1">*Para usar Ontologias Geradas pelo C&L: </font></td>               
                    </TR>
                    <TR> 
                        <td CLASS="Estilo">   <font size="1">Histórico em DAML da ontologia do projeto -> Botao Direito do Mouse -> Copiar Atalho</font></td>             
                    </TR>
                </table>


                <?php
            } else {
                ?>	
                <br>
                <table ALIGN=CENTER> 
                    <tr> 
                        <th>Você não é um administrador deste projeto:</th> 	
                    </tr>	
                    <tr> 
                        <td CLASS="Estilo"><a href="#" onClick="generates_grafo();" >Gerar grafo deste projeto</a></td>
                    </tr>  
                </table>			
                <?php
            }
        } else {        // SCRIPT CHAMADO PELO INDEX.PHP 
            ?>    

            <p>Selecione um projeto acima, ou crie um novo projeto.</p> 

            <?php
        }
        ?>    
        <i><a href="showSource.php?file=main.php">Veja o código fonte!</a></i> 
    </body> 

</html> 

