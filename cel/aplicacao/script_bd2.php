<html>

    <head>
        <title></title>
    </head>

    <body>

        <?php

        include_once 'bd.inc';
        include_once 'database_support.php';

        function convert_impacts() {
            $link = database_connect() or die("Database connection error: " . mysql_error() . __LINE__);

            $filename = "teste.txt";

            $query = "select * from lexico;";
            $result = mysql_query($query) or die("Database query failed: " . mysql_error() . __LINE__);

            if (!$handle = fopen($filename, 'w')) {
                print "Could not open ($filename) file";
                exit;
            }

            // Its important to write to teste.txt to separate impacts that are in a same impact

            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $id_lexico = $line['id_lexico'];
                $impacto = $line['impacto'];

                if (!fwrite($handle, "@\r\n$id_lexico\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }

                if (!fwrite($handle, "$impacto\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }
            }

            fclose($handle);

            mysql_query("delete from impacto;");

            $lines = file($filename);

            $pegar_id = "FALSE";
            $id_lexico = 0;

            foreach ($lines as $line_num => $line) {
                if ($line[0] == '@') {
                    $pegar_id = 1;
                    continue;
                }
                if ($pegar_id) {
                    $id = scanf($line, "%d");
                    $id_lexico = $id[0];
                    $pegar_id = 0;
                    continue;
                }

                print ($line . "<br>\n");
                if (strcmp(trim($line), "") != 0) {
                    $query = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$line');";
                    $result = mysql_query($query) or die("Database query failed: " . mysql_error() . " " . $line . " " . $id_lexico . " " . __LINE__);
                }
            }

            $query = "select * from impacto order by id_lexico;";
            $result = mysql_query($query) or die("Database query failed: " . mysql_error() . __LINE__);
            $result2 = mysql_num_rows($result);

            mysql_close($link);
        }
        ?>

    </body>

</html>
