<?php
session_start();
if (!isset($_SESSION["Nome"])) {
    header("Location: index.php");
} else {
    require_once "graphics.php";

    $paginaHTML = graphics::getPage("account_php.html");

    unset($_SESSION['paginaPrecedenteModificaLibro']);

    $codiceIdentificativo = $_SESSION["Codice_identificativo"];
    $codiceIdentificativo = hash('sha256', $codiceIdentificativo);
    $liAccount = "";

    if ($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957") {
        unset($_SESSION["editFlag"]);
        // admin
        $liAccount = "
                <li>
                    <i class='fa-li fa fa-plus-circle fa-3x'></i>
                    <h3><a href='aggiungiLibro.php'>Aggiungi libro</a></h3>
                    <p>Aggiungi un nuovo libro al database</p>
                </li>
                <li>
                    <i class='fa-li fas fa-chart-bar fa-3x'></i>
                    <h3 lang='en'><a href='analytics.php'>Analytics</a></h3>
                    <p>Visualizza le <span lang='en'>analytics</span> di SecondRead</p>
                </li>
                <li>
                    <i class='fa-li fas fa-list-alt fa-3x'></i>
                    <h3 lang='en'><a href='ordiniPendenti.php'>Ordini pendenti</a></h3>
                    <p>Visualizza gli ordini pendenti di SecondRead</p>
                </li>
                <li>
                    <i class='fa-li fas fa-list fa-3x'></i>
                    <h3 lang='en'><a href='ordiniEffettuati.php'>Ordini effettuati</a></h3>
                    <p>Visualizza gli ordini effettuati di SecondRead</p>
                </li>
            ";
    } else {
        // user normale
        $liAccount = "
                <li>
                    <i class='fa-li far fa-list-alt fa-3x'></i>
                    <h3><a href='ordini.php'>Ordini</a></h3>
                    <p>Visualizza ordini effettuati</p>
                </li>
                <li>
                    <i class='fa-li far fa-user fa-3x'></i>
                    <h3><a href='datilogin.php'>Dati Login</a></h3>
                    <p>Visualizza o modifica dati personali</p>
                </li>
                
                <li>
                    <i class='fa-li far far fa-address-card fa-3x'></i>
                    <h3><a href='indirizzi.php'>Indirizzi</a></h3>
                    <p>Gestisci gli indirizzi relativi al tuo account</p>
                </li>
                <li>
                    <i class='fa-li far far fa-heart fa-3x'></i>
                    <h3 lang='en'><a href='wishlist.php'>Wishlist</a></h3>
                    <p>Visualizza gli articoli nella tua wishlist</p>
                </li>
                <li>
                    <i class='fa-li far fa-comment-dots fa-3x'></i>
                    <h3><a href='recensioni.php'>Recensioni</a></h3>
                    <p>Visualizza, modifica o cancella le recensioni lasciate ai libri</p>
                </li>
            ";
    }

    // Accesso al database

    // -------------------

    $paginaHTML = str_replace("</liAccount>", $liAccount, $paginaHTML);
    $paginaHTML = str_replace('<li class="nav-item"><a class="nav-link" href="account.php">Area riservata</a></li>', '<li class="nav-item selectedNavItem">Area riservata</li>', $paginaHTML);
    echo $paginaHTML;
}
