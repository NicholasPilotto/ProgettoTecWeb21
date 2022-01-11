<?php
class graphics {
    public static function getPage($nome, $root = false) {
        $aux = $root ? "frontend/" : "";
        $headerHTML = file_get_contents($root . "header.html");
        $paginaHTML = file_get_contents($nome);
        $footerHTML = file_get_contents($root . "footer.html");

        $paginaHTML = str_replace("<headerSito/>", $headerHTML, $paginaHTML);
        return str_replace("<footerSito/>", $footerHTML, $paginaHTML);
    }
}
