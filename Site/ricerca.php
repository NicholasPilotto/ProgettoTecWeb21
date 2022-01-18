<?php
    require_once "graphics.php";
    
    $paginaHTML = graphics::getPage("ricerca_php.html");

    // Stampa filtri GENERE

    $generi = array(
        "genere10" => "Storia e Biografie",
        "genere11" => "Fumetti e Manga",
        "genere12" => "Classici e Romanzi",
        "genere13" => "Avventura e Azione",
        "genere14" => "Scuole e Universit&aacute",
        "genere15" => "Arte e Tempo Libero",

        "genere16" => "Filosofia e Psicologia",
        "genere17" => "Scienza e Fantascienza",
        "genere18" => "Economia e Business",
        "genere19" => "Dizionari ed Enciclopedie",
        "genere20" => "Medicina e Salute",
        "genere21" => "Bambini e Ragazzi",
    );

    $listaFiltriGenere =  "<ul>";

    foreach($generi as $key => $value)
    {
        $listaFiltriGenere .= "<li>";

        $listaFiltriGenere .= "<input type='checkbox' id='" . $key . "' name='" . $key . "' ";

        if(isset($_GET[$key]) && $_GET[$key] == "on")
        {
            $listaFiltriGenere .= "checked";
        }

        $listaFiltriGenere .= "/>";

        $listaFiltriGenere .= "<label for=" . $key . ">" . $value . "</label>";

        $listaFiltriGenere .= "</li>";
    }

    $listaFiltriGenere .= "</ul>";

    $paginaHTML = str_replace("</listaFiltriGenere>", $listaFiltriGenere, $paginaHTML);

    /*
        <ul>
            <li>
                <input type="checkbox" id="genere1" name="genere1" </valoreGenere1> />
                <label for="genere1">Genere1</label>
            </li>
            <li>
                <input type="checkbox" id="genere2" name="genere2" valoreGenere2/>
                <label for="genere2">Genere2</label>
            </li>
            <li>
                <input type="checkbox" id="genere3" name="genere3" valoreGenere3/>
                <label for="genere3">Genere3</label>
            </li>
        </ul>
    */

    /*
    $genere1 = $_GET['genere1'];
    $genere2 = $_GET['genere2'];
    $genere3 = $_GET['genere3'];*/
    /*
    $genere1 = $genere2 = $genere3 = "";
    if(isset($_GET['genere1'])) $genere1 = "checked";
    if(isset($_GET['genere2'])) $genere2 = "checked";
    if(isset($_GET['genere3'])) $genere3 = "checked";

    $paginaHTML = str_replace("valoreGenere1", $genere1, $paginaHTML);
    $paginaHTML = str_replace("valoreGenere2", $genere2, $paginaHTML);
    $paginaHTML = str_replace("valoreGenere3", $genere3, $paginaHTML);*/

    // Accesso al database

    // -------------------

    echo $paginaHTML;
?>