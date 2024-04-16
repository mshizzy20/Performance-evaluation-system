<?php
    // Define a function for escaping special characters in a string
    function escapeCharacters($string)
    {
        // Use htmlspecialchars() function to convert special characters to HTML entities
        // ENT_QUOTES:
        //   Convert both double and single quotes to their respective HTML entities
        // 'UTF-8':
        //   Specify the character encoding as UTF-8
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
?>