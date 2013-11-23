<?php

// Escapes the PHP metacharacters from the desired string($string)
function escape_metacharacters($string) {
    $string = ereg_replace("[][{}()*+?.\\^$|]", "\\\\0", $string);
    return $string;
}

// Makes sure a string($string) is safe
function safely_prepare_data($string) {

    // Replaces & by amp; (avoiding XML trouble)
    $string = ereg_replace("&", "&amp;", $string);

    // Removes html and php tags
    $string = strip_tags($string);
    
    //Checks if the get_magic_quotes_gpc() directive is activated, if so, use stripslashes function on string
    $string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
    
    $string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
    
    return $string;
}
?>
