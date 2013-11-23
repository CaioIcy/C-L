<?php
// File: recuperaDAML.php
// Purpose: This module lists all the generated DAML files on $_SESSION['diretorio']
?>

<HTML> 
    <HEAD> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 
        <TITLE>DAML files recovery</TITLE> 
    </HEAD> 

    <BODY> 
        <H2>DAML files history</H2> 
        <?php
        include_once 'CELConfig/CELConfig.inc';

        function extract_data($file_name) {
            list($project, $rest) = split("__", $file_name);
            list($day, $month, $year, $hour, $minute, $second, $extension) = split('[_-.]', $rest);

            if (!is_numeric($day) || !is_numeric($month) || !is_numeric($year) || !is_numeric($hour) || !is_numeric($minute) || !is_numeric($second))
                return "-";

            $months_written = "-";
            switch ($month) {
                case 1: $months_written = "janeiro";
                    break;
                case 2: $months_written = "fevereiro";
                    break;
                case 3: $months_written = "mar�o";
                    break;
                case 4: $months_written = "abril";
                    break;
                case 5: $months_written = "maio";
                    break;
                case 6: $months_written = "junho";
                    break;
                case 7: $months_written = "julho";
                    break;
                case 8: $months_written = "agosto";
                    break;
                case 9: $months_written = "setembro";
                    break;
                case 10: $months_written = "outubro";
                    break;
                case 11: $months_written = "novembro";
                    break;
                case 12: $months_written = "dezembro";
                    break;
            }

            return $day . " de " . $months_written . " de " . $year . " �s " . $hour . ":" . $minute . "." . $second . "\n";
        }

        function extract_project($file_name) {
            list($project) = split("__", $file_name);
            return $project;
        }

        $directory = $_SESSION['diretorio'];
        $site = $_SESSION['site'];

        if ($directory == "") {
            $directory = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
        }

        if ($site == "") {
            $site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
            if ($site == "http:///") {
                print( "Aten��o: O arquivo de configura��o do CELConfig (padr�o: config2.conf) precisa ser configurado corratamente.<BR>\n * N�o foram preenchidas as vari�veis 'HTTPD_ip','CEL_dir_relativo' e 'DAML_dir_relativo_ao_CEL'.<BR>\nPor favor, verifique o arquivo e tente novamente.<BR>\n");
            }
        }

        // Assembles the table with DAML files
        print( "<CENTER><TABLE WIDTH=\"80%\">\n");
        print( "<TR>\n\t<Th><STRONG>Projeto</STRONG></Th>\n\t<Th><STRONG>Gerado em</STRONG></Th>\n</TR>\n");
        if ($directory_handle = @opendir($directory)) {
            while (( $file = readdir($directory_handle) ) !== false) {
                if (is_file($directory . "/" . $file) && $file != "." && $file != "..") {
                    print( "<TR>\n");
                    print( "\t<TD WIDTH=\"25%\" CLASS=\"Estilo\"><B>" . extract_project($file) . "</B></TD>\n");
                    print( "\t<TD WIDTH=\"55%\" CLASS=\"Estilo\">" . extract_data($file) . "</TD>\n");
                    print( "\t<TD WIDTH=\"10%\" >[<A HREF=\"" . $site . $file . "\">Abrir</A>]</TD>\n");
                    print( "</TR>\n");
                }
            }
            closedir($directory_handle);
        }
        print("</TABLE></CENTER>\n");
        ?> 
    </BODY> 
</HTML> 