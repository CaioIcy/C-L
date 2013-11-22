<?php

include_once 'auxiliar_bd.php';
include_once 'bd.inc';

session_start();


$database_conection = database_connect();
?>    

<html> 
    <head> 


        <script language="javascript" src="/Shared/mtmcode.js">
        </script> 


        <script language="javascript">
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
            MTMenuText = "Ontologia";


            /****************************************************************************** 
             * Functions                                                                      * 
             ******************************************************************************/
            function MTMenu() {
                this.items = new Array();
                this.MTMAddItem = MTMAddItem;
                this.addItem = MTMAddItem;
                this.makeLastSubmenu = MTMakeLastSubmenu;
            }
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

<?php
//Arvore de Conceitos 

if (isset($_SESSION['lista_de_conceitos']))
    $concept_list = $_SESSION['lista_de_conceitos'];
else
    $concept_list = array();
//$arv = get_lista_de_conceitos();   


/*    N�veis da arvore 
  conceito
  Verbo
  Predicado
 */

/*
  echo "menu.addItem(\"Teste\");\n";
  echo "menu.addItem(\"Teste2\");\n";

 */


// Conceitos 
foreach ($concept_list as $concept) {
    echo "\nmenu.addItem(\"$concept->nome\");\n";
    echo " var mC = null;\n";
    echo " mC = new MTMenu();\n";
    echo "menu.makeLastSubmenu(mC);\n";

    //Rela��es 
    //Verbos 
    foreach ($concept->relacoes as $relationship) {
        echo " mC.addItem(\"$relationship->verbo\",\"\");\n";
        echo " var mV = new MTMenu();\n";

        //Predicados 
        foreach ($relationship->predicados as $predicate) {
            echo " mV.addItem(\"$predicate\",\"blank.html\",\"enganaarvore\");\n";
        }

        echo " mC.makeLastSubmenu(mV);\n";
    }
}


mysql_close($database_conection);
?>

        </script>


    </head>
    <body onload="MTMStartMenu(true);" bgcolor="#FFFFFF" text="#ffffcc" link="yellow" vlink="lime" alink="red">

        <?php
        print "<font color=black>";
        print_r($concept_list);
        print "</font>";
        ?>
    </body>
</html>