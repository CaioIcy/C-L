<?php

session_start();

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';

chechUserAuthentication("index.php");        // Checa se o usuario foi autenticado

$database_recuperation = database_connect() or die("Erro ao conectar ao SGBD");

//Cen�rio -  Gerar Relat�rios XML 
//Objetivo:	Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//              identificados por data.
//Contexto:     Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//Pr�-Condi��o: Login, projeto cadastrado.
//Atores:	Administrador
//Recursos:	Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.
//Epis�dios:    Gerando com sucesso o relat�rio a partir dos dados cadastrados do projeto,
//              o sistema fornece ao administrador a tela de visualiza��o do relat�rio
//              XML criado

$query = "select * from publicacao where id_projeto = $id_project AND versao = $version";
$query_connecting_database = mysql_query($query) or die("Erro ao enviar a query");
$row = mysql_fetch_row($query_connecting_database);
$xml_base = $row[3];

echo $xml_base;
?>
