<?php
include_once '/../bd.inc';
include_once '/../CELConfig/CELConfig.inc';

session_start();


// Cen�rio - Realizar logout
// Objetivo:  Permitir ao usu�rio realizar o logout, mantendo a integridade do que foi 
//            realizado,  e retorna a tela de login	
// Contexto:  Sistema aberto. Usu�rio ter acessado ao sistema. 
//            Usu�rio deseja sair da aplica��o e manter a integridade do que foi 
//            realizado 
//Pr�-Condi��o: Usu�rio ter acessado ao sistema	
// Atores:	  Usu�rio, Sistema.	
// Recursos:  Interface	
// Epis�dios: O sistema fecha a sess�o do usu�rio, mantendo a integridade do que foi realizado 
//            O sistema retorna a interface de login, possibilitando o usu�rio se logar 
//            novamente 	

session_destroy();
session_unset();
$ip_value = CELConfig_ReadVar("HTTPD_ip");
?>

<html>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <script language="javascript1.3">


        document.writeln('<p style="color: blue; font-weight: bold; text-align: center">A aplição teminou escolha uma das opções abaixo:</p>');
        document.writeln('<p align="center"><a href="javascript:logoff();">Entrar novamente</a></p>');
        document.writeln('<p align="center"><a href="http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . "../"); ?>">Página inicial</a></p>');
        document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

        function logoff()
        {
            location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
        }


        //window.close();
        //location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
    </script>
</html>

