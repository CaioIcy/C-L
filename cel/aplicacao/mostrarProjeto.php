<?php

include_once 'funcoes_genericas.php';
include_once 'httprequest.inc';

/*
 * Scenario - Choose project
 * Objective:   Allow the user/administrator to choose a project.
 * Context:     The user/administrator wisehs to choose a project.
 *              Pre-conditions: logged in, be registered as an administrator
 * Actors:      Administrator, user
 * Resources:   Registered users
 * Episodes:    1- User selects a project from the project list
 *              2- If the user selected a project that he administrates, see
 *                  ADMINISTRATOR CHOOSES PROJECT
 *              3- Else, see USER CHOOSES PROJECT
 */
$database_recuperation = database_connect() or die("Error while connecting to the database.");

$query = "select * from publicacao where id_projeto = $id_project AND versao = $version";
$query_connecting_database = mysql_query($query) or die("Error while sending query.");
$row = mysql_fetch_row($query_connecting_database);
$xml_base = $row[3];

echo $xml_base;
?>
