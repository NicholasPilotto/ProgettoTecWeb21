<?php
class graphics {
    public static function getPage($nome) {
        $headerHTML = file_get_contents("header.html");

        $linkUtente = "";

        if (isset($_SESSION["Nome"])) {
            $linkUtente .= "<p id='benvenuto'>Benvenuto, " . $_SESSION["Nome"] . "</p>";
            $linkUtente .= "<a class='linkUtente' href='account.php'>Account</a>";
            $linkUtente .= "<a class='linkUtente' href=''>Carrello</a>";
            $linkUtente .= "<a class='linkUtente' href='esci.php'>Esci</a>";
        } else {
            $linkUtente .= "<a class='linkUtente' href='accedi.php'>Accedi</a>";
            $linkUtente .= "<a class='linkUtente' href='registrati.php'>Registrati</a>";
            $linkUtente .= "<a class='linkUtente' href=''>Carrello</a>";
        }
        $headerHTML = str_replace("</linkUtente>", $linkUtente, $headerHTML);

        $linkHTML = file_get_contents("link.html");
        $paginaHTML = file_get_contents($nome);
        $footerHTML = file_get_contents("footer.html");
        $thisYear = date("Y");
        $footerHTML = str_replace("</currentDate>", $thisYear, $footerHTML);

        $upButton = file_get_contents("upButton.html");

        $paginaHTML = str_replace("</upButton>", $upButton, $paginaHTML);

        $paginaHTML = str_replace("</headerSito>", $headerHTML, $paginaHTML);
        $paginaHTML = str_replace("</linkSito>", $linkHTML, $paginaHTML);
        return str_replace("</footerSito>", $footerHTML, $paginaHTML);
    }
}
