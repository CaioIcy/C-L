<?php

session_start();

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';

chechUserAuthentication("index.php");

$database_recuperation = database_connect() or die("Error while connecting to the database.");

/*
 * Scenario - Generate XML logs
 * Objective:   Allow the administrator to generate logs in XML format from a project,
 *              that is identified by date.
 * Context:     Manager wishes to generate a log from one of the project he is an
 *              administrator of.
 *              Pre-condition: Logged in, project is registered
 * Actors:      Administrator
 * Resources:   System, log data, data from registered project, database
 * Episodes:    1- From the data of the regisstered project, the system supplies to
 *                  the administrator a screen to visualize the created XML
 */

$query = "select * from publicacao where id_projeto = $id_project AND versao = $version";
$query_connecting_database = mysql_query($query) or die("Error while sending query.");
$row = mysql_fetch_row($query_connecting_database);
$xml_base = $row[3];

echo $xml_base;
?>
