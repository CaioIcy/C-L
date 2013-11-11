<?php
/* File: code.php
 * Purpose: This script does something
 */
session_start();

include_once 'bd.inc';
include_once 'funcoes_genericas.php';

if (isset($_GET['id_projeto'])) {
    $id_project = $_GET['id_projeto'];
}


check_user_authentication("index.php");   //Checks if user is authenticated
//$id_projeto = 2; 
?>  

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 

<?php
// Connects to SGBD 
$database_conection = database_connect() or die("Error while connecting to SGBD");


/*
 * If &id_project is set, it corresponds to the ID of the project that is
 * to be shown. If not set, then, by default, we will not show any project.
 * Instead, we will wait for the user to choose a project (through JavaScript)
 * in heading.php. We should check if the ID is actually corresponding to a
 * project that the user has access to
 */
if (isset($id_project)) {
    check_proj_perm($_SESSION['id_usuario_corrente'], $id_project) or die("Permission denied");
    $query_database_command = "SELECT nome FROM projeto WHERE id_projeto = $id_project";
    $query_connecting_database = mysql_query($query_database_command) or die("Error while sending the query");
    $result = mysql_fetch_array($query_connecting_database);
    $project_name = $result['nome'];
} else {
    ?>  

    <script language="javascript1.3">

        top.frames['menu'].document.writeln('<font color="red">No project selected</font>');

    </script> 

    <?php
    exit();
}
?>  

<html> 
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <head> 
        <!--figure out if this script should be used or not-->
        <script type="text/javascript">
            /*
             * Framebuster script to relocate browser when MSIE bookmarks this
             * page instead of the parent frameset.  Set variable relocateURL
             * to the index document of your website (relative URLs are ok)
             */

            /*var relocateURL = "/"; 
             
             if (parent.frames.length == 0) { 
             if(document.images) { 
             location.replace(relocateURL); 
             } else { 
             location = relocateURL; 
             } 
             }*/
        </script> 

        <script type="text/javascript" src="mtmcode.js">
        </script> 

        <script type="text/javascript">
            // Morten's JavaScript Tree Menu 
            // version 2.3.2, dated 2002-02-24 
            // http://www.treemenu.com/ 

            // Copyright (c) 2001-2002, Morten Wang & contributors 
            // All rights reserved. 

            // This software is released under the BSD License which should accompany 
            // it in the file "COPYING".  If you do not have this file you can access 
            // the license through the WWW at http://www.treemenu.com/license.txt 

            // Nearly all user-configurable options are set to their default values. 
            // Have a look at the section "Setting options" in the installation guide 
            // for description of each option and their possible values. 

            MTMDefaultTarget = "text";
            MTMenuText = "<?= $project_name ?>";

            /****************************************************************************** 
             * User-configurable list of icons.                                            * 
             ******************************************************************************/

            var MTMIconList = null;
            MTMIconList = new IconList();
            MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre"));
            MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post"));

            /****************************************************************************** 
             * User-configurable menu.                                                     * 
             ******************************************************************************/

            var menu = null;
            menu = new MTMenu();
            menu.addItem("Scenarios");
            // + submenu 
            var mc = null;
            mc = new MTMenu();

<?php
$query_database_command = "SELECT id_cenario, titulo  
                  FROM cenario  
                  WHERE id_projeto = $id_project  
                  ORDER BY titulo";

$query_connecting_database = mysql_query($query_database_command) or die("Error while sending the selection query");

/*
 * We should remove all the HTML tags from the title of the scenario.
 * If we do not, an error will happen while displaying it on the menu.
 * This search and replace removes anything that is potentially dangerous.
 * It can also, remove parts that are not HTML tags.
 */
$search = "'<[\/\!]*?[^<>]*?>'si";
$replace = "";
while ($row = mysql_fetch_row($query_connecting_database)) {    // for each scenario of the project 
    $row[1] = preg_replace($search, $replace, $row[1]);
    ?>

            mc.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=c");

            // + submenu 
            var mcs_<?= $row[0] ?> = null;
            mcs_<?= $row[0] ?> = new MTMenu();
            mcs_<?= $row[0] ?>.addItem("Sub-scenarios", "", null, "Scenarios that this scenario references");
            // + submenu 
            var mcsrc_<?= $row[0] ?> = null;
            mcsrc_<?= $row[0] ?> = new MTMenu();

    <?php
    $query_database_command = "SELECT c.id_cenario_to, cen.titulo FROM centocen c, cenario cen WHERE c.id_cenario_from = " . $row[0];
    $query_database_command = $query_database_command . " AND c.id_cenario_to = cen.id_cenario";
    $qrr_2 = mysql_query($query_database_command) or die("Error while sending the selection query");
    while ($row_2 = mysql_fetch_row($qrr_2)) {
        $row_2[1] = preg_replace($search, $replace, $row_2[1]);
        ?>

                    mcsrc_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=c&cc=<?= $row[0] ?>");

        <?php
    }
    ?>

            // - submenu 
            mcs_<?= $row[0] ?>.makeLastSubmenu(mcsrc_<?= $row[0] ?>);

            // - submenu 
            mc.makeLastSubmenu(mcs_<?= $row[0] ?>);

    <?php
}
?>

    // - submenu 
    menu.makeLastSubmenu(mc);
    menu.addItem("Lexicon");
    // + submenu 
    var ml = null;
    ml = new MTMenu();

