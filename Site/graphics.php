<?php
    class graphics 
    {
        public static function getPage($nome)
        {
            $headerHTML = file_get_contents("header.html");
            $linkHTML = file_get_contents("link.html");
            $paginaHTML = file_get_contents($nome);
            $footerHTML = file_get_contents("footer.html");

            $paginaHTML = str_replace("</headerSito>", $headerHTML, $paginaHTML);
            $paginaHTML = str_replace("</linkSito>", $linkHTML, $paginaHTML);
            return str_replace("</footerSito>", $footerHTML, $paginaHTML);
        }
    }
?>