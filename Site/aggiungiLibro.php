<?php
    function OrderWithoutTags($array, $attr)
    {
        $newArray = array();
        foreach($array as $elem)
        {
            $elem[$attr] = strip_tags($elem[$attr]);
            array_push($newArray, $elem);
        }
        $column = array_column($newArray, $attr);
        array_multisort($column, SORT_ASC, $newArray);
        return $newArray;
    }

    session_start();
    use DB\Service;

    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        require_once('backend/db.php');
        require_once "graphics.php";
        
        $paginaHTML = graphics::getPage("aggiungiLibro_php.html");

        $codiceIdentificativo = $_SESSION["Codice_identificativo"];
        $codiceIdentificativo = hash('sha256', $codiceIdentificativo);
        $liAccount = "";

        if($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957")
        {
            // admin
            $connessione = new Service();
            $a = $connessione->openConnection();

            $queryAutori = $connessione->get_all_authors();
            $selectAutori = "<select class='styleMultipleSelect' id='autore' name='autore' multiple required>";
            if($queryAutori->ok())
            {
                foreach(OrderWithoutTags($queryAutori->get_result(), "nome") as $autore)
                {
                    $cognome = ($autore['cognome'] != "-") ? $autore['cognome'] : "";
                    $selectAutori .= "<option value='" . $autore['id'] . "'>" . $autore['nome'] . " " . $cognome . "</option>";
                }
            }
            $selectAutori .= "</select>";

            $queryEditori = $connessione->get_all_editors();
            $selectEditori = "<select class='styleSelect' id='editore' name='editore' required>";
            if($queryEditori->ok())
            {
                foreach(OrderWithoutTags($queryEditori->get_result(), "nome") as $editore)
                {
                    $selectEditori .= "<option value='" . $editore['id'] . "'>" . $editore['nome'] . "</option>";
                }
            }
            $selectEditori .= "</select>";

            // replace
            $paginaHTML = str_replace("</selectAutori>", $selectAutori, $paginaHTML);
            $paginaHTML = str_replace("</selectEditori>", $selectEditori, $paginaHTML);
            // -------

            // controllo se l'admin viene da "aggiungi libro" o da "modifica libro"
            if (isset($_GET['isbn']))
            {
                // modifica libro
                $isbn = $_GET['isbn'];  
                $queryIsbn = $connessione->get_book_by_isbn($isbn);

                if ($queryIsbn->ok() && !$queryIsbn->is_empty())
                {
                    $libro = $queryIsbn->get_result();

                    /*
                    $dom = new DOMDocument;
                    $dom->loadHTML($paginaHTML);
                    
                    $node = $dom->getElementById("isbn");
                    $node->setAttribute("value", "123");
                    echo $dom->saveHTML();
                    */
                }
            }

            echo $paginaHTML;
        }
        else
        {
            // user normale
            header("Location: index.php");
        }
    }
?>