<?php
$query_database_command = "SELECT id_lexico, nome  
                  FROM lexico  
                  WHERE id_projeto = $id_project  
                  ORDER BY nome";

$query_connecting_database = mysql_query($query_database_command) or die("Error while sending the selection query");
while ($row = mysql_fetch_row($query_connecting_database)) {   // for each lexicon of the project 
    ?>

            ml.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=l");
            // + submenu 
            var mls_<?= $row[0] ?> = null;
            mls_<?= $row[0] ?> = new MTMenu();
            // mls_<?= $row[0] ?>.addItem("L�xico", "", null, "Terms of the lexicon this term references"); 
            // + submenu 
            // var mlsrl_<?= $row[0] ?> = null; 
            // mlsrl_<?= $row[0] ?> = new MTMenu(); 

    <?php
    $query_database_command = "SELECT l.id_lexico_to, lex.nome FROM lextolex l, lexico lex WHERE l.id_lexico_from = " . $row[0];
    $query_database_command = $query_database_command . " AND l.id_lexico_to = lex.id_lexico";
    $qrr_2 = mysql_query($query_database_command) or die("Error while sending the selection query");
    while ($row_2 = mysql_fetch_row($qrr_2)) {
        ?>

                    // mlsrl_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>"); 
                    mls_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>");

        <?php
    }
    ?>

            // - submenu 
            // mls_<?= $row[0] ?>.makeLastSubmenu(mlsrl_<?= $row[0] ?>); 
            // - submenu 
            ml.makeLastSubmenu(mls_<?= $row[0] ?>);

    <?php
}
?>

    // -submenu 
    menu.makeLastSubmenu(ml);

    // ONTOLOGY 
    // + submenu 
    menu.addItem("Ontology");
    var mo = null;
    mo = new MTMenu();

    // -submenu 
    menu.makeLastSubmenu(mo);


    // CONCEPT 
    // ++ submenu 
    mo.addItem("Concepts");
    var moc = null;
    moc = new MTMenu();

<?php
$query_database_command = "SELECT id_conceito, nome  
                  FROM conceito 
                  WHERE id_projeto = $id_project  
                  ORDER BY nome";

$query_connecting_database = mysql_query($query_database_command) or die("Error while sending the selection query");
while ($row = mysql_fetch_row($query_connecting_database)) {  // for each concept of the project 
    print "moc.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=oc\");";
}
?>

    // --submenu 
    mo.makeLastSubmenu(moc);

    // RELATIONS 
    // ++ submenu 
    mo.addItem("Relations");
    var mor = null;
    mor = new MTMenu();

<?php
$query_database_command = "SELECT   id_relacao, nome 
                  FROM     relacao r 
                  WHERE    id_projeto = $id_project  
                  ORDER BY nome";

$query_connecting_database = mysql_query($query_database_command) or die("Error while sending the selection query");
while ($row = mysql_fetch_row($query_connecting_database)) {   // for each relation of the project 
    print "mor.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=or\");";
}
?>

    // --submenu    
    mo.makeLastSubmenu(mor);

    // AXIOM 
    // ++ submenu 
    mo.addItem("Axioms");
    var moa = null;
    moa = new MTMenu();

<?php
$query_database_command = "SELECT   id_axioma, axioma 
                 FROM     axioma 
                 WHERE    id_projeto = $id_project  
                 ORDER BY axioma";

$query_connecting_database = mysql_query($query_database_command) or die("Error while sending the selection query");

while ($row = mysql_fetch_row($query_connecting_database)) {  // for each axiom of the project 
    $axi = explode(" disjoint ", $row[1]);
    print "moa.addItem(\"$axi[0]\", \"main.php?id=$row[0]&t=oa\");";
}
?>

    // --submenu    
    mo.makeLastSubmenu(moa);

        </script> 
    </head> 
    <body onload="MTMStartMenu(true);" bgcolor="#000033" text="#ffffcc" link="yellow" vlink="lime" alink="red"> 
    </body> 
</html> 
