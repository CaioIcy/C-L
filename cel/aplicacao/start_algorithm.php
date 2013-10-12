<?php
	//@session_destroy();
	//session_unset();
	session_start();

	include_once 'bd.inc';
	include_once 'database_support.php';
	include_once 'script_bd2.php' ;

	//mysql_close($link);
        $database_conection = database_connect();

	$lexicon_array = is_typeDefined();

	if( is_array($lexicon_array))
	{
		foreach( $lexicon_array as $id_lexicon )
		{
			$lexicon_unit = obter_lexico($id_lexicon);
			$lexicon_newArray[] = $lexicon_unit["nome"];
		}

		$_SESSION["lista"] = $lexicon_newArray;
		$_SESSION["job"] = "type";
		$_SESSION["nome1"] = 1;
		?>
		<script language="javascript">
			window.location = "auxiliar_interface.php";
		</script>
		<?php

		exit();
	}

	$_SESSION["lista_de_sujeito"] = get_subjectList();
	$_SESSION["lista_de_objeto" ] = get_objectList();
	$_SESSION["lista_de_verbo"  ] = get_verbList();
	$_SESSION["lista_de_estado" ] = get_stateList();

	/*print_r($_SESSION["lista_de_sujeito"]);
	print_r($_SESSION["lista_de_objeto"]);
	print_r($_SESSION["lista_de_verbo"]);
	print_r($_SESSION["lista_de_estado"]);*/

	$_SESSION["salvar"] = "FALSE";


	if( $_POST["load"] == "FALSE" )
	{
		converte_impactos();
		$_SESSION["lista_de_conceitos"] = array();
		$_SESSION["lista_de_relacoes" ] = array();
		$_SESSION["lista_de_axiomas"  ] = array();

		$_SESSION["funcao"] = "sujeito_objeto";

		$_SESSION["index1"] = 0;
		$_SESSION["index2"] = 0;
		$_SESSION["index3"] = 0;
		$_SESSION["index4"] = 0;
		$_SESSION["index5"] = 0;
		$_SESSION["index6"] = 0;
		$_SESSION["index7"] = 0;

	}
	else
	{
		$_SESSION["lista_de_relacoes"]  = get_relationList();
		$_SESSION["lista_de_conceitos"] = get_conceptList();
		$_SESSION["lista_de_axiomas"]   = get_lista_de_axiomas();

		$_SESSION["funcao"] = get_funcao();



		$indices = get_indices();
		if(count($indices) == 5)
		{
		$_SESSION["index1"] = $indices['index1']; //Sujeito
		$_SESSION["index3"] = $indices['index3']; //Verbo
		$_SESSION["index4"] = $indices['index4']; //Estado
		$_SESSION["index5"] = $indices['index5']; //Organizacao
		}
		else
		{
			$_SESSION["index1"] = 0; //Sujeito
			$_SESSION["index3"] = 0; //Verbo
			$_SESSION["index4"] = 0; //Estado
			$_SESSION["index5"] = 0; //Organizacao
		}
		$_SESSION["index2"] = 0;
		$_SESSION["index6"] = 0;
		$_SESSION["index7"] = 0;

	}

	mysql_close($database_conection);


?>

<html>
  <head>
    <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
    <style>

    </style>
  </head>
<body>

	<script language="javascript">
			window.location = "algoritmo.php";
	</script>

</body>
</html>