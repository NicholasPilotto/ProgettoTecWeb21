<?php
class graphics {
    public static function getPage($nome) {
        $headerHTML = file_get_contents("header.html");

        $linkUtente = "";
        $helloUser = "";

        require_once "cart.php";
        $c;

        if (isset($_SESSION["Nome"])) {
            $helloUser = "<p id='benvenuto'>Benvenuto, " . $_SESSION["Nome"] . "</p>";
            //     $linkUtente .= "<a class='linkUtente' href='account.php'>Account</a>";

            $linkUtente .= '<li class="nav-item"><a class="nav-link" href="account.php">Area riservata</a></li>
            ';
            $linkUtente .= '<li class="nav-item"><abbr class="notification" title="Carrello *q* elementi"><a class="linkUtente" href="carrello.php">Carrello';
            if (isset($_SESSION["cart"])) {
                $c = cart::build_cart_from_session();
                $linkUtente .= '<abbr aria-hidden="true" class="badge">' . $c->get_quantity() . '</abbr>';
            }
            $linkUtente = str_replace("*q*", isset($c) ? $c->get_quantity() : "", $linkUtente);
            $linkUtente .= '</a></abbr></li>
            ';
            $linkUtente .= '<li class="nav-item"><a class="linkUtente" href="esci.php">Esci</a></li>';
        } else {
            $linkUtente = '<li class="nav-item"><a class="nav-link" href="accedi.php">Area riservata</a></li>';
            //     $linkUtente .= "<a class='linkUtente' href='accedi.php'>Accedi</a>";
            //     $linkUtente .= "<a class='linkUtente' href='registrati.php'>Registrati</a>";
            //     $linkUtente .= "<a class='linkUtente' href='carrello.php'>Carrello</a>";
        }


        // $headerHTML = str_replace("</linkUtente>", $linkUtente, $headerHTML);
        $linkHTML = file_get_contents("link.html");
        $paginaHTML = file_get_contents($nome);
        $footerHTML = file_get_contents("footer.html");
        $thisYear = date("Y");
        $footerHTML = str_replace("</currentDate>", $thisYear, $footerHTML);

        $navBar = file_get_contents("navBarSito.html");
        $navBar = str_replace("</areaRiservata>", $linkUtente, $navBar);

        $paginaHTML = str_replace("</navBarSito>", $navBar, $paginaHTML);
        $paginaHTML = str_replace("</helloUser>", $helloUser, $paginaHTML);


        $upButton = file_get_contents("upButton.html");

        $paginaHTML = str_replace("</upButton>", $upButton, $paginaHTML);

        $paginaHTML = str_replace("</headerSito>", $headerHTML, $paginaHTML);
        $paginaHTML = str_replace("</linkSito>", $linkHTML, $paginaHTML);
        return str_replace("</footerSito>", $footerHTML, $paginaHTML);
    }
}
