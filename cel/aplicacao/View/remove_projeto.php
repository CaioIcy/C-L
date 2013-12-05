<?php
session_start();

include_once '../funcoes_genericas.php';
include_once '../httprequest.inc';

//Cen�rio  -  Remover Projeto 
//Objetivo:	   Permitir ao Administrador do projeto remover um projeto
//Contexto:	   Um Administrador de projeto deseja remover um determinado projeto da base de dados
//Pr�-Condi��o:    Login, Ser administrador do projeto selecionado.  
//Atores:	   Administrador
//Recursos:	   Sistema, dados do projeto, base de dados
//Epis�dios:       O Administrador clica na op��o �remover projeto� encontrada no menu superior.
//                 O sistema disponibiliza uma tela para o administrador ter certeza de que esta removendo o projeto correto
//                 O Administrador clica no link de remo��o.
//                 O sistema chama a p�gina que remover� o projeto do banco de dados.
?>
<html>
    <head>
        <title>Remover Projeto</title>
    </head>
<?php
$id_project = $_SESSION['id_projeto_corrente'];
$id_user = $_SESSION['id_usuario_corrente'];

$database_conection = database_connect() or die("Erro ao conectar ao SGBD");
$qv = "SELECT * FROM projeto WHERE id_projeto = '$id_project' ";
$qvr = mysql_query($qv) or die("Erro ao enviar a query de select no projeto");
$result_array_projecto = mysql_fetch_array($qvr);
$project_name = $result_array_projecto[1];
$data_project = $result_array_projecto[2];
$project_description = $result_array_projecto[3];
?>    
    <body>
        <h4>Remover Projeto:</h4>

        <p><br>
        </p>
        <table width="100%" border="0">
            <tr> 
                <td width="29%"><b>Nome do Projeto:</b></td>
                <td width="29%"><b>Data de cria&ccedil;&atilde;o</b></td>
                <td width="42%"><b>Descri&ccedil;&atilde;o</b></td>
            </tr>
            <tr> 
                <td width="29%"><?php echo $project_name; ?></td>
                <td width="29%"><?php echo $data_project; ?></td>
                <td width="42%"><?php echo $project_description; ?></td>
            </tr>
        </table>
        <br><br>
    <center><b>Cuidado!O projeto ser� apagado para todos seus usu�rios!</b></center>
    <p><br>
    <center><a href="remove_projeto_base.php">Apagar o projeto</a></center> 
</p>
<p>
    <i><a href="showSource.php?file=remove_projeto.php">Veja o c�digo fonte!</a></i> 
</p>
</body>
</html>